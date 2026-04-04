<?php

namespace App\Services;

use App\Models\Setting;
use Stripe\StripeClient;

class StripeService
{
    /**
     * Get the Stripe publishable (public) key.
     * Checks DB settings first, falls back to .env / config.
     */
    public static function publishableKey(): ?string
    {
        $key = Setting::get('stripe_key');
        if (!empty($key)) {
            return $key;
        }
        return config('services.stripe.key') ?: config('services.stripe.public') ?: null;
    }

    /**
     * Get the Stripe secret key.
     * Checks DB settings first, falls back to .env / config.
     */
    public static function secretKey(): ?string
    {
        $key = Setting::get('stripe_secret');
        if (!empty($key)) {
            return $key;
        }
        return config('services.stripe.secret') ?: null;
    }

    /**
     * Get a configured StripeClient instance, or null if no secret key is available.
     */
    public static function client(): ?StripeClient
    {
        $secret = static::secretKey();
        if (empty($secret)) {
            return null;
        }
        return new StripeClient($secret);
    }

    /**
     * Check if Stripe is fully configured (both keys present).
     */
    public static function isConfigured(): bool
    {
        return !empty(static::secretKey()) && !empty(static::publishableKey());
    }
}
