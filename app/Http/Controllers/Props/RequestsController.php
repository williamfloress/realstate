<?php

namespace App\Http\Controllers\Props;

use App\Http\Controllers\Controller;
use App\Models\Prop\Request as PropRequest;  // Alias para evitar conflicto con Illuminate\Http\Request
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de solicitudes (requests)
 *
 * Gestiona las solicitudes de información que los visitantes envían desde
 * la página de detalle de una propiedad. Valida los datos, crea el registro
 * en la BD y redirige con mensaje de éxito o errores de validación.
 */
class RequestsController extends Controller
{
    /**
     * Recibe el formulario de contacto, valida los datos y crea un nuevo request.
     *
     * - Valida: property_id (debe existir), name, email, phone (opcional), message (opcional)
     * - Evita duplicados: 1 request por propiedad por usuario (logueado) o por email (invitado)
     * - Si el usuario está logueado, guarda su user_id para vincular la solicitud
     * - Crea el request con status 'pending' por defecto
     * - Redirige a la página anterior con mensaje flash de éxito o errores
     */
    public function insertRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:2000',
        ]);

        $propertyId = (int) $validated['property_id'];
        $email = $validated['email'];

        // Usuario logueado: solo 1 request por propiedad por user_id
        if (Auth::check()) {
            $exists = PropRequest::where('property_id', $propertyId)
                ->where('user_id', Auth::id())
                ->exists();
            if ($exists) {
                return back()->with('error', 'Ya enviaste una solicitud para esta propiedad.');
            }
        }

        // Usuario invitado: solo 1 request por propiedad por email (evita spam con el mismo correo)
        if (! Auth::check()) {
            $exists = PropRequest::where('property_id', $propertyId)
                ->where('email', $email)
                ->exists();
            if ($exists) {
                return back()->with('error', 'Ya enviaste una solicitud para esta propiedad con este correo.');
            }
        }

        PropRequest::create([
            'property_id' => $validated['property_id'],
            'user_id' => Auth::id(),  // null si no está logueado
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => PropRequest::STATUS_PENDING,
        ]);

        return back()->with('success', 'Solicitud enviada correctamente.');
    }
}
