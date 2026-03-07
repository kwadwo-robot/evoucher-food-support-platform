<?php
/**
 * Standalone seed script to create test users for all roles.
 * Run with: php seed_users.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$users = [
    [
        'name' => 'Green Grocers Northampton',
        'email' => 'shop@evoucher.org',
        'password' => Hash::make('Admin@1234'),
        'role' => 'local_shop',
        'is_approved' => true,
        'is_active' => true,
        'first_name' => 'John',
        'last_name' => 'Smith',
        'phone' => '01604123456',
        'address' => '12 Market Street, Northampton',
        'postcode' => 'NN1 1AA',
        'organisation_name' => 'Green Grocers',
        'email_verified_at' => now(),
    ],
    [
        'name' => 'Jane Doe',
        'email' => 'recipient@evoucher.org',
        'password' => Hash::make('Admin@1234'),
        'role' => 'recipient',
        'is_approved' => true,
        'is_active' => true,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'phone' => '07700900000',
        'address' => '5 Abington Street, Northampton',
        'postcode' => 'NN1 2LH',
        'email_verified_at' => now(),
    ],
    [
        'name' => 'Northampton Community Trust',
        'email' => 'vcfse@evoucher.org',
        'password' => Hash::make('Admin@1234'),
        'role' => 'vcfse',
        'is_approved' => true,
        'is_active' => true,
        'first_name' => 'Sarah',
        'last_name' => 'Johnson',
        'phone' => '01604654321',
        'organisation_name' => 'Northampton Community Trust',
        'organisation_type' => 'vcfse',
        'email_verified_at' => now(),
    ],
    [
        'name' => 'Northampton Academy',
        'email' => 'school@evoucher.org',
        'password' => Hash::make('Admin@1234'),
        'role' => 'school_care',
        'is_approved' => true,
        'is_active' => true,
        'first_name' => 'Michael',
        'last_name' => 'Brown',
        'phone' => '01604789012',
        'organisation_name' => 'Northampton Academy',
        'organisation_type' => 'school',
        'email_verified_at' => now(),
    ],
];

foreach ($users as $userData) {
    $user = User::updateOrCreate(
        ['email' => $userData['email']],
        $userData
    );
    echo "Created/updated: {$user->email} (role: {$user->role})\n";
}

echo "\nAll test users seeded successfully!\n";
