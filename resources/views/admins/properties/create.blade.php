@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.properties.index') }}">Propiedades</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nueva propiedad</li>
                </ol>
            </nav>
            <h4 class="mt-2 mb-0">Crear nueva propiedad</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
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

                    <form method="POST" action="{{ route('admin.properties.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">Título *</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ old('title') }}" required autofocus>
                                </div>

                                <div class="form-group">
                                    <label for="description">Descripción</label>
                                    <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                                </div>

                                <h6 class="mt-4 mb-3">Imágenes</h6>
                                <div class="form-group">
                                    <label for="images">Subir imágenes</label>
                                    <input type="file" name="images[]" id="images" class="form-control-file" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple>
                                    <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WebP. Máx. 5 MB por imagen. La primera imagen será la portada.</small>
                                </div>

                                <h6 class="mt-4 mb-3">Ubicación</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Dirección</label>
                                            <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city">Ciudad</label>
                                            <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="state">Estado/Provincia</label>
                                            <input type="text" name="state" id="state" class="form-control" value="{{ old('state') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="zip">Código postal</label>
                                            <input type="text" name="zip" id="zip" class="form-control" value="{{ old('zip') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="country">País</label>
                                            <input type="text" name="country" id="country" class="form-control" value="{{ old('country', 'US') }}" maxlength="2">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price">Precio *</label>
                                    <input type="number" name="price" id="price" class="form-control" step="0.01" min="0"
                                        value="{{ old('price') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="offer_type">Tipo de oferta *</label>
                                    <select name="offer_type" id="offer_type" class="form-control" required>
                                        <option value="sale" {{ old('offer_type') === 'sale' ? 'selected' : '' }}>Venta</option>
                                        <option value="rent" {{ old('offer_type') === 'rent' ? 'selected' : '' }}>Renta</option>
                                        <option value="lease" {{ old('offer_type') === 'lease' ? 'selected' : '' }}>Arrendamiento</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="home_type_id">Tipo de propiedad</label>
                                    <select name="home_type_id" id="home_type_id" class="form-control">
                                        <option value="">-- Seleccionar --</option>
                                        @foreach ($homeTypes as $ht)
                                            <option value="{{ $ht->id }}" {{ old('home_type_id') == $ht->id ? 'selected' : '' }}>
                                                {{ $ht->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($homeTypes->isEmpty())
                                        <small class="form-text text-warning">
                                            <a href="{{ route('admin.hometypes.create') }}">Crear tipos de propiedad</a> primero.
                                        </small>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="beds">Habitaciones</label>
                                            <input type="number" name="beds" id="beds" class="form-control" min="0" value="{{ old('beds') }}">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="baths">Baños</label>
                                            <input type="number" name="baths" id="baths" class="form-control" min="0" value="{{ old('baths') }}">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="sqft">m²</label>
                                            <input type="number" name="sqft" id="sqft" class="form-control" min="0" value="{{ old('sqft') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="year_built">Año de construcción</label>
                                    <input type="number" name="year_built" id="year_built" class="form-control"
                                        min="1800" max="{{ date('Y') + 1 }}" value="{{ old('year_built') }}">
                                </div>

                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Borrador</option>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Activa</option>
                                        <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Pausada</option>
                                        <option value="reserved" {{ old('status') === 'reserved' ? 'selected' : '' }}>Reservado</option>
                                        <option value="sold" {{ old('status') === 'sold' ? 'selected' : '' }}>Vendida</option>
                                        <option value="rented" {{ old('status') === 'rented' ? 'selected' : '' }}>Rentada</option>
                                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Cerrada</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear propiedad</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
