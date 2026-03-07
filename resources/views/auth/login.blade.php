@extends('layouts.app')

@section('content')
    <!-- Hero: imagen de fondo y título "Log In" (estilo template inmobiliario) -->
    <div
        class="site-blocks-cover inner-page-cover overlay"
        style="background-image: url('{{ asset('assets/images/hero_bg_2.jpg') }}');"
        data-aos="fade"
        data-stellar-background-ratio="0.5"
    >
        <div class="container">
            <div class="row align-items-center justify-content-center text-center">
                <div class="col-md-10">
                    <h1 class="mb-2">{{ __('messages.Log In') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de login: POST a route('login'), validación con $errors y old() -->
    <div class="site-section">
        <div class="container">
            <div class="row">
                <div
                    class="col-md-12"
                    data-aos="fade-up"
                    data-aos-delay="100"
                >
                    <h3 class="h4 text-black widget-title mb-3">{{ __('messages.Login') }}</h3>

                    <form
                        method="POST"
                        action="{{ route('login') }}"
                        class="form-contact-agent"
                    >
                        @csrf

                        <div class="form-group">
                            <label for="email">{{ __('messages.E-Mail Address') }}</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                            >
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('messages.Password') }}</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                required
                                autocomplete="current-password"
                            >
                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <!-- Remember Me: mantiene sesión; logout usa formulario oculto con @csrf -->
                        <div class="form-group">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="remember"
                                    id="remember"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="remember">
                                    {{ __('messages.Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <input
                                type="submit"
                                class="btn btn-primary"
                                value="{{ __('messages.Login') }}"
                            >
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('messages.Forgot Your Password?') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
