<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Set the application locale from the session.
     * Falls back to the system-level default_locale setting (stored in DB),
     * which itself defaults to Khmer ('km').
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $systemDefault = SystemSetting::get('default_locale', 'km');
        } catch (\Throwable) {
            $systemDefault = 'km';
        }

        $locale = session('locale', $systemDefault);

        if (!in_array($locale, ['km', 'en'], true)) {
            $locale = $systemDefault;
        }

        App::setLocale($locale);

        return $next($request);
    }
}
