@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Sectores (AMC)</h4>
            <a href="{{ route('admin.sectores.create') }}" class="btn btn-primary">Nuevo sector</a>
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

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Latitud</th>
                            <th>Longitud</th>
                            <th>Propiedades</th>
                            <th style="width: 140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sectores as $sector)
                            <tr>
                                <td>{{ $sector->id }}</td>
                                <td>{{ $sector->nombre }}</td>
                                <td>{{ $sector->latitud ?? '-' }}</td>
                                <td>{{ $sector->longitud ?? '-' }}</td>
                                <td>{{ $sector->properties_count }}</td>
                                <td>
                                    <a href="{{ route('admin.sectores.edit', $sector) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form method="POST" action="{{ route('admin.sectores.destroy', $sector) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este sector?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay sectores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($sectores->hasPages())
            <div class="card-footer">
                {{ $sectores->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
