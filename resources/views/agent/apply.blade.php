@extends('layouts.app')

@section('content')
@php $heroImage = asset('assets/images/hero_bg_2.jpg'); @endphp
<div class="site-blocks-cover inner-page-cover overlay" style="background-image: url('{{ $heroImage }}');" data-aos="fade" data-stellar-background-ratio="0.5">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-md-10">
                <h1 class="mb-2">Trabaja con nosotros</h1>
                <p class="mb-0">Solicita ser agente inmobiliario</p>
            </div>
        </div>
    </div>
</div>

<div class="site-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white p-4 border rounded">
                    <h3 class="mb-4">Formulario de solicitud</h3>
                    <p class="text-muted mb-4">Completa el formulario y adjunta los documentos requeridos. Revisaremos tu solicitud y te notificaremos.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('agent.apply.store') }}" enctype="multipart/form-data">
                        @csrf

                        <h5 class="mb-3">Información personal</h5>
                        <div class="form-group">
                            <label for="phone">Teléfono *</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', Auth::user()->phone) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="license_number">Número de licencia / matrícula (bienes raíces)</label>
                            <input type="text" name="license_number" id="license_number" class="form-control" value="{{ old('license_number') }}">
                        </div>
                        <div class="form-group">
                            <label for="bio">Breve descripción profesional</label>
                            <textarea name="bio" id="bio" class="form-control" rows="3">{{ old('bio') }}</textarea>
                        </div>

                        <h5 class="mb-3 mt-4">Documentos requeridos</h5>
                        <div class="form-group">
                            <label for="real_estate_certificate">Certificado de bienes raíces *</label>
                            <input type="file" name="real_estate_certificate" id="real_estate_certificate" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="form-text text-muted">PDF, JPG o PNG. Máx. 5 MB.</small>
                        </div>
                        <div class="form-group">
                            <label for="id_document">Documento de identidad *</label>
                            <input type="file" name="id_document" id="id_document" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="form-text text-muted">PDF, JPG o PNG. Máx. 5 MB.</small>
                        </div>
                        <div class="form-group">
                            <label for="other_documents">Otros documentos (opcional)</label>
                            <input type="file" name="other_documents[]" id="other_documents" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" multiple>
                            <small class="form-text text-muted">Certificaciones adicionales, etc. Máx. 5 MB cada uno.</small>
                        </div>

                        <div class="form-group">
                            <label for="message">Mensaje adicional</label>
                            <textarea name="message" id="message" class="form-control" rows="4" placeholder="Cuéntanos por qué te gustaría unirte como agente...">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
