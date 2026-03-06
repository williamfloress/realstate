@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.requests.index') }}">Solicitudes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Solicitud #{{ $request->id }}</li>
                </ol>
            </nav>
            <h4 class="mt-2 mb-0">Detalle de solicitud</h4>
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
                        <dd class="col-sm-9">{{ $request->name }}</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9"><a href="mailto:{{ $request->email }}">{{ $request->email }}</a></dd>

                        <dt class="col-sm-3">Teléfono</dt>
                        <dd class="col-sm-9">{{ $request->phone ?? '-' }}</dd>

                        <dt class="col-sm-3">Fecha</dt>
                        <dd class="col-sm-9">{{ $request->created_at->format('d/m/Y H:i') }}</dd>

                        @if ($request->message)
                            <dt class="col-sm-3">Mensaje</dt>
                            <dd class="col-sm-9">{{ $request->message }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            @if ($request->property)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Propiedad de interés</h5>
                    </div>
                    <div class="card-body">
                        <h6>{{ $request->property->title ?? $request->property->address ?? 'Propiedad' }}</h6>
                        <p class="text-muted mb-2">
                            {{ $request->property->address ?? '' }}
                            {{ $request->property->city ?? '' }}, {{ $request->property->state ?? '' }}
                        </p>
                        <strong class="text-success">${{ number_format($request->property->price ?? 0, 0) }}</strong>
                        <a href="{{ route('single.property', $request->property->id) }}" target="_blank" class="btn btn-sm btn-outline-primary ml-2">Ver propiedad</a>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Responder y cambiar estado</h5>
                </div>
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

                    <form method="POST" action="{{ route('admin.requests.update', $request) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{ old('status', $request->status) === 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="contacted" {{ old('status', $request->status) === 'contacted' ? 'selected' : '' }}>Contactado</option>
                                <option value="closed" {{ old('status', $request->status) === 'closed' ? 'selected' : '' }}>Cerrado</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="admin_response">Respuesta / Notas internas</label>
                            <textarea name="admin_response" id="admin_response" class="form-control" rows="5" placeholder="Escribe aquí la respuesta al solicitante o notas para el seguimiento...">{{ old('admin_response', $request->admin_response) }}</textarea>
                            <small class="form-text text-muted">Opcional. Puede usarse como respuesta al cliente o notas internas.</small>
                        </div>

                        @if ($request->responded_at)
                            <p class="small text-muted mb-2">Última respuesta: {{ $request->responded_at->format('d/m/Y H:i') }}</p>
                        @endif

                        <button type="submit" class="btn btn-primary btn-block">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
