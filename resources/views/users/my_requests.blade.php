@extends('layouts.app')

@section('content')
@php $heroImage = asset('assets/images/hero_bg_2.jpg'); @endphp
<div class="site-blocks-cover inner-page-cover overlay" style="background-image: url('{{ $heroImage }}');" data-aos="fade" data-stellar-background-ratio="0.5">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-md-10">
                <h1 class="mb-2">Mis solicitudes</h1>
            </div>
        </div>
    </div>
</div>

<div class="site-section">
    <div class="container">
        @forelse ($requests as $req)
            <div class="row mb-4" data-aos="fade-up">
                <div class="col-12">
                    <div class="property-entry h-100 d-block">
                        <div class="p-4 bg-white border">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <div class="mb-2">
                                    <h2 class="h5 mb-1">
                                        @if($req->property)
                                            <a href="{{ route('single.property', $req->property->id) }}">
                                                {{ $req->property->title ?? $req->property->address ?? 'Propiedad' }}
                                            </a>
                                        @else
                                            Propiedad no disponible
                                        @endif
                                    </h2>
                                    @if($req->property)
                                        <span class="property-location d-block text-muted mb-2">
                                            <span class="property-icon icon-room"></span>
                                            {{ $req->property->address ?? '' }} {{ $req->property->city ?? '' }}, {{ $req->property->state ?? '' }}
                                        </span>
                                        <strong class="text-success">${{ number_format($req->property->price ?? 0, 0) }}</strong>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @php
                                        $statusLabel = match($req->status ?? '') {
                                            'pending' => 'Pendiente',
                                            'contacted' => 'Contactado',
                                            'closed' => 'Cerrado',
                                            default => $req->status ?? 'Pendiente',
                                        };
                                        $statusClass = match($req->status ?? '') {
                                            'pending' => 'badge-warning',
                                            'contacted' => 'badge-info',
                                            'closed' => 'badge-secondary',
                                            default => 'badge-light',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                    <p class="small text-muted mb-0 mt-1">{{ $req->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @if($req->message)
                                <p class="mb-0 mt-2"><strong>Mensaje:</strong> {{ Str::limit($req->message, 150) }}</p>
                            @endif
                            @if($req->admin_response)
                                <div class="mt-2 p-2 bg-light rounded">
                                    <strong>Respuesta del agente:</strong>
                                    <p class="mb-0 mt-1">{{ $req->admin_response }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="row justify-content-center">
                <div class="col-md-8 text-center py-5">
                    <p class="lead">No tienes solicitudes de información.</p>
                    <a href="{{ route('properties.index') }}" class="btn btn-primary rounded-0">Ver propiedades</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
