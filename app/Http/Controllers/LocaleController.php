<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

class LocaleController extends Controller
{
    public function setLocale(string $locale)
    {
        if (! in_array($locale, ['en', 'es'])) {
            $locale = config('app.fallback_locale');
        }

        session(['locale' => $locale]);
        App::setLocale($locale);

        return Redirect::back();
    }
}
