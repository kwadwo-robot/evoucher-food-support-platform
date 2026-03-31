<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodListing extends Model
{
    protected $fillable = [
        'shop_user_id', 'item_name', 'description', 'quantity',
        'expiry_date', 'voucher_value', 'image_url', 'status',
        'collection_address', 'collection_time', 'collection_instructions',
        'listing_type', 'original_price', 'discounted_price',
    ];

    protected $casts = [
        'expiry_date'      => 'date',
        'voucher_value'    => 'decimal:2',
        'original_price'   => 'decimal:2',
        'discounted_price' => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function shopUser()    { return $this->belongsTo(User::class, 'shop_user_id'); }
    public function shop()        { return $this->belongsTo(User::class, 'shop_user_id'); }
    public function shopProfile() { return $this->hasOneThrough(ShopProfile::class, User::class, 'id', 'user_id', 'shop_user_id', 'id'); }
    public function redemptions() { return $this->hasMany(Redemption::class); }
    public function allocations() { return $this->hasMany(SurplusAllocation::class); }

    // ── Accessors ────────────────────────────────────────────────────────────

    /** Human-readable label for the listing type */
    public function getListingTypeLabelAttribute(): string
    {
        return match($this->listing_type ?? 'free') {
            'discounted' => 'Food to Go',
            'surplus'    => 'Free Surplus',
            default      => 'Free',
        };
    }

    /** Badge CSS class for the listing type */
    public function getListingTypeBadgeAttribute(): string
    {
        return match($this->listing_type ?? 'free') {
            'discounted' => 'badge-orange',
            'surplus'    => 'badge-purple',
            default      => 'badge-green',
        };
    }

    /** Icon for the listing type */
    public function getListingTypeIconAttribute(): string
    {
        return match($this->listing_type ?? 'free') {
            'discounted' => 'fa-tag',
            'surplus'    => 'fa-boxes-stacked',
            default      => 'fa-gift',
        };
    }

    // ── Query Scopes (visibility rules) ─────────────────────────────────────

    /** Recipients see: discounted only (NOT free or surplus) */
    public function scopeVisibleToRecipient($query)
    {
        return $query->where('listing_type', 'discounted');
    }

    /** Schools/Care see: discounted only (NOT free or surplus) */
    public function scopeVisibleToSchoolCare($query)
    {
        return $query->where('listing_type', 'discounted');
    }

    /** VCFSE sees: free + discounted + surplus (all types) */
    public function scopeVisibleToVcfse($query)
    {
        return $query->whereIn('listing_type', ['free', 'discounted', 'surplus']);
    }

    /** Public browse page shows: discounted only */
    public function scopePubliclyVisible($query)
    {
        return $query->where('listing_type', 'discounted');
    }
}
