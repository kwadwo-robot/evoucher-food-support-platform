<?php

namespace App\Http\Controllers\Recipient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the recipient's profile form.
     */
    public function edit(Request $request)
    {
        $user = Auth::user();
        return view('recipient.profile', [
            'profile' => $user,
            'user' => $user,
        ]);
    }

    /**
     * Update the recipient's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'postcode' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('recipient.profile.edit')->with('success', 'Profile updated successfully!');
    }
}
