@extends('layouts.admin')


@section('content')
<div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5 col-xl-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title mt-5">Login Admin</h5>
                  @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                      {{ $errors->first() }}
                    </div>
                  @endif
                  <form method="POST" class="p-auto" action="{{ route('admin.login.submit') }}">
                      @csrf
                      <!-- Email input -->
                      <div class="form-outline mb-4">
                        <input type="email" name="email" id="form2Example1" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus />
                      </div>

                      <!-- Password input -->
                      <div class="form-outline mb-4">
                        <input type="password" name="password" id="form2Example2" placeholder="Password" class="form-control" required />
                      </div>

                      <!-- Remember me -->
                      <div class="form-check mb-4">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }} />
                        <label class="form-check-label" for="remember">Recordarme</label>
                      </div>

                      <!-- Submit button -->
                      <button type="submit" class="btn btn-primary mb-4 text-center">Login</button>
                    </form>

                </div>
          </div>
        </div>
        </div>

@endsection
