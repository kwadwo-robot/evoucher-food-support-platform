<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBankDetail extends Model
{
    protected $fillable = [
        'shop_user_id',
        'account_holder_name',
        'bank_name',
        'sort_code',
        'account_number',
        'bank_reference',
        'status',
        'approved_at',
        'rejection_reason',
    ];
    
    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_user_id');
    }
    
    public function changeRequests()
    {
        return $this->hasMany(BankDetailChangeRequest::class, 'bank_detail_id');
    }
    
    public function isLocked()
    {
        return $this->status === 'active';
    }
    
    public function isPendingApproval()
    {
        return $this->status === 'pending_approval';
    }
    
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
