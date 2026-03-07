@extends('layouts.app')

@section('content')
@php $heroImage = asset('assets/images/hero_bg_2.jpg'); @endphp
<div class="site-blocks-cover inner-page-cover overlay" style="background-image: url('{{ $heroImage }}');" data-aos="fade" data-stellar-background-ratio="0.5">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-md-10">
                <h1 class="mb-2">Estado de tu solicitud</h1>
            </div>
        </div>
    </div>
</div>

<div class="site-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="bg-white p-4 border rounded text-center">
                    @if ($application->status === 'pending')
                        <span class="badge badge-warning badge-lg p-3 mb-3">En revisión</span>
                        <p class="lead">Tu solicitud para ser agente está siendo revisada por nuestro equipo.</p>
                        <p class="text-muted">Te notificaremos por email cuando tengamos una respuesta.</p>
                    @elseif ($application->status === 'approved')
                        <span class="badge badge-success badge-lg p-3 mb-3">Aprobada</span>
                        <p class="lead">¡Felicidades! Tu solicitud ha sido aprobada.</p>
                        <a href="{{ route('agent.dashboard') }}" class="btn btn-primary mt-3">Ir al panel de agente</a>
                    @else
                        <span class="badge badge-danger badge-lg p-3 mb-3">Rechazada</span>
                        <p class="lead">Lamentablemente tu solicitud no fue aprobada.</p>
                        @if ($application->admin_notes)
                            <div class="alert alert-light mt-3 text-left">
                                <strong>Notas:</strong> {{ $application->admin_notes }}
                            </div>
                        @endif
                        <a href="{{ route('agent.apply') }}" class="btn btn-outline-primary mt-3">Volver a solicitar</a>
                    @endif
                    <hr class="my-4">
                    <a href="{{ route('home') }}" class="btn btn-link">Volver al inicio</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
