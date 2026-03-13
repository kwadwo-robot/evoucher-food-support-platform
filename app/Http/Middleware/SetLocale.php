<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    protected array $supported = ['en', 'ar', 'ro', 'pl'];

    public function handle(Request $request, Closure $next)
    {
        // Check if a language switch is requested via query parameter
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, $this->supported)) {
                Session::put('locale', $lang);
            }
        }

        // Apply the stored locale from session or default to 'en'
        $locale = Session::get('locale', config('app.locale', 'en'));
        
        // Ensure the locale is supported
        if (in_array($locale, $this->supported)) {
            App::setLocale($locale);
            // Also set the session locale to ensure persistence
            Session::put('locale', $locale);
        } else {
            // Default to English if unsupported locale
            App::setLocale('en');
            Session::put('locale', 'en');
        }

        return $next($request);
    }
}
