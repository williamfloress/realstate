<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- This file has been downloaded from Bootsnipp.com. Enjoy! -->
    <title>Umbral — Panel Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('assets/vendor/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/styles/styles.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap.min.js') }}"></script>
</head>
<body class="{{ auth()->guard('admin')->check() ? 'admin-logged-in' : 'admin-guest' }}">
<div id="wrapper">
    <nav class="navbar header-top fixed-top navbar-expand-lg  navbar-dark bg-dark">
      <div class="container">
      <a class="navbar-brand" href="#" style="font-family:'Playfair Display',serif;font-weight:700;">Umbral<span style="color:#D4A373;">.</span></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarText">
        @auth('admin')
        <ul class="navbar-nav side-nav" >
          <li class="nav-item">
            <a class="nav-link text-white" style="margin-left: 20px;" href="{{ route('admin.dashboard') }}">Home
              <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.index') }}" style="margin-left: 20px;">Admins</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.hometypes.index') }}" style="margin-left: 20px;">Hometypes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.sectores.index') }}" style="margin-left: 20px;">Sectores (AMC)</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.properties.index') }}" style="margin-left: 20px;">Properties</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.requests.index') }}" style="margin-left: 20px;">Requests</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.agent-applications.index') }}" style="margin-left: 20px;">Solicitudes de agentes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('amc.index') }}" style="margin-left: 20px;">AMC</a>
          </li>
        </ul>
        @endauth
        <ul class="navbar-nav ml-md-auto d-md-flex">
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/') }}">Home
              <span class="sr-only">(current)</span>
            </a>
          </li>
          @auth('admin')
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ Auth::guard('admin')->user()->name }}
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a>
              <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
              </form>
            </div>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.login') }}">Login</a>
          </li>
          @endauth
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
<script type="text/javascript">

</script>
</body>
</html>