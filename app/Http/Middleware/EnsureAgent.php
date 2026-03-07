<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAgent()) {
            return redirect()->route('home')->with('error', 'Acceso restringido a agentes.');
        }

        return $next($request);
    }
}
