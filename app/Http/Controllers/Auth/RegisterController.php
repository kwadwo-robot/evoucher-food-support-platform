<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OrganisationProfile;
use App\Models\RecipientProfile;
use App\Models\ShopProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $role = $request->get('role', 'recipient');
        $validRoles = ['recipient', 'local_shop', 'vcfse', 'school_care'];
        if (!in_array($role, $validRoles)) $role = 'recipient';
        return view('auth.register', compact('role'));
    }

    public function register(Request $request)
    {
        $role = $request->input('role', 'recipient');
        $validRoles = ['recipient', 'local_shop', 'vcfse', 'school_care'];
        if (!in_array($role, $validRoles)) $role = 'recipient';

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => ['required', 'in:recipient,local_shop,vcfse,school_care'],
        ];

        if ($role === 'recipient') {
            $rules += [
                'phone'   => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string'],
            ];
        } elseif ($role === 'local_shop') {
            $rules += [
                'shop_name'    => ['required', 'string', 'max:200'],
                'shop_address' => ['required', 'string'],
                'phone'        => ['nullable', 'string', 'max:20'],
            ];
        } elseif ($role === 'vcfse') {
            $rules += [
                'org_name'       => ['required', 'string', 'max:200'],
                'contact_name'   => ['nullable', 'string', 'max:150'],
                'phone'          => ['nullable', 'string', 'max:20'],
                'charity_number' => ['nullable', 'string', 'max:50'],
            ];
        } else { // school_care
            $rules += [
                'school_org_name'     => ['required', 'string', 'max:200'],
                'org_type'            => ['nullable', 'string', 'max:50'],
                'school_contact_name' => ['nullable', 'string', 'max:150'],
                'phone'               => ['nullable', 'string', 'max:20'],
            ];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => $role,
            'is_approved' => in_array($role, ['vcfse', 'school_care', 'local_shop']) ? false : true,
            'is_active'   => true,
        ]);

        if ($role === 'recipient') {
            RecipientProfile::create([
                'user_id'    => $user->id,
                'first_name' => $validated['name'],
                'last_name'  => '',
                'phone'      => $validated['phone'] ?? null,
                'address'    => $validated['address'] ?? null,
            ]);
        } elseif ($role === 'local_shop') {
            ShopProfile::create([
                'user_id'   => $user->id,
                'shop_name' => $validated['shop_name'],
                'address'   => $validated['shop_address'],
                'phone'     => $validated['phone'] ?? null,
            ]);
        } elseif ($role === 'vcfse') {
            OrganisationProfile::create([
                'user_id'        => $user->id,
                'org_name'       => $validated['org_name'],
                'org_type'       => 'vcfse',
                'contact_person' => $validated['contact_name'] ?? null,
                'charity_number' => $validated['charity_number'] ?? null,
                'phone'          => $validated['phone'] ?? null,
            ]);
        } else { // school_care
            OrganisationProfile::create([
                'user_id'        => $user->id,
                'org_name'       => $validated['school_org_name'],
                'org_type'       => $validated['org_type'] ?? 'school',
                'contact_person' => $validated['school_contact_name'] ?? null,
                'charity_number' => null,
                'phone'          => $validated['phone'] ?? null,
            ]);
        }

        if ($role === 'recipient') {
            Auth::login($user);
            return redirect()->route('recipient.dashboard')
                ->with('success', 'Welcome! Your account has been created.');
        }

        return redirect()->route('login')
            ->with('success', 'Your account has been submitted for approval. You will be notified once approved.');
    }
}
