<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurplusAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_listing_id',
        'vcfse_user_id',
        'school_care_user_id',
        'allocated_at',
        'expires_at',
        'claimed_at',
        'status',
        'allocation_sequence',
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'expires_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    /**
     * Get the food listing associated with this allocation
     */
    public function foodListing(): BelongsTo
    {
        return $this->belongsTo(FoodListing::class);
    }

    /**
     * Get the VCFSE user allocated this item
     */
    public function vcfseUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vcfse_user_id');
    }

    /**
     * Get the School/Care user allocated this item
     */
    public function schoolCareUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'school_care_user_id');
    }

    /**
     * Get the allocated user (either VCFSE or School/Care)
     */
    public function getAllocatedUser()
    {
        return $this->school_care_user_id ? $this->schoolCareUser : $this->vcfseUser;
    }

    /**
     * Check if allocation has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get time remaining in minutes
     */
    public function getTimeRemainingMinutes(): int
    {
        if (!$this->expires_at) {
            return 0;
        }
        return max(0, (int)$this->expires_at->diffInMinutes(now()));
    }

    /**
     * Get next VCFSE user in queue for this item
     */
    public static function getNextVcfseUser($foodListingId)
    {
        // Get all VCFSE users, excluding those who already have expired allocations for this item
        $usedUsers = self::where('food_listing_id', $foodListingId)
            ->whereIn('status', ['expired', 'redeemed'])
            ->pluck('vcfse_user_id')
            ->toArray();

        return User::where('role', 'vcfse')
            ->where('is_approved', true)
            ->whereNotIn('id', $usedUsers)
            ->inRandomOrder()
            ->first();
    }

    /**
     * Get next School/Care user in queue for this item
     */
    public static function getNextSchoolCareUser($foodListingId)
    {
        // Get all School/Care users, excluding those who already have expired allocations for this item
        $usedUsers = self::where('food_listing_id', $foodListingId)
            ->whereIn('status', ['expired', 'redeemed'])
            ->whereNotNull('school_care_user_id')
            ->pluck('school_care_user_id')
            ->toArray();

        return User::where('role', 'school_care')
            ->where('is_approved', true)
            ->whereNotIn('id', $usedUsers)
            ->inRandomOrder()
            ->first();
    }
}
