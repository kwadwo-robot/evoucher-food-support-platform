<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'is_approved', 'is_active', 'activation_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ---- Relationships ----
    public function recipientProfile() { return $this->hasOne(RecipientProfile::class); }
    public function shopProfile() { return $this->hasOne(ShopProfile::class); }
    public function organisationProfile() { return $this->hasOne(OrganisationProfile::class); }
    public function vouchers() { return $this->hasMany(Voucher::class, 'recipient_user_id'); }
    public function issuedVouchers() { return $this->hasMany(Voucher::class, 'issued_by'); }
    public function redemptions() { return $this->hasMany(Redemption::class, 'recipient_user_id'); }
    public function foodListings() { return $this->hasMany(FoodListing::class, 'shop_user_id'); }
    public function payments() { return $this->hasMany(Payment::class, 'donor_user_id'); }
    public function donations() { return $this->hasMany(Donation::class, 'donor_user_id'); }

    // ---- Role Helpers ----
    public function isAdmin(): bool { return in_array($this->role, ['admin', 'super_admin']); }
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isRecipient(): bool { return $this->role === 'recipient'; }
    public function isShop(): bool { return $this->role === 'local_shop'; }
    public function isVcfse(): bool { return $this->role === 'vcfse'; }
    public function isSchoolCare(): bool { return $this->role === 'school_care'; }

    public function getDashboardRoute(): string
    {
        return match($this->role) {
            'admin', 'super_admin' => 'admin.dashboard',
            'recipient'            => 'recipient.dashboard',
            'local_shop'           => 'shop.dashboard',
            'vcfse'                => 'vcfse.dashboard',
            'school_care'          => 'school.dashboard',
            default                => 'home',
        };
    }
}
