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
        // Check if a language switch is requested
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, $this->supported)) {
                Session::put('locale', $lang);
            }
        }

        // Apply the stored locale or default to 'en'
        $locale = Session::get('locale', 'en');
        if (in_array($locale, $this->supported)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
