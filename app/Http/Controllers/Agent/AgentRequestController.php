<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Prop\Request as PropRequest;
use Illuminate\Http\Request;

class AgentRequestController extends Controller
{
    private function agentRequests()
    {
        return PropRequest::with(['property', 'user'])
            ->whereHas('property', fn ($q) => $q->where('agent_id', auth()->id()));
    }

    public function index(Request $request)
    {
        $query = $this->agentRequests()->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15);
        return view('agent.requests.index', compact('requests'));
    }

    public function show(PropRequest $propRequest)
    {
        if ($propRequest->property->agent_id !== auth()->id()) {
            abort(403);
        }
        $propRequest->load(['property', 'user']);
        return view('agent.requests.show', ['request' => $propRequest]);
    }

    public function update(Request $httpRequest, PropRequest $propRequest)
    {
        if ($propRequest->property->agent_id !== auth()->id()) {
            abort(403);
        }

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

        return redirect()->route('agent.requests.show', $propRequest)
            ->with('success', 'Solicitud actualizada.');
    }
}
