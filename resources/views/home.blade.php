@extends('layouts.app')

@section('content')
    <!-- Carrusel hero: slides dinámicos desde $properties. Si no hay propiedades, se muestra un slide por defecto. -->
    <div class="slide-one-item home-slider owl-carousel">
        @forelse ($properties as $property)
            @php
                // Clase según tipo de oferta (sale=rojo, rent/lease=verde)
                $offerClass = ($property->offer_type ?? '') === 'sale' ? 'bg-danger' : 'bg-success';
                $offerLabel = match($property->offer_type ?? '') {
                    'sale' => 'For Sale',
                    'rent' => 'For Rent',
                    'lease' => 'For Lease',
                    default => 'For Sale',
                };
                $bgImage = $property->image ? asset('assets/images/' . $property->image) : asset('assets/images/hero_bg_1.jpg');
            @endphp
            <div class="site-blocks-cover overlay" style="background-image: url('{{ $bgImage }}');" data-aos="fade" data-stellar-background-ratio="0.5">
                <div class="container">
                    <div class="row align-items-center justify-content-center text-center">
                        <div class="col-md-10">
                            <span class="d-inline-block {{ $offerClass }} text-white px-3 mb-3 property-offer-type rounded">{{ $offerLabel }}</span>
                            <h1 class="mb-2">{{ $property->title ?? $property->address ?? 'Property' }}</h1>
                            <p class="mb-5"><strong class="h2 text-success font-weight-bold">${{ number_format($property->price ?? 0, 0) }}</strong></p>
                            <p><a href="{{route('single.property', $property->id)}}" class="btn btn-white btn-outline-white py-3 px-5 rounded-0 btn-2">See Details</a></p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Slide por defecto cuando no hay propiedades en BD -->
            <div class="site-blocks-cover overlay" style="background-image: url('{{ asset('assets/images/hero_bg_1.jpg') }}');" data-aos="fade" data-stellar-background-ratio="0.5">
                <div class="container">
                    <div class="row align-items-center justify-content-center text-center">
                        <div class="col-md-10">
                            <span class="d-inline-block bg-success text-white px-3 mb-3 property-offer-type rounded">For Rent</span>
                            <h1 class="mb-2">Welcome</h1>
                            <p class="mb-5"><strong class="h2 text-success font-weight-bold">Find your dream property</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Sección de búsqueda: formulario con tipo de listado, oferta (venta/renta) y ciudad. Maqueta sin action. -->
    <div class="site-section site-section-sm pb-0">
        <div class="container">
            <div class="row">
                <form class="form-search col-md-12" style="margin-top: -100px;">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="list-types">Listing Types</label>
                            <div class="select-wrap">
                                <span class="icon icon-arrow_drop_down"></span>
                                <select name="list-types" id="list-types" class="form-control d-block rounded-0">
                                    <option value="">Condo</option>
                                    <option value="">Commercial Building</option>
                                    <option value="">Land Property</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="offer-types">Offer Type</label>
                            <div class="select-wrap">
                                <span class="icon icon-arrow_drop_down"></span>
                                <select name="offer-types" id="offer-types" class="form-control d-block rounded-0">
                                    <option value="">For Sale</option>
                                    <option value="">For Rent</option>
                                    <option value="">For Lease</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="select-city">Select City</label>
                            <div class="select-wrap">
                                <span class="icon icon-arrow_drop_down"></span>
                                <select name="select-city" id="select-city" class="form-control d-block rounded-0">
                                    <option value="">New York</option>
                                    <option value="">Brooklyn</option>
                                    <option value="">London</option>
                                    <option value="">Japan</option>
                                    <option value="">Philippines</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="submit" class="btn btn-success text-white btn-block rounded-0" value="Search">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sección de listado: barra de vista (módulo/lista), filtros All/Rent/Sale, orden y tarjetas de propiedades. -->
    <div class="site-section">
        <div class="container">
            <!-- Barra de opciones de vista y orden (Sort by) -->
            <div class="row">
                <div class="col-md-12">
                    <div class="view-options bg-white py-3 px-3 d-md-flex align-items-center">
                        <div class="mr-auto">
                            <a href="{{ url('/') }}" class="icon-view view-module active"><span class="icon-view_module"></span></a>
                            <a href="{{ url('/properties') }}" class="icon-view view-list"><span class="icon-view_list"></span></a>
                        </div>
                        <div class="ml-auto d-flex align-items-center">
                            <div>
                                <a href="#" class="view-list px-3 border-right active">All</a>
                                <a href="#" class="view-list px-3 border-right">Rent</a>
                                <a href="#" class="view-list px-3">Sale</a>
                            </div>
                            <div class="select-wrap">
                                <span class="icon icon-arrow_drop_down"></span>
                                <select class="form-control form-control-sm d-block rounded-0">
                                    <option value="">Sort by</option>
                                    <option value="">Price Ascending</option>
                                    <option value="">Price Descending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de tarjetas: bucle dinámico desde $properties -->
            <div class="row">
                @foreach ($properties as $property)
                    @php
                        $offerLabel = match($property->offer_type ?? '') { 'sale' => 'Sale', 'rent' => 'Rent', 'lease' => 'Lease', default => 'Sale' };
                        $cardImage = $property->image ? asset('assets/images/' . $property->image) : asset('assets/images/img_1.jpg');
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
                                <a href="#" class="property-favorite"><span class="icon-heart-o"></span></a>
                                <h2 class="property-title"><a href="#">{{ $property->title ?? $property->address ?? 'Property' }}</a></h2>
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
                @endforeach
            </div>
        </div>
    </div>

    <!-- Sección "Why Choose Us?": título, texto y tres servicios (Research Suburbs, Sold Houses, Security Priority) -->
    <div class="site-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 text-center">
                    <div class="site-section-title">
                        <h2>Why Choose Us?</h2>
                    </div>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis maiores quisquam saepe architecto error corporis aliquam. Cum ipsam a consectetur aut sunt sint animi, pariatur corporis, eaque, deleniti cupiditate officia.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="service text-center">
                        <span class="icon flaticon-house"></span>
                        <h2 class="service-heading">Research Suburbs</h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt iure qui natus perspiciatis ex odio molestia.</p>
                        <p><span class="read-more">Read More</span></p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="service text-center">
                        <span class="icon flaticon-sold"></span>
                        <h2 class="service-heading">Sold Houses</h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt iure qui natus perspiciatis ex odio molestia.</p>
                        <p><span class="read-more">Read More</span></p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="service text-center">
                        <span class="icon flaticon-camera"></span>
                        <h2 class="service-heading">Security Priority</h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt iure qui natus perspiciatis ex odio molestia.</p>
                        <p><span class="read-more">Read More</span></p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección "Our Agents": tarjetas de agentes con foto, nombre, cargo, descripción y redes sociales. -->
    <div class="site-section bg-light">
        <div class="container">
            <div class="row mb-5 justify-content-center">
                <div class="col-md-7">
                    <div class="site-section-title text-center">
                        <h2>Our Agents</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero magnam officiis ipsa eum pariatur labore fugit amet eaque iure vitae, repellendus laborum in modi reiciendis quis! Optio minima quibusdam, laboriosam.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-4 mb-5 mb-lg-5">
                    <div class="team-member">
                        <img src="{{ asset('assets/images/person_1.jpg') }}" alt="Image" class="img-fluid rounded mb-4">
                        <div class="text">
                            <h2 class="mb-2 font-weight-light text-black h4">Megan Smith</h2>
                            <span class="d-block mb-3 text-white-opacity-05">Real Estate Agent</span>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Modi dolorem totam non quis facere blanditiis praesentium est. Totam atque corporis nisi, veniam non. Tempore cupiditate, vitae minus obcaecati provident beatae!</p>
                            <p>
                                <a href="#" class="text-black p-2"><span class="icon-facebook"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-twitter"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-linkedin"></span></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-5 mb-lg-5">
                    <div class="team-member">
                        <img src="{{ asset('assets/images/person_2.jpg') }}" alt="Image" class="img-fluid rounded mb-4">
                        <div class="text">
                            <h2 class="mb-2 font-weight-light text-black h4">Brooke Cagle</h2>
                            <span class="d-block mb-3 text-white-opacity-05">Real Estate Agent</span>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis, cumque vitae voluptates culpa earum similique corrupti itaque veniam doloribus amet perspiciatis recusandae sequi nihil tenetur ad, modi quos id magni!</p>
                            <p>
                                <a href="#" class="text-black p-2"><span class="icon-facebook"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-twitter"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-linkedin"></span></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-5 mb-lg-5">
                    <div class="team-member">
                        <img src="{{ asset('assets/images/person_3.jpg') }}" alt="Image" class="img-fluid rounded mb-4">
                        <div class="text">
                            <h2 class="mb-2 font-weight-light text-black h4">Philip Martin</h2>
                            <span class="d-block mb-3 text-white-opacity-05">Real Estate Agent</span>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maiores illo iusto, inventore, iure dolorum officiis modi repellat nobis, praesentium perspiciatis, explicabo. Atque cupiditate, voluptates pariatur odit officia libero veniam quo.</p>
                            <p>
                                <a href="#" class="text-black p-2"><span class="icon-facebook"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-twitter"></span></a>
                                <a href="#" class="text-black p-2"><span class="icon-linkedin"></span></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
