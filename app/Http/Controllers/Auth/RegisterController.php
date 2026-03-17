<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\OrganisationProfile;
use App\Models\RecipientProfile;
use App\Models\ShopProfile;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'phone'    => ['nullable', 'string', 'max:20'],
        ];

        if ($role === 'recipient') {
            $rules += [
                'address'  => ['nullable', 'string'],
                'postcode' => ['nullable', 'string', 'max:10'],
            ];
        } elseif ($role === 'local_shop') {
            $rules += [
                'shop_name'        => ['required', 'string', 'max:200'],
                'shop_category'    => ['required', 'string', 'max:100'],
                'shop_address'     => ['required', 'string'],
                'shop_town'        => ['required', 'string', 'max:100'],
                'shop_postcode'    => ['required', 'string', 'max:10'],
                'opening_hours'    => ['nullable', 'string', 'max:255'],
                'shop_description' => ['nullable', 'string'],
            ];
        } elseif ($role === 'vcfse') {
            $rules += [
                'org_name'       => ['required', 'string', 'max:200'],
                'contact_name'   => ['required', 'string', 'max:150'],
                'charity_number' => ['nullable', 'string', 'max:50'],
                'org_address'    => ['required', 'string'],
                'org_town'       => ['required', 'string', 'max:100'],
                'org_postcode'   => ['required', 'string', 'max:10'],
                'org_website'    => ['nullable', 'url', 'max:255'],
            ];
        } else { // school_care
            $rules += [
                'school_org_name'     => ['required', 'string', 'max:200'],
                'org_type'            => ['required', 'string', 'max:50'],
                'school_contact_name' => ['required', 'string', 'max:150'],
                'school_address'      => ['required', 'string'],
                'school_town'         => ['required', 'string', 'max:100'],
                'school_postcode'     => ['required', 'string', 'max:10'],
                'school_website'      => ['nullable', 'url', 'max:255'],
                'school_reg_number'   => ['nullable', 'string', 'max:100'],
            ];
        }

        $validated = $request->validate($rules);

        // ALL roles are automatically approved and active on signup — no manual admin approval needed
        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => $role,
            'is_approved' => true,
            'is_active'   => true,
        ]);

        // Create role-specific profile
        if ($role === 'recipient') {
            RecipientProfile::create([
                'user_id'    => $user->id,
                'first_name' => $validated['name'],
                'last_name'  => '',
                'phone'      => $validated['phone'] ?? null,
                'address'    => $validated['address'] ?? null,
                'postcode'   => $validated['postcode'] ?? null,
            ]);
        } elseif ($role === 'local_shop') {
            ShopProfile::create([
                'user_id'       => $user->id,
                'shop_name'     => $validated['shop_name'],
                'category'      => $validated['shop_category'],
                'address'       => $validated['shop_address'],
                'town'          => $validated['shop_town'],
                'postcode'      => $validated['shop_postcode'],
                'phone'         => $validated['phone'] ?? null,
                'opening_hours' => $validated['opening_hours'] ?? null,
                'description'   => $validated['shop_description'] ?? null,
            ]);
            try {
                NotificationService::notifyNewShopRegistration($user);
            } catch (\Exception $e) {
                \Log::warning('Shop registration notification failed: ' . $e->getMessage());
            }
        } elseif ($role === 'vcfse') {
            OrganisationProfile::create([
                'user_id'        => $user->id,
                'org_name'       => $validated['org_name'],
                'org_type'       => 'vcfse',
                'contact_person' => $validated['contact_name'],
                'charity_number' => $validated['charity_number'] ?? null,
                'phone'          => $validated['phone'] ?? null,
                'address'        => $validated['org_address'] . ', ' . $validated['org_town'],
                'postcode'       => $validated['org_postcode'],
                'website'        => $validated['org_website'] ?? null,
            ]);
            try {
                NotificationService::notifyNewVCFSERegistration($user);
            } catch (\Exception $e) {
                \Log::warning('VCFSE registration notification failed: ' . $e->getMessage());
            }
        } else { // school_care
            OrganisationProfile::create([
                'user_id'        => $user->id,
                'org_name'       => $validated['school_org_name'],
                'org_type'       => $validated['org_type'],
                'contact_person' => $validated['school_contact_name'],
                'charity_number' => $validated['school_reg_number'] ?? null,
                'phone'          => $validated['phone'] ?? null,
                'address'        => $validated['school_address'] . ', ' . $validated['school_town'],
                'postcode'       => $validated['school_postcode'],
                'website'        => $validated['school_website'] ?? null,
            ]);
            try {
                NotificationService::notifyNewSchoolCareRegistration($user);
            } catch (\Exception $e) {
                \Log::warning('School/Care registration notification failed: ' . $e->getMessage());
            }
        }

        // Send welcome email to new user
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            \Log::warning('Welcome email failed for ' . $user->email . ': ' . $e->getMessage());
        }

        // Log the user in immediately and redirect to their dashboard
        Auth::login($user);

        $dashboardRoutes = [
            'recipient'   => 'recipient.dashboard',
            'local_shop'  => 'shop.dashboard',
            'vcfse'       => 'vcfse.dashboard',
            'school_care' => 'school.dashboard',
        ];

        $roleLabels = [
            'recipient'   => 'Recipient',
            'local_shop'  => 'Local Food Shop',
            'vcfse'       => 'VCFSE Organisation',
            'school_care' => 'School / Care Organisation',
        ];

        return redirect()->route($dashboardRoutes[$role])
            ->with('success', 'Welcome to eVoucher Food Support, ' . $user->name . '! Your ' . $roleLabels[$role] . ' account is now active.');
    }
}
