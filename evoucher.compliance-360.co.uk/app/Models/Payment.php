<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'donor_user_id', 'amount', 'currency', 'status',
        'payment_method', 'stripe_payment_id', 'stripe_session_id', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function donor() { return $this->belongsTo(User::class, 'donor_user_id'); }
}
