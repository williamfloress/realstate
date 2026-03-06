<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Prop\Request as PropRequest;
use Illuminate\Http\Request;

class AdminRequestController extends Controller
{
    /**
     * Display a listing of all requests.
     */
    public function index(Request $request)
    {
        $query = PropRequest::with(['property', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15);

        return view('admins.requests.index', compact('requests'));
    }

    /**
     * Display the specified request.
     */
    public function show(PropRequest $propRequest)
    {
        $propRequest->load(['property', 'user']);

        return view('admins.requests.show', ['request' => $propRequest]);
    }

    /**
     * Update the request (status and/or admin response).
     */
    public function update(Request $httpRequest, PropRequest $propRequest)
    {
        $validated = $httpRequest->validate([
            'status' => ['required', 'in:pending,contacted,closed'],
            'admin_response' => ['nullable', 'string', 'max:5000'],
        ]);

        $update = [
            'status' => $validated['status'],
        ];

        if (array_key_exists('admin_response', $validated)) {
            $update['admin_response'] = $validated['admin_response'] ?: null;
            $update['responded_at'] = $validated['admin_response'] ? now() : null;
        }

        $propRequest->update($update);

        $labels = [
            'pending' => 'Pendiente',
            'contacted' => 'Contactado',
            'closed' => 'Cerrado',
        ];

        return redirect()->route('admin.requests.show', $propRequest)
            ->with('success', 'Solicitud actualizada. Estado: ' . $labels[$validated['status']]);
    }
}
