@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.hometypes.index') }}">Tipos de propiedades</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nuevo tipo</li>
                </ol>
            </nav>
            <h4 class="mt-2 mb-0">Crear nuevo tipo de propiedad</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.hometypes.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="home_type">Slug (identificador para URLs)</label>
                            <input type="text" name="home_type" id="home_type" class="form-control"
                                value="{{ old('home_type') }}" placeholder="ej: duplex, studio"
                                pattern="[a-z0-9\-]+" required autofocus>
                            <small class="form-text text-muted">Solo letras minúsculas, números y guiones. Ej: condo, apartment, commercial</small>
                        </div>

                        <div class="form-group">
                            <label for="name">Nombre para mostrar</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" placeholder="ej: Duplex, Estudio" required>
                        </div>

                        <div class="form-group">
                            <label for="order">Orden</label>
                            <input type="number" name="order" id="order" class="form-control"
                                value="{{ old('order') }}" min="0" placeholder="Opcional">
                            <small class="form-text text-muted">Define el orden en listados. Si se deja vacío, se asigna al final.</small>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.hometypes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear tipo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
