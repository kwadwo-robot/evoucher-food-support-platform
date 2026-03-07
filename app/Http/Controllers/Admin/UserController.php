<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['recipientProfile','shopProfile','organisationProfile']);
        if ($request->role) $query->where('role', $request->role);
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
        return back()->with('success', $user->name . ' has been approved.');
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

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,super_admin,recipient,vcfse,local_shop,school_care']);
        $user->update(['role' => $request->role]);
        return back()->with('success', 'User role updated.');
    }
}
