@extends('layouts.agent')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush

@section('content')
<div class="dashboard-content">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('agent.properties.index') }}">Mis propiedades</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar propiedad</li>
                </ol>
            </nav>
            <h4 class="mt-2 mb-0">Editar propiedad</h4>
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

                    <form method="POST" action="{{ route('agent.properties.update', $property) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">Título *</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ old('title', $property->title) }}" required autofocus>
                                </div>

                                <div class="form-group">
                                    <label for="description">Descripción</label>
                                    <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $property->description) }}</textarea>
                                </div>

                                <h6 class="mt-4 mb-3">Imágenes</h6>
                                @if ($property->images->isNotEmpty())
                                    <div class="mb-3">
                                        <small class="d-block text-muted mb-2">Imágenes actuales:</small>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($property->images as $img)
                                                <img src="{{ $img->url }}" alt="" class="rounded" style="height: 60px; width: auto;">
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="images">Añadir más imágenes</label>
                                    <input type="file" name="images[]" id="images" class="form-control-file" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple>
                                    <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WebP. Máx. 5 MB por imagen.</small>
                                </div>

                                <h6 class="mt-4 mb-3">Ubicación</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Dirección</label>
                                            <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $property->address) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city">Ciudad</label>
                                            <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $property->city) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="state">Estado/Provincia</label>
                                            <input type="text" name="state" id="state" class="form-control" value="{{ old('state', $property->state) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="zip">Código postal</label>
                                            <input type="text" name="zip" id="zip" class="form-control" value="{{ old('zip', $property->zip) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="country">País</label>
                                            <input type="text" name="country" id="country" class="form-control" value="{{ old('country', $property->country ?? 'US') }}" maxlength="2">
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mt-4 mb-3">Mapa - Ubicación del pin</h6>
                                <p class="text-muted small">Haz clic en el mapa para colocar el pin de la propiedad.</p>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $property->latitude) }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $property->longitude) }}">
                                <div id="property-map" style="height: 350px; border-radius: 8px; border: 1px solid #ddd;"></div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price">Precio *</label>
                                    <input type="number" name="price" id="price" class="form-control" step="0.01" min="0"
                                        value="{{ old('price', $property->price) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="offer_type">Tipo de oferta *</label>
                                    <select name="offer_type" id="offer_type" class="form-control" required>
                                        <option value="sale" {{ old('offer_type', $property->offer_type) === 'sale' ? 'selected' : '' }}>Venta</option>
                                        <option value="rent" {{ old('offer_type', $property->offer_type) === 'rent' ? 'selected' : '' }}>Renta</option>
                                        <option value="lease" {{ old('offer_type', $property->offer_type) === 'lease' ? 'selected' : '' }}>Arrendamiento</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="home_type_id">Tipo de propiedad</label>
                                    <select name="home_type_id" id="home_type_id" class="form-control">
                                        <option value="">-- Seleccionar --</option>
                                        @foreach ($homeTypes as $ht)
                                            <option value="{{ $ht->id }}" {{ old('home_type_id', $property->home_type_id) == $ht->id ? 'selected' : '' }}>
                                                {{ $ht->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="sector_id">Sector (AMC)</label>
                                    <select name="sector_id" id="sector_id" class="form-control">
                                        <option value="">-- Seleccionar --</option>
                                        @foreach ($sectores as $s)
                                            <option value="{{ $s->id }}" {{ old('sector_id', $property->sector_id) == $s->id ? 'selected' : '' }}>
                                                {{ $s->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="beds">Habitaciones</label>
                                            <input type="number" name="beds" id="beds" class="form-control" min="0" value="{{ old('beds', $property->beds) }}">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="baths">Baños</label>
                                            <input type="number" name="baths" id="baths" class="form-control" min="0" value="{{ old('baths', $property->baths) }}">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="parqueos">Parqueos</label>
                                            <input type="number" name="parqueos" id="parqueos" class="form-control" min="0" value="{{ old('parqueos', $property->parqueos) }}">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="sqft">m²</label>
                                            <input type="number" name="sqft" id="sqft" class="form-control" min="0" value="{{ old('sqft', $property->sqft) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="area_construccion_m2">Área construcción m² (AMC)</label>
                                    <input type="number" name="area_construccion_m2" id="area_construccion_m2" class="form-control" step="0.01" min="0" value="{{ old('area_construccion_m2', $property->area_construccion_m2) }}" placeholder="Para análisis comparativo">
                                </div>

                                <h6 class="mt-3 mb-2">Acabados (AMC)</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="acabado_piso_id">Piso</label>
                                            <select name="acabado_piso_id" id="acabado_piso_id" class="form-control">
                                                <option value="">-- Seleccionar --</option>
                                                @foreach ($acabadosPiso as $a)
                                                    <option value="{{ $a->id }}" {{ old('acabado_piso_id', $property->acabado_piso_id) == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="acabado_cocina_id">Cocina</label>
                                            <select name="acabado_cocina_id" id="acabado_cocina_id" class="form-control">
                                                <option value="">-- Seleccionar --</option>
                                                @foreach ($acabadosCocina as $a)
                                                    <option value="{{ $a->id }}" {{ old('acabado_cocina_id', $property->acabado_cocina_id) == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="acabado_bano_id">Baño</label>
                                            <select name="acabado_bano_id" id="acabado_bano_id" class="form-control">
                                                <option value="">-- Seleccionar --</option>
                                                @foreach ($acabadosBano as $a)
                                                    <option value="{{ $a->id }}" {{ old('acabado_bano_id', $property->acabado_bano_id) == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="year_built">Año de construcción</label>
                                    <input type="number" name="year_built" id="year_built" class="form-control"
                                        min="1800" max="{{ date('Y') + 1 }}" value="{{ old('year_built', $property->year_built) }}">
                                </div>

                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="draft" {{ old('status', $property->status) === 'draft' ? 'selected' : '' }}>Borrador</option>
                                        <option value="active" {{ old('status', $property->status) === 'active' ? 'selected' : '' }}>Activa</option>
                                        <option value="paused" {{ old('status', $property->status) === 'paused' ? 'selected' : '' }}>Pausada</option>
                                        <option value="reserved" {{ old('status', $property->status) === 'reserved' ? 'selected' : '' }}>Reservado</option>
                                        <option value="sold" {{ old('status', $property->status) === 'sold' ? 'selected' : '' }}>Vendida</option>
                                        <option value="rented" {{ old('status', $property->status) === 'rented' ? 'selected' : '' }}>Rentada</option>
                                        <option value="closed" {{ old('status', $property->status) === 'closed' ? 'selected' : '' }}>Cerrada</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('agent.properties.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var lat = {{ $property->latitude ?? 10.4961 }};
    var lng = {{ $property->longitude ?? -66.8533 }};
    var map = L.map('property-map').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    var marker = L.marker([lat, lng]).addTo(map);
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitude').value = e.latlng.lat.toFixed(7);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(7);
    });
});
</script>
@endpush
