<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$adminUsers = [
    [
        'name' => 'Super Administrator',
        'email' => 'superadmin@evoucher.org',
        'password' => Hash::make('Admin@1234'),
        'role' => 'super_admin',
        'is_approved' => true,
        'is_active' => true,
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email_verified_at' => now(),
    ],
    [
        'name' => 'Administrator',
        'email' => 'admin@evoucher.org',
        'password' => Hash::make('Admin@1234'),
        'role' => 'admin',
        'is_approved' => true,
        'is_active' => true,
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email_verified_at' => now(),
    ],
];

foreach ($adminUsers as $userData) {
    $user = User::updateOrCreate(
        ['email' => $userData['email']],
        $userData
    );
    echo "Created/updated: {$user->email} (role: {$user->role})\n";
}

echo "\nAdmin users seeded successfully!\n";
