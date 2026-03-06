@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    {{-- Encabezado de bienvenida --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <h4 class="card-title mb-1">Dashboard</h4>
                    <p class="card-text text-muted mb-0">
                        Bienvenido, <strong>{{ Auth::guard('admin')->user()->name }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Properties</h5>
                    <p class="card-text mb-0">
                        <a href="{{ route('admin.properties.index') }}" class="text-decoration-none text-dark">
                            <span class="h4 font-weight-bold">{{ $propertyCount }}</span>
                            <small class="text-muted d-block">propiedades registradas</small>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Home Types</h5>
                    <p class="card-text mb-0">
                        <a href="{{ route('admin.hometypes.index') }}" class="text-decoration-none text-dark">
                            <span class="h4 font-weight-bold">{{ $homeTypeCount }}</span>
                            <small class="text-muted d-block">tipos de vivienda</small>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Admins</h5>
                    <p class="card-text mb-0">
                        <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-dark">
                            <span class="h4 font-weight-bold">{{ $adminCount }}</span>
                            <small class="text-muted d-block">administradores</small>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Requests</h5>
                    <p class="card-text mb-0">
                        <a href="{{ route('admin.requests.index') }}" class="text-decoration-none text-dark">
                            <span class="h4 font-weight-bold">{{ $requestCount }}</span>
                            <small class="text-muted d-block">solicitudes</small>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Acciones rápidas --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <h5 class="card-title mb-3">Acciones</h5>
                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
