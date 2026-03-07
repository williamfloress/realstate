@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.agent-applications.index') }}">Solicitudes de agentes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Solicitud #{{ $agentApplication->id }}</li>
                </ol>
            </nav>
            <h4 class="mt-2 mb-0">Revisar solicitud de agente</h4>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del solicitante</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Nombre</dt>
                        <dd class="col-sm-9">{{ $agentApplication->user->name ?? '-' }}</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $agentApplication->user->email ?? '-' }}</dd>

                        <dt class="col-sm-3">Teléfono</dt>
                        <dd class="col-sm-9">{{ $agentApplication->phone ?? '-' }}</dd>

                        <dt class="col-sm-3">Licencia / Matrícula</dt>
                        <dd class="col-sm-9">{{ $agentApplication->license_number ?? '-' }}</dd>

                        <dt class="col-sm-3">Fecha solicitud</dt>
                        <dd class="col-sm-9">{{ $agentApplication->created_at->format('d/m/Y H:i') }}</dd>

                        @if ($agentApplication->bio)
                            <dt class="col-sm-3">Descripción profesional</dt>
                            <dd class="col-sm-9">{{ $agentApplication->bio }}</dd>
                        @endif

                        @if ($agentApplication->message)
                            <dt class="col-sm-3">Mensaje</dt>
                            <dd class="col-sm-9">{{ $agentApplication->message }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Documentos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Certificado de bienes raíces</strong>
                            @if ($agentApplication->real_estate_certificate)
                                <a href="{{ asset('storage/' . $agentApplication->real_estate_certificate) }}" target="_blank" class="d-block mt-1">Ver documento</a>
                            @else
                                <span class="text-muted">No subido</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Documento de identidad</strong>
                            @if ($agentApplication->id_document)
                                <a href="{{ asset('storage/' . $agentApplication->id_document) }}" target="_blank" class="d-block mt-1">Ver documento</a>
                            @else
                                <span class="text-muted">No subido</span>
                            @endif
                        </div>
                        @if ($agentApplication->other_documents && count($agentApplication->other_documents) > 0)
                            <div class="col-12">
                                <strong>Otros documentos</strong>
                                <ul class="list-unstyled mt-1">
                                    @foreach ($agentApplication->other_documents as $doc)
                                        <li><a href="{{ asset('storage/' . $doc) }}" target="_blank">Ver documento</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Decisión</h5>
                </div>
                <div class="card-body">
                    @php
                        $statusBadge = match($agentApplication->status) {
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'secondary',
                        };
                        $statusLabel = match($agentApplication->status) {
                            'pending' => 'Pendiente',
                            'approved' => 'Aprobada',
                            'rejected' => 'Rechazada',
                            default => $agentApplication->status,
                        };
                    @endphp
                    <p class="mb-3">Estado actual: <span class="badge badge-{{ $statusBadge }}">{{ $statusLabel }}</span></p>

                    @if ($agentApplication->reviewed_at)
                        <p class="small text-muted">
                            Revisado el {{ $agentApplication->reviewed_at->format('d/m/Y H:i') }}
                            @if ($agentApplication->reviewer)
                                por {{ $agentApplication->reviewer->name }}
                            @endif
                        </p>
                    @endif

                    @if ($agentApplication->admin_notes)
                        <div class="alert alert-light">
                            <strong>Notas:</strong> {{ $agentApplication->admin_notes }}
                        </div>
                    @endif

                    @if ($agentApplication->status === 'pending')
                        <form method="POST" action="{{ route('admin.agent-applications.approve', $agentApplication) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">Aprobar solicitud</button>
                        </form>

                        <form method="POST" action="{{ route('admin.agent-applications.reject', $agentApplication) }}">
                            @csrf
                            <div class="form-group">
                                <label for="admin_notes">Notas (opcional, se mostrarán al solicitante)</label>
                                <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3" placeholder="Motivo del rechazo...">{{ old('admin_notes') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-block">Rechazar solicitud</button>
                        </form>
                    @endif

                    <a href="{{ route('admin.agent-applications.index') }}" class="btn btn-outline-secondary btn-block mt-3">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
