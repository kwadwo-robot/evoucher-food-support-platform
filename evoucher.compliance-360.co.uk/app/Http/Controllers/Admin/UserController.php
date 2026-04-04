<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Mail\AccountApprovedMail;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['recipientProfile','shopProfile','organisationProfile']);
        
        // Handle special filter for 'donor' role (vcfse and school_care organizations)
        if ($request->role) {
            if ($request->role === 'donor') {
                $query->whereIn('role', ['vcfse', 'school_care']);
            } else {
                $query->where('role', $request->role);
            }
        }
        
        if ($request->status === 'pending') $query->where('is_approved', false);
        if ($request->search) $query->where(function($q) use ($request) {
            $q->where('name','like','%'.$request->search.'%')
              ->orWhere('email','like','%'.$request->search.'%');
        });
        $users = $query->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['recipientProfile','shopProfile','organisationProfile','vouchers','donations','redemptions']);
        return view('admin.users.show', compact('user'));
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);

        // Send account approved email
        try {
            Mail::to($user->email)->send(new AccountApprovedMail($user));
        } catch (\Exception $e) {
            \Log::warning('Account approved email failed for ' . $user->email . ': ' . $e->getMessage());
        }

        return back()->with('success', $user->name . ' has been approved. An email notification has been sent.');
    }

    public function reject(User $user)
    {
        $user->update(['is_approved' => false, 'is_active' => false]);
        return back()->with('success', $user->name . ' has been rejected.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', $user->name . ' has been ' . $status . '.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        // Update recipient profile if it exists
        if ($user->recipientProfile) {
            $user->recipientProfile->update($request->only(['address', 'postcode']));
        }

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|string|min:8|confirmed']);

        $user->update(['password' => bcrypt($request->password)]);

        // Send password reset email
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $request->password));
        } catch (\Exception $e) {
            \Log::warning('Password reset email failed for ' . $user->email . ': ' . $e->getMessage());
        }

        return back()->with('success', 'Password reset successfully for ' . $user->name . '. An email with the new password has been sent.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,super_admin,recipient,vcfse,local_shop,school_care']);
        $user->update(['role' => $request->role]);
        return back()->with('success', 'User role updated.');
    }
}
