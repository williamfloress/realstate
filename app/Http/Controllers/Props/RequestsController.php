<?php

namespace App\Http\Controllers\Props;

use App\Http\Controllers\Controller;
use App\Models\Prop\Request as PropRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestsController extends Controller
{
    public function insertRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:2000',
        ]);

        PropRequest::create([
            'property_id' => $validated['property_id'],
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => PropRequest::STATUS_PENDING,
        ]);

        return back()->with('success', 'Solicitud enviada correctamente.');
    }
}
