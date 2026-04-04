<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetailChangeRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'shop_user_id',
        'bank_detail_id',
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
    
    public function bankDetail()
    {
        return $this->belongsTo(ShopBankDetail::class, 'bank_detail_id');
    }
    
    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    public function isApproved()
    {
        return $this->status === 'approved';
    }
    
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
