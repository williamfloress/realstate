@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sectores.index') }}">Sectores</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar sector</li>
                </ol>
            </nav>
            <h4 class="mt-2 mb-0">Editar sector: {{ $sector->nombre }}</h4>
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

                    <form method="POST" action="{{ route('admin.sectores.update', $sector) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nombre">Nombre *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                value="{{ old('nombre', $sector->nombre) }}" required autofocus>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="latitud">Latitud</label>
                                <input type="number" name="latitud" id="latitud" class="form-control" step="any"
                                    value="{{ old('latitud', $sector->latitud) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="longitud">Longitud</label>
                                <input type="number" name="longitud" id="longitud" class="form-control" step="any"
                                    value="{{ old('longitud', $sector->longitud) }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.sectores.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
