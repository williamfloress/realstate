<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Umbral — Panel de Agente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('assets/vendor/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/styles/styles.css') }}" rel="stylesheet">
    @stack('head')
    <script src="{{ asset('assets/vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap.min.js') }}"></script>
</head>
<body class="admin-logged-in">
<div id="wrapper">
    <nav class="navbar header-top fixed-top navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}" style="font-family:'Playfair Display',serif;font-weight:700;">Umbral<span style="color:#D4A373;">.</span></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav side-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white" style="margin-left: 20px;" href="{{ route('agent.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('agent.properties.index') }}" style="margin-left: 20px;">Propiedades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('amc.index') }}" style="margin-left: 20px;">AMC</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('agent.requests.index') }}" style="margin-left: 20px;">Solicitudes</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-md-auto d-md-flex">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Sitio web</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('agent.dashboard') }}">Dashboard</a>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar sesión</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <main>
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
