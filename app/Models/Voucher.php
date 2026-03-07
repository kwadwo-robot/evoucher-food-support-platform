<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'recipient_user_id', 'issued_by',
        'value', 'remaining_value', 'status', 'expiry_date', 'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'value' => 'decimal:2',
        'remaining_value' => 'decimal:2',
    ];

    public function recipient() { return $this->belongsTo(User::class, 'recipient_user_id'); }
    public function issuedBy() { return $this->belongsTo(User::class, 'issued_by'); }
    public function redemptions() { return $this->hasMany(Redemption::class); }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper('EV-' . Str::random(8));
        } while (static::where('code', $code)->exists());
        return $code;
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expiry_date >= now();
    }
}
