@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-0">Solicitudes de agentes</h4>
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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="mb-3">
        <form method="GET" action="{{ route('admin.agent-applications.index') }}" class="form-inline">
            <label class="mr-2">Filtrar por estado:</label>
            <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprobada</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazada</option>
            </select>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th style="width: 120px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $app)
                            <tr>
                                <td>{{ $app->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $app->user->name ?? '-' }}</td>
                                <td>{{ $app->user->email ?? '-' }}</td>
                                <td>{{ $app->phone ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusBadge = match($app->status) {
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary',
                                        };
                                        $statusLabel = match($app->status) {
                                            'pending' => 'Pendiente',
                                            'approved' => 'Aprobada',
                                            'rejected' => 'Rechazada',
                                            default => $app->status,
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusBadge }}">{{ $statusLabel }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.agent-applications.show', $app) }}" class="btn btn-sm btn-outline-primary">Revisar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay solicitudes de agentes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($applications->hasPages())
            <div class="card-footer">
                {{ $applications->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
