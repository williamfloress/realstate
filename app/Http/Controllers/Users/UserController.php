<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de usuario autenticado.
 *
 * Gestiona la vista de solicitudes y favoritos del usuario logueado.
 */
class UserController extends Controller
{
    /**
     * Muestra las solicitudes de información enviadas por el usuario autenticado.
     */
    public function myRequests()
    {
        $requests = Auth::user()
            ->requests()
            ->with('property')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.my_requests', compact('requests'));
    }

    /**
     * Muestra las propiedades guardadas en favoritos por el usuario autenticado.
     */
    public function myFavorites()
    {
        $saved = Auth::user()
            ->savedProperties()
            ->with('property.homeType')
            ->orderBy('created_at', 'desc')
            ->get();

        $properties = $saved->pluck('property')->filter()->values();
        $savedPropertyIds = $properties->pluck('id');

        return view('users.my_favorites', compact('properties', 'savedPropertyIds'));
    }
}
