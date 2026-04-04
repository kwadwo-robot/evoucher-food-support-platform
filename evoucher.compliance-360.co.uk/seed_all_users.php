<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\OrganisationProfile;
use Illuminate\Support\Facades\Hash;

// Users to create
$users = [
    // Admin users
    [
        'name' => 'Super Administrator',
        'email' => 'superadmin@evoucher.org',
        'role' => 'super_admin',
        'is_approved' => true,
    ],
    [
        'name' => 'Administrator',
        'email' => 'admin@evoucher.org',
        'role' => 'admin',
        'is_approved' => true,
    ],
    // Local Shop
    [
        'name' => 'Local Shop Owner',
        'email' => 'shop@evoucher.org',
        'role' => 'local_shop',
        'is_approved' => true,
    ],
    // Recipient
    [
        'name' => 'John Recipient',
        'email' => 'recipient@evoucher.org',
        'role' => 'recipient',
        'is_approved' => true,
    ],
    // VCFSE
    [
        'name' => 'Northampton Community Trust',
        'email' => 'vcfse@evoucher.org',
        'role' => 'vcfse',
        'is_approved' => true,
    ],
    // School/Care
    [
        'name' => 'Northampton Primary School',
        'email' => 'school@evoucher.org',
        'role' => 'school_care',
        'is_approved' => true,
    ],
];

foreach ($users as $userData) {
    $user = User::updateOrCreate(
        ['email' => $userData['email']],
        array_merge($userData, ['password' => Hash::make('Admin@1234')])
    );

    // Create organisation profile for VCFSE and School/Care
        if (in_array($userData['role'], ['vcfse', 'school_care'])) {
            OrganisationProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'org_name' => $userData['name'],
                    'org_type' => $userData['role'] === 'vcfse' ? 'VCFSE' : 'School/Care',
                    'contact_person' => 'Contact Person',
                    'phone' => '01604123456',
                    'address' => 'Northamptonshire',
                    'postcode' => 'NN1 1AA',
                ]
            );
        }

    echo "Created/updated: {$userData['email']} (role: {$userData['role']})\n";
}

echo "\nAll test users seeded successfully!\n";
