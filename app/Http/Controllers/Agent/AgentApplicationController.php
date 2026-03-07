<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgentApplicationController extends Controller
{
    public function showForm()
    {
        if (! Auth::check()) {
            return redirect()->route('register')->with('redirect', route('agent.apply'));
        }

        $user = Auth::user();
        if ($user->isAgent()) {
            return redirect()->route('agent.dashboard')->with('info', 'Ya eres un agente.');
        }

        $application = $user->agentApplication;
        if ($application) {
            if ($application->status === AgentApplication::STATUS_PENDING) {
                return redirect()->route('agent.apply.status')->with('info', 'Tu solicitud está en revisión.');
            }
            if ($application->status === AgentApplication::STATUS_APPROVED) {
                return redirect()->route('agent.dashboard');
            }
        }

        return view('agent.apply', compact('application'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isAgent()) {
            return redirect()->route('agent.dashboard');
        }

        if ($user->agentApplication && $user->agentApplication->status === AgentApplication::STATUS_PENDING) {
            return redirect()->route('agent.apply.status')->with('error', 'Ya tienes una solicitud en revisión.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:50'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'real_estate_certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'id_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'other_documents' => ['nullable', 'array'],
            'other_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'message' => ['nullable', 'string', 'max:2000'],
        ], [
            'real_estate_certificate.required' => 'El certificado de bienes raíces es obligatorio.',
            'id_document.required' => 'El documento de identidad es obligatorio.',
        ]);

        $realEstatePath = $request->file('real_estate_certificate')->store('agent-applications', 'public');
        $idDocPath = $request->file('id_document')->store('agent-applications', 'public');

        $otherPaths = [];
        if ($request->hasFile('other_documents')) {
            foreach ($request->file('other_documents') as $file) {
                $otherPaths[] = $file->store('agent-applications', 'public');
            }
        }

        AgentApplication::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $validated['phone'],
                'license_number' => $validated['license_number'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'real_estate_certificate' => $realEstatePath,
                'id_document' => $idDocPath,
                'other_documents' => $otherPaths ?: null,
                'message' => $validated['message'] ?? null,
                'status' => AgentApplication::STATUS_PENDING,
            ]
        );

        return redirect()->route('agent.apply.status')->with('success', 'Solicitud enviada. Te notificaremos cuando sea revisada.');
    }

    public function status()
    {
        $application = Auth::user()->agentApplication;
        if (! $application) {
            return redirect()->route('agent.apply');
        }

        return view('agent.status', compact('application'));
    }
}
