<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if (! $locale) {
            $locale = $this->detectBrowserLocale($request);
        }

        if (in_array($locale, ['en', 'es'])) {
            App::setLocale($locale);
        } else {
            App::setLocale(config('app.fallback_locale'));
        }

        return $next($request);
    }

    private function detectBrowserLocale(Request $request): string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (! $acceptLanguage) {
            return config('app.locale');
        }

        $languages = [];
        foreach (explode(',', $acceptLanguage) as $part) {
            $part = trim($part);
            if (str_contains($part, ';')) {
                [$code, $q] = explode(';', $part, 2);
                $code = strtolower(trim(explode('-', trim($code))[0]));
                $q = (float) str_replace('q=', '', trim($q));
            } else {
                $code = strtolower(trim(explode('-', trim($part))[0]));
                $q = 1.0;
            }
            if (strlen($code) === 2 && in_array($code, ['es', 'en'])) {
                $languages[$code] = max($languages[$code] ?? 0, $q);
            }
        }

        if (empty($languages)) {
            return config('app.locale');
        }

        arsort($languages);
        return array_key_first($languages);
    }
}
