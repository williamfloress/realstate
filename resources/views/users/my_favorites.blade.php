@extends('layouts.app')

@section('content')
@php $heroImage = asset('assets/images/hero_bg_2.jpg'); @endphp
<div class="site-blocks-cover inner-page-cover overlay" style="background-image: url('{{ $heroImage }}');" data-aos="fade" data-stellar-background-ratio="0.5">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-md-10">
                <h1 class="mb-2">Mis favoritos</h1>
            </div>
        </div>
    </div>
</div>

<div class="site-section">
    <div class="container">
        @if(session('success'))
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
        @forelse ($properties as $property)
            @php
                $offerLabel = match($property->offer_type ?? '') { 'sale' => 'Sale', 'rent' => 'Rent', 'lease' => 'Lease', default => 'Sale' };
                $cardImage = $property->image_url ?: asset('assets/images/img_1.jpg');
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="property-entry h-100">
                    <a href="{{ route('single.property', $property->id) }}" class="property-thumbnail">
                        <div class="offer-type-wrap">
                            <span class="offer-type {{ ($property->offer_type ?? '') === 'sale' ? 'bg-danger' : 'bg-success' }}">{{ $offerLabel }}</span>
                        </div>
                        <img src="{{ $cardImage }}" alt="{{ $property->title }}" class="img-fluid">
                    </a>
                    <div class="p-4 property-body">
                        <form action="{{ route('save.property') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="property_id" value="{{ $property->id }}">
                            <button type="submit" class="property-favorite active" style="border:none;cursor:pointer;" title="Quitar de favoritos">
                                <span class="icon-heart"></span>
                            </button>
                        </form>
                        <h2 class="property-title"><a href="{{ route('single.property', $property->id) }}">{{ $property->title ?? $property->address ?? 'Property' }}</a></h2>
                        <span class="property-location d-block mb-3"><span class="property-icon icon-room"></span> {{ $property->address ?? '' }} {{ $property->city ?? '' }}, {{ $property->state ?? '' }}</span>
                        <strong class="property-price text-primary mb-3 d-block text-success">${{ number_format($property->price ?? 0, 0) }}</strong>
                        <ul class="property-specs-wrap mb-3 mb-lg-0">
                            <li><span class="property-specs">Beds</span><span class="property-specs-number">{{ $property->beds ?? '-' }}</span></li>
                            <li><span class="property-specs">Baths</span><span class="property-specs-number">{{ $property->baths ?? '-' }}</span></li>
                            <li><span class="property-specs">SQ FT</span><span class="property-specs-number">{{ number_format($property->sqft ?? 0) }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="lead">No tienes propiedades guardadas en favoritos.</p>
                <a href="{{ route('properties.index') }}" class="btn btn-primary rounded-0">Ver propiedades</a>
            </div>
        @endforelse
        </div>
    </div>
</div>
@endsection
