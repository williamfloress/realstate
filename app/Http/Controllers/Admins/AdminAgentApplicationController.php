<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\AgentApplication;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAgentApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = AgentApplication::with(['user', 'reviewer'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(15);
        return view('admins.agent-applications.index', compact('applications'));
    }

    public function show(AgentApplication $agentApplication)
    {
        $agentApplication->load(['user', 'reviewer']);
        return view('admins.agent-applications.show', compact('agentApplication'));
    }

    public function approve(AgentApplication $agentApplication)
    {
        if ($agentApplication->status !== AgentApplication::STATUS_PENDING) {
            return back()->with('error', 'Esta solicitud ya fue procesada.');
        }

        $agentApplication->update([
            'status' => AgentApplication::STATUS_APPROVED,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->guard('admin')->id(),
        ]);

        $agentApplication->user->update(['role' => User::ROLE_AGENT]);

        return redirect()->route('admin.agent-applications.index')
            ->with('success', 'Solicitud aprobada. El usuario ahora es agente.');
    }

    public function reject(Request $request, AgentApplication $agentApplication)
    {
        if ($agentApplication->status !== AgentApplication::STATUS_PENDING) {
            return back()->with('error', 'Esta solicitud ya fue procesada.');
        }

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $agentApplication->update([
            'status' => AgentApplication::STATUS_REJECTED,
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->guard('admin')->id(),
        ]);

        return redirect()->route('admin.agent-applications.index')
            ->with('success', 'Solicitud rechazada.');
    }
}
