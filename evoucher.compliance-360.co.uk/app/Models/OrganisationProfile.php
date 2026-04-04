<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganisationProfile extends Model
{
    protected $fillable = [
        'user_id', 'org_name', 'org_type', 'contact_person',
        'phone', 'charity_number', 'address', 'postcode', 'website',
        'wallet_balance',
    ];

    protected $casts = [
        'wallet_balance' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
