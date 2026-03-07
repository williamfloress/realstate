@extends('layouts.app')

@section('content')
    <!-- Carrusel hero: slides dinámicos desde $properties. Si no hay propiedades, se muestra un slide por defecto. -->
    <div class="slide-one-item home-slider owl-carousel">
        @forelse ($properties as $property)
            @php
                // Clase según tipo de oferta (sale=rojo, rent/lease=verde)
                $offerClass = ($property->offer_type ?? '') === 'sale' ? 'bg-danger' : 'bg-success';
                $offerLabel = match($property->offer_type ?? '') {
                    'sale' => __('messages.For Sale'),
                    'rent' => __('messages.For Rent'),
                    'lease' => __('messages.For Lease'),
                    default => __('messages.For Sale'),
                };
                $bgImage = $property->image_url ?: asset('assets/images/hero_bg_1.jpg');
            @endphp
            <div class="site-blocks-cover" style="background-image: url('{{ $bgImage }}');" data-aos="fade" data-stellar-background-ratio="0.5">
                <div class="container">
                    <div class="row align-items-center justify-content-center text-center">
                        <div class="col-md-10">
                            <span class="d-inline-block {{ $offerClass }} text-white px-3 mb-3 property-offer-type rounded">{{ $offerLabel }}</span>
                            <h1 class="mb-2">{{ $property->title ?? $property->address ?? __('messages.Property') }}</h1>
                            <p class="mb-5"><strong class="h2 text-success font-weight-bold">${{ number_format($property->price ?? 0, 0) }}</strong></p>
                            <p><a href="{{route('single.property', $property->id)}}" class="btn btn-white btn-outline-white py-3 px-5 rounded-0 btn-2">{{ __('messages.See Details') }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="site-blocks-cover" style="background-image: url('{{ asset('assets/images/hero_bg_1.jpg') }}');" data-aos="fade" data-stellar-background-ratio="0.5">
                <div class="container">
                    <div class="row align-items-center justify-content-center text-center">
                        <div class="col-md-10">
                            <h1 class="mb-2" style="font-family:'Playfair Display',serif;">Cruza el umbral hacia tu próximo hogar</h1>
                            <p class="mb-5"><strong class="h2 font-weight-bold" style="color:#D4A373;">¿Dónde quieres empezar tu historia?</strong></p>
                            <p><a href="{{ route('properties.index') }}" class="btn btn-white btn-outline-white py-3 px-5 rounded-0 btn-2">Ver Propiedades</a></p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Sección de búsqueda: formulario con tipo de listado, oferta (venta/renta) y ciudad. -->
    <div class="site-section site-section-sm pb-0">
        <div class="container">
            <div class="row">
                <form class="form-search col-md-12" style="margin-top: -100px;" action="{{ route('home') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="list-types">{{ __('messages.Listing Types') }}</label>
                            <div class="select-wrap">
                                <span class="icon icon-arrow_drop_down select-dropdown-trigger" data-target="list-types" role="button" tabindex="0" aria-label="Abrir menú"></span>
                                <select name="list-types" id="list-types" class="form-control d-block rounded-0">
                                    <option value="">{{ __('messages.All Types') }}</option>
                                    @foreach ($homeTypes ?? [] as $ht)
                                        <option value="{{ $ht->home_type }}" {{ request('list-types') === $ht->home_type ? 'selected' : '' }}>{{ $ht->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="offer-types">{{ __('messages.Offer Type') }}</label>
                            <div class="select-wrap">
                                <span class="icon icon-arrow_drop_down select-dropdown-trigger" data-target="offer-types" role="button" tabindex="0" aria-label="Abrir menú"></span>
                                <select name="offer-types" id="offer-types" class="form-control d-block rounded-0">
                                    <option value="">{{ __('messages.All') }}</option>
                                    <option value="sale" {{ request('offer-types') === 'sale' ? 'selected' : '' }}>{{ __('messages.For Sale') }}</option>
                                    <option value="rent" {{ request('offer-types') === 'rent' ? 'selected' : '' }}>{{ __('messages.For Rent') }}</option>
                                    <option value="lease" {{ request('offer-types') === 'lease' ? 'selected' : '' }}>{{ __('messages.For Lease') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="select-city">{{ __('messages.Select City') }}</label>
                            <div class="select-wrap">
                                <span class="icon icon-arrow_drop_down select-dropdown-trigger" data-target="select-city" role="button" tabindex="0" aria-label="Abrir menú"></span>
                                <select name="select-city" id="select-city" class="form-control d-block rounded-0">
                                    <option value="">{{ __('messages.All Cities') }}</option>
                                    @foreach ($cities ?? [] as $city)
                                        <option value="{{ $city }}" {{ request('select-city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="submit" class="btn btn-success text-white btn-block rounded-0" value="{{ __('messages.Search') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sección de listado: barra de vista (módulo/lista), filtros All/Rent/Sale, orden y tarjetas de propiedades. -->
    <div class="site-section">
        <div class="container">
            {{-- Mensaje de éxito al guardar propiedad (visible junto a las tarjetas) --}}
            @if(session('success'))
              <div class="row">
                <div class="col-12 mb-3">
                  <div class="alert alert-success alert-dismissible fade show" role="alert" style="position:relative;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    {{ session('success') }}
                  </div>
                </div>
              </div>
            @endif
            <!-- Barra de opciones de vista y orden (Sort by) -->
            <div class="row">
                <div class="col-md-12">
                    <div class="view-options bg-white py-3 px-3 d-md-flex align-items-center">
                        <div class="mr-auto">
                            <a href="{{ route('home') }}" class="icon-view view-module active"><span class="icon-view_module"></span></a>
                            <a href="{{ route('properties.index') }}" class="icon-view view-list"><span class="icon-view_list"></span></a>
                        </div>
                        <div class="ml-auto d-flex align-items-center">
                            <div>
                                <a href="{{ route('properties.index') }}" class="view-list px-3 border-right {{ request()->is('properties') && !request()->is('properties/*') ? 'active' : '' }}">{{ __('messages.All') }}</a>
                                <a href="{{ route('properties.byType', 'rent') }}" class="view-list px-3 border-right {{ request()->is('rent') ? 'active' : '' }}">{{ __('messages.Rent') }}</a>
                                <a href="{{ route('properties.byType', 'buy') }}" class="view-list px-3 border-right {{ request()->is('buy') ? 'active' : '' }}">{{ __('messages.Sale') }}</a>
                                <a href="{{ route('price.asc.properties') }}" class="view-list px-3 border-right {{ request()->is('properties/price-asc') ? 'active' : '' }}">{{ __('messages.Price ↑') }}</a>
                                <a href="{{ route('price.desc.properties') }}" class="view-list px-3 {{ request()->is('properties/price-desc') ? 'active' : '' }}">{{ __('messages.Price ↓') }}</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de tarjetas: bucle dinámico desde $properties -->
            <div class="row">
                @foreach ($properties as $property)
                    @php
                        $offerLabel = match($property->offer_type ?? '') { 'sale' => __('messages.Sale'), 'rent' => __('messages.Rent'), 'lease' => __('messages.Lease'), default => __('messages.Sale') };
                        $cardImage = $property->image_url ?: asset('assets/images/img_1.jpg');
                    @endphp
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="property-entry h-100">
                            <a href="{{route('single.property', $property->id)}}" class="property-thumbnail">
                                <div class="offer-type-wrap">
                                    <span class="offer-type {{ ($property->offer_type ?? '') === 'sale' ? 'bg-danger' : 'bg-success' }}">{{ $offerLabel }}</span>
                                </div>
                                <img src="{{ $cardImage }}" alt="{{ $property->title }}" class="img-fluid">
                            </a>
                            <div class="p-4 property-body">
                                @auth
                                    <form action="{{ route('save.property') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                                        <button type="submit" class="property-favorite {{ $savedPropertyIds->contains($property->id) ? 'active' : '' }}" style="border:none;cursor:pointer;" title="{{ $savedPropertyIds->contains($property->id) ? __('messages.Remove from favorites') : __('messages.Save to favorites') }}">
                                            <span class="{{ $savedPropertyIds->contains($property->id) ? 'icon-heart' : 'icon-heart-o' }}"></span>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="property-favorite" title="{{ __('messages.Sign in to save') }}">
                                        <span class="icon-heart-o"></span>
                                    </a>
                                @endauth
                                <h2 class="property-title"><a href="{{ route('single.property', $property->id) }}">{{ $property->title ?? $property->address ?? __('messages.Property') }}</a></h2>
                                <span class="property-location d-block mb-3"><span class="property-icon icon-room"></span> {{ $property->address ?? '' }} {{ $property->city ?? '' }}, {{ $property->state ?? '' }}</span>
                                <strong class="property-price text-primary mb-3 d-block text-success">${{ number_format($property->price ?? 0, 0) }}</strong>
                                <ul class="property-specs-wrap mb-3 mb-lg-0">
                                    <li><span class="property-specs">{{ __('messages.Beds') }}</span><span class="property-specs-number">{{ $property->beds ?? '-' }}</span></li>
                                    <li><span class="property-specs">{{ __('messages.Baths') }}</span><span class="property-specs-number">{{ $property->baths ?? '-' }}</span></li>
                                    <li><span class="property-specs">{{ __('messages.SQ FT') }}</span><span class="property-specs-number">{{ number_format($property->sqft ?? 0) }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Sección "¿Por qué Umbral?" -->
    <div class="site-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 text-center">
                    <div class="site-section-title">
                        <h2>{{ __('messages.Why Choose Us?') }}</h2>
                    </div>
                    <p>En Umbral transformamos la búsqueda de tu hogar en una experiencia cercana y transparente. Con un equipo local que conoce cada rincón de Caracas, te acompañamos desde la primera visita hasta la firma.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="service text-center">
                        <span class="icon flaticon-house"></span>
                        <h2 class="service-heading">{{ __('messages.Research Suburbs') }}</h2>
                        <p>Analizamos cada sector de Caracas — desde Chacao hasta El Hatillo — para que tomes la mejor decisión según tu estilo de vida.</p>
                        <p><span class="read-more">{{ __('messages.Read More') }}</span></p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="service text-center">
                        <span class="icon flaticon-sold"></span>
                        <h2 class="service-heading">{{ __('messages.Sold Houses') }}</h2>
                        <p>Más de 200 familias venezolanas ya cruzaron el umbral con nosotros. Cada transacción cerrada es una historia de confianza.</p>
                        <p><span class="read-more">{{ __('messages.Read More') }}</span></p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="service text-center">
                        <span class="icon flaticon-camera"></span>
                        <h2 class="service-heading">{{ __('messages.Security Priority') }}</h2>
                        <p>Verificamos cada propiedad, documentación legal y antecedentes del inmueble para que inviertas con total tranquilidad.</p>
                        <p><span class="read-more">{{ __('messages.Read More') }}</span></p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección "Nuestros Agentes" -->
    <div class="site-section bg-light">
        <div class="container">
            <div class="row mb-5 justify-content-center">
                <div class="col-md-7">
                    <div class="site-section-title text-center">
                        <h2>{{ __('messages.Our Agents') }}</h2>
                        <p>Nuestro equipo de agentes conoce Caracas como la palma de su mano. Con experiencia local y compromiso genuino, te guiamos para encontrar el espacio perfecto para tu próxima etapa.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-4 mb-5 mb-lg-5">
                    <div class="team-member">
                        <img src="{{ asset('assets/images/person_4.jpg') }}" alt="María Alejandra Rodríguez" class="img-fluid rounded mb-4">
                        <div class="text">
                            <h2 class="mb-2 font-weight-light text-black h4">María Alejandra Rodríguez</h2>
                            <span class="d-block mb-3 text-white-opacity-05">{{ __('messages.Real Estate Agent') }}</span>
                            <p>Especialista en propiedades premium en Chacao y Valle Arriba. Con más de 8 años de experiencia en el mercado inmobiliario caraqueño, María Alejandra se distingue por su atención personalizada y conocimiento profundo de las zonas más exclusivas de la ciudad.</p>
                            <p>
                                <a href="#" class="text-black p-2"><span class="icon-facebook"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-twitter"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-instagram"></span></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-5 mb-lg-5">
                    <div class="team-member">
                        <img src="{{ asset('assets/images/person_2.jpg') }}" alt="Carlos Eduardo Mendoza" class="img-fluid rounded mb-4">
                        <div class="text">
                            <h2 class="mb-2 font-weight-light text-black h4">Carlos Eduardo Mendoza</h2>
                            <span class="d-block mb-3 text-white-opacity-05">{{ __('messages.Real Estate Agent') }}</span>
                            <p>Experto en inmuebles familiares y estudios modernos. Carlos conecta a jóvenes profesionales y familias con los espacios que mejor se adaptan a su ritmo de vida en Los Palos Grandes, La Tahona y El Hatillo.</p>
                            <p>
                                <a href="#" class="text-black p-2"><span class="icon-facebook"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-twitter"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-instagram"></span></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-5 mb-lg-5">
                    <div class="team-member">
                        <img src="{{ asset('assets/images/person_5.jpg') }}" alt="Valentina Torres García" class="img-fluid rounded mb-4">
                        <div class="text">
                            <h2 class="mb-2 font-weight-light text-black h4">Valentina Torres García</h2>
                            <span class="d-block mb-3 text-white-opacity-05">{{ __('messages.Real Estate Agent') }}</span>
                            <p>Apasionada por la arquitectura y el diseño, Valentina se especializa en propiedades de inversión en Altamira y Las Mercedes. Su visión estratégica y carisma la convierten en la aliada perfecta para quienes buscan rentabilidad y estilo.</p>
                            <p>
                                <a href="#" class="text-black p-2"><span class="icon-facebook"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-twitter"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-instagram"></span></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
