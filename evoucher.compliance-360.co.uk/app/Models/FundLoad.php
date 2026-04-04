<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundLoad extends Model
{
    protected $fillable = [
        'organisation_user_id',
        'admin_user_id',
        'amount',
        'notes',
        'reference',
        'stripe_transaction_id',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function organisation()
    {
        return $this->belongsTo(User::class, 'organisation_user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
