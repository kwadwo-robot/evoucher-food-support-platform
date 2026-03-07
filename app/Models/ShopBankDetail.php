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
    ];

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_user_id');
    }
}
