<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /**
     * Store the chosen locale in the session and redirect back.
     */
    public function switch(string $locale): RedirectResponse
    {
        if (!in_array($locale, ['km', 'en'], true)) {
            $locale = 'km';
        }

        session(['locale' => $locale]);

        return redirect()->back();
    }
}
