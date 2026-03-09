<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProfile extends Model
{
    protected $fillable = [
        'user_id', 'shop_name', 'category', 'phone', 'address', 'town', 'postcode',
        'opening_hours', 'description', 'logo_url', 'is_verified',
    ];

    protected $casts = ['is_verified' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function foodListings() { return $this->hasMany(FoodListing::class, 'shop_user_id', 'user_id'); }
}
