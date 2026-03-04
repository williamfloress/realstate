@extends('layouts.app')

@section('content')

@php
    // Imagen de portada: usa la imagen de la propiedad o una por defecto
    $coverImage = ($singleProperty->image ?? null)
        ? asset('assets/images/' . $singleProperty->image)
        : asset('assets/images/hero_bg_2.jpg');
    // Etiqueta y clase según tipo de oferta (sale=rojo, rent/lease=verde)
    $offerLabel = match($singleProperty->offer_type ?? '') {
        'sale' => 'For Sale',
        'rent' => 'For Rent',
        'lease' => 'For Lease',
        default => 'Property Details',
    };
    $offerClass = ($singleProperty->offer_type ?? '') === 'sale' ? 'bg-danger' : 'bg-success';
@endphp

{{-- Hero: portada con título, precio y tipo de oferta --}}
<div class="site-blocks-cover inner-page-cover overlay" style="background-image: url('{{ $coverImage }}');" data-aos="fade" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row align-items-center justify-content-center text-center">
          <div class="col-md-10">
            <span class="d-inline-block text-white px-3 mb-3 property-offer-type rounded {{ $offerClass }}">{{ $offerLabel }}</span>
            <h1 class="mb-2">{{ $singleProperty->title ?? $singleProperty->address ?? 'Property' }}</h1>
            <p class="mb-5"><strong class="h2 text-success font-weight-bold">${{ number_format($singleProperty->price ?? 0, 0) }}</strong></p>
          </div>
        </div>
      </div>
    </div>  

    <div class="site-section site-section-sm">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            {{-- Carrusel de imágenes: principal + placeholders si no hay más --}}
            <div>
              <div class="slide-one-item home-slider owl-carousel">
                @foreach ($images as $image)
                <div><img src="{{ asset('assets/images/' . $image->path) }}" alt="{{ $singleProperty->title }}" class="img-fluid"></div>
                @endforeach
              </div>
            </div>
            {{-- Cuerpo: precio, especificaciones (beds, baths, sqft), tipo de hogar, año, precio/sqft --}}
            <div class="bg-white property-body border-bottom border-left border-right">
              <div class="row mb-5">
                <div class="col-md-6">
                  <strong class="text-success h1 mb-3">${{ number_format($singleProperty->price ?? 0, 0) }}</strong>
                </div>
                <div class="col-md-6">
                  <ul class="property-specs-wrap mb-3 mb-lg-0 float-lg-right">
                    <li><span class="property-specs">Beds</span><span class="property-specs-number">{{ $singleProperty->beds ?? '-' }}</span></li>
                    <li><span class="property-specs">Baths</span><span class="property-specs-number">{{ $singleProperty->baths ?? '-' }}</span></li>
                    <li><span class="property-specs">SQ FT</span><span class="property-specs-number">{{ number_format($singleProperty->sqft ?? 0) }}</span></li>
                  </ul>
                </div>
              </div>
              <div class="row mb-5">
                <div class="col-md-6 col-lg-4 text-center border-bottom border-top py-3">
                  <span class="d-inline-block text-black mb-0 caption-text">Home Type</span>
                  <strong class="d-block">{{ ucfirst($singleProperty->home_type ?? '-') }}</strong>
                </div>
                <div class="col-md-6 col-lg-4 text-center border-bottom border-top py-3">
                  <span class="d-inline-block text-black mb-0 caption-text">Year Built</span>
                  <strong class="d-block">{{ $singleProperty->year_built ?? '-' }}</strong>
                </div>
                <div class="col-md-6 col-lg-4 text-center border-bottom border-top py-3">
                  <span class="d-inline-block text-black mb-0 caption-text">Price/Sqft</span>
                  <strong class="d-block">${{ number_format($singleProperty->price_per_sqft ?? 0, 0) }}</strong>
                </div>
              </div>
              <h2 class="h4 text-black">More Info</h2>
              <p>{{ $singleProperty->description ?? 'No additional description available.' }}</p>

              {{-- Galería: imágenes de la propiedad (usa asset() para rutas correctas) --}}
              @php
                $galleryImages = ['img_1.jpg', 'img_2.jpg', 'img_3.jpg', 'img_4.jpg', 'img_5.jpg', 'img_6.jpg'];
                if ($singleProperty->image ?? null) {
                    array_unshift($galleryImages, $singleProperty->image);
                }
              @endphp
              <div class="row no-gutters mt-5">
                <div class="col-12">
                  <h2 class="h4 text-black mb-3">Gallery</h2>
                </div>
                @foreach (array_slice($galleryImages, 0, 8) as $img)
                  @php $imgPath = 'assets/images/' . $img; @endphp
                  <div class="col-sm-6 col-md-4 col-lg-3">
                    <a href="{{ asset($imgPath) }}" class="image-popup gal-item"><img src="{{ asset($imgPath) }}" alt="{{ $singleProperty->title }}" class="img-fluid"></a>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
          <div class="col-lg-4">

            {{-- Widget: formulario de solicitud/inquiry (envía a RequestsController) --}}
            <div class="bg-white widget border rounded">
              <h3 class="h4 text-black widget-title mb-3">Contact Agent</h3>
              @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
              @endif
              @if($errors->any())
                <div class="alert alert-danger mb-3">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <form action="{{ route('insert.request') }}" method="POST" class="form-contact-agent">
                @csrf
                <input type="hidden" name="property_id" value="{{ $singleProperty->id }}">
                <div class="form-group">
                  <label for="contact-name">Name</label>
                  <input type="text" id="contact-name" name="name" class="form-control" value="{{ old('name', auth()->user()?->name) }}" required>
                </div>
                <div class="form-group">
                  <label for="contact-email">Email</label>
                  <input type="email" id="contact-email" name="email" class="form-control" value="{{ old('email', auth()->user()?->email) }}" required>
                </div>
                <div class="form-group">
                  <label for="contact-phone">Phone</label>
                  <input type="text" id="contact-phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Optional">
                </div>
                <div class="form-group">
                  <label for="contact-message">Message</label>
                  <textarea id="contact-message" name="message" class="form-control" rows="4" placeholder="Tell us about your interest in this property...">{{ old('message') }}</textarea>
                </div>
                <div class="form-group">
                  <input type="submit" id="submit-contact" class="btn btn-primary" value="Send Request">
                </div>
              </form>
            </div>

            {{-- Widget: compartir en redes (URL actual de la página) --}}
            @php $shareUrl = url()->current(); $shareTitle = $singleProperty->title ?? 'Property'; @endphp
            <div class="bg-white widget border rounded">
              <h3 class="h4 text-black widget-title mb-3 ml-0">Share</h3>
              <div class="px-3" style="margin-left: -15px;">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}&quote={{ urlencode($shareTitle) }}" target="_blank" rel="noopener" class="pt-3 pb-3 pr-3 pl-0"><span class="icon-facebook"></span></a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($shareTitle) }}&url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="pt-3 pb-3 pr-3 pl-0"><span class="icon-twitter"></span></a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="pt-3 pb-3 pr-3 pl-0"><span class="icon-linkedin"></span></a>
              </div>
            </div>

          </div>
          
        </div>
      </div>
    </div>

    {{-- Propiedades relacionadas: misma ciudad, excluyendo la actual (máx. 3) --}}
    <div class="site-section site-section-sm bg-light">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="site-section-title mb-5">
              <h2>Related Properties</h2>
            </div>
          </div>
        </div>
        <div class="row mb-5">
          @forelse(($relatedProperties ?? collect()) as $property)
            @php
              $relOfferLabel = match($property->offer_type ?? '') { 'sale' => 'Sale', 'rent' => 'Rent', 'lease' => 'Lease', default => 'Sale' };
              $relOfferClass = ($property->offer_type ?? '') === 'sale' ? 'bg-danger' : 'bg-success';
              $relClass = ($property->offer_type ?? '') === 'lease' ? 'bg-info' : $relOfferClass;
              $relImage = $property->image ? asset('assets/images/' . $property->image) : asset('assets/images/img_1.jpg');
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
              <div class="property-entry h-100">
                <a href="{{ route('single.property', $property->id) }}" class="property-thumbnail">
                  <div class="offer-type-wrap">
                    <span class="offer-type {{ $relClass }}">{{ $relOfferLabel }}</span>
                  </div>
                  <img src="{{ $relImage }}" alt="{{ $property->title }}" class="img-fluid">
                </a>
                <div class="p-4 property-body">
                  <a href="#" class="property-favorite"><span class="icon-heart-o"></span></a>
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
            <div class="col-12">
              <p class="text-muted">No related properties found.</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>

@endsection