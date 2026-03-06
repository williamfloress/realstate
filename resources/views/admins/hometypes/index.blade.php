@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Tipos de propiedades</h4>
            <a href="{{ route('admin.hometypes.create') }}" class="btn btn-primary">Nuevo tipo</a>
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

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Slug</th>
                            <th>Nombre</th>
                            <th>Orden</th>
                            <th>Propiedades</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($homeTypes as $ht)
                            <tr>
                                <td>{{ $ht->id }}</td>
                                <td><code>{{ $ht->home_type }}</code></td>
                                <td>{{ $ht->name }}</td>
                                <td>{{ $ht->order }}</td>
                                <td>{{ $ht->properties_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay tipos de propiedades registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($homeTypes->hasPages())
            <div class="card-footer">
                {{ $homeTypes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
