<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\ShopProfile;
use App\Models\RecipientProfile;
use App\Models\OrganisationProfile;

// Fix shop profile
$shopUser = User::where('email', 'shop@evoucher.org')->first();
if ($shopUser && !$shopUser->shopProfile) {
    ShopProfile::create([
        'user_id'   => $shopUser->id,
        'shop_name' => 'Test Local Shop',
        'address'   => '123 Market Street, Northampton',
        'phone'     => '01604 000001',
        'town'      => 'Northampton',
        'postcode'  => 'NN1 1AA',
    ]);
    echo "Shop profile created\n";
} else {
    echo "Shop profile already exists\n";
}

// Fix recipient profile
$recUser = User::where('email', 'recipient@evoucher.org')->first();
if ($recUser && !$recUser->recipientProfile) {
    RecipientProfile::create([
        'user_id'    => $recUser->id,
        'first_name' => 'Test',
        'last_name'  => 'Recipient',
        'phone'      => '07700 000002',
        'address'    => '45 High Street, Northampton, NN1 2BB',
    ]);
    echo "Recipient profile created\n";
} else {
    echo "Recipient profile already exists\n";
}

// Fix VCFSE profile
$vcfseUser = User::where('email', 'vcfse@evoucher.org')->first();
if ($vcfseUser && !$vcfseUser->organisationProfile) {
    OrganisationProfile::create([
        'user_id'        => $vcfseUser->id,
        'org_name'       => 'Northampton Community Trust',
        'org_type'       => 'vcfse',
        'contact_person' => 'VCFSE Admin',
        'phone'          => '01604 000003',
        'charity_number' => '1234567',
    ]);
    echo "VCFSE profile created\n";
} else {
    echo "VCFSE profile already exists\n";
}

// Fix school profile
$schoolUser = User::where('email', 'school@evoucher.org')->first();
if ($schoolUser && !$schoolUser->organisationProfile) {
    OrganisationProfile::create([
        'user_id'        => $schoolUser->id,
        'org_name'       => 'Northampton Primary School',
        'org_type'       => 'school',
        'contact_person' => 'School Admin',
        'phone'          => '01604 000004',
        'charity_number' => null,
    ]);
    echo "School profile created\n";
} else {
    echo "School profile already exists\n";
}

echo "Done.\n";
