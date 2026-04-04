<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDeposit extends Model
{
    protected $fillable = [
        'organisation_user_id',
        'amount',
        'reference',
        'bank_account_holder',
        'bank_name',
        'sort_code',
        'account_number',
        'status',
        'notes',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function organisation()
    {
        return $this->belongsTo(User::class, 'organisation_user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }
}
