<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceFeeSetting extends Model
{
    protected $table = 'service_fee_settings';

    protected $fillable = [
        'service_fee_percentage',
        'description',
    ];

    protected $casts = [
        'service_fee_percentage' => 'decimal:2',
    ];

    /**
     * Get the current service fee percentage
     */
    public static function getCurrentPercentage()
    {
        return self::first()?->service_fee_percentage ?? 10.00;
    }

    /**
     * Update the service fee percentage
     */
    public static function setPercentage($percentage)
    {
        $setting = self::first() ?? new self();
        $setting->service_fee_percentage = $percentage;
        $setting->save();
        return $setting;
    }
}
