@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-0">Solicitudes de información</h4>
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

    <div class="mb-3">
        <form method="GET" action="{{ route('admin.requests.index') }}" class="form-inline">
            <label class="mr-2">Filtrar por estado:</label>
            <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contactado</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Cerrado</option>
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
                            <th>Solicitante</th>
                            <th>Email</th>
                            <th>Propiedad</th>
                            <th>Estado</th>
                            <th style="width: 100px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $req)
                            <tr>
                                <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $req->name }}</td>
                                <td>{{ $req->email }}</td>
                                <td>
                                    @if ($req->property)
                                        <a href="{{ route('single.property', $req->property->id) }}" target="_blank">
                                            {{ Str::limit($req->property->title ?? $req->property->address ?? 'Propiedad', 25) }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusBadge = match($req->status) {
                                            'pending' => 'warning',
                                            'contacted' => 'info',
                                            'closed' => 'secondary',
                                            default => 'light',
                                        };
                                        $statusLabel = match($req->status) {
                                            'pending' => 'Pendiente',
                                            'contacted' => 'Contactado',
                                            'closed' => 'Cerrado',
                                            default => $req->status,
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusBadge }}">{{ $statusLabel }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.requests.show', $req) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay solicitudes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($requests->hasPages())
            <div class="card-footer">
                {{ $requests->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
