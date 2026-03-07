<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgentOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('admin')->check()) {
            return $next($request);
        }
        if (auth()->check() && auth()->user()->isAgent()) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Debe iniciar sesión como agente o administrador.');
    }
}
