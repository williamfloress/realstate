@extends('layouts.agent')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <h4 class="card-title mb-1">Dashboard</h4>
                    <p class="card-text text-muted mb-0">
                        Bienvenido, <strong>{{ Auth::user()->name }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Mis propiedades</h5>
                    <p class="card-text mb-0">
                        <a href="{{ route('agent.properties.index') }}" class="text-decoration-none text-dark">
                            <span class="h4 font-weight-bold">{{ $propertyCount }}</span>
                            <small class="text-muted d-block">propiedades</small>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Solicitudes</h5>
                    <p class="card-text mb-0">
                        <a href="{{ route('agent.requests.index') }}" class="text-decoration-none text-dark">
                            <span class="h4 font-weight-bold">{{ $requestCount }}</span>
                            <small class="text-muted d-block">solicitudes de información</small>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Tipos de propiedad</h5>
                    <p class="card-text mb-0">
                        <span class="h4 font-weight-bold">{{ $homeTypeCount }}</span>
                        <small class="text-muted d-block">disponibles</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <h5 class="card-title mb-3">Acciones rápidas</h5>
                    <a href="{{ route('agent.properties.create') }}" class="btn btn-primary">Nueva propiedad</a>
                    <a href="{{ route('agent.requests.index') }}" class="btn btn-outline-primary ml-2">Ver solicitudes</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
