<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ChangePasswordController extends Controller
{
    /**
     * Show the change password form.
     */
    public function show()
    {
        return view('auth.change-password');
    }

    /**
     * Handle the change password request.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password you entered is incorrect.'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Send security notification email
        try {
            Mail::to($user->email)->send(new PasswordChangedMail($user));
        } catch (\Exception $e) {
            \Log::warning('Password changed email failed for ' . $user->email . ': ' . $e->getMessage());
        }

        return back()->with('success', 'Your password has been updated successfully. A confirmation email has been sent to ' . $user->email . '.');
    }
}
