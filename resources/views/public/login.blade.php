@extends('feature-satu-form::public.layout')

@section('panel_title', 'Public Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            @if(session('error'))
                <div class="alert alert-danger border-0">{{ session('error') }}</div>
            @endif

            <p class="text-muted mb-4">Login dulu sebelum isi form. Akun guest akan otomatis mengisi data pengguna saat form dibuka.</p>

            <form method="POST" action="{{ route('public.login.submit') }}">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted text-uppercase" for="username">Username</label>
                    <input id="username" name="username" value="{{ old('username') }}" class="form-control @error('username') is-invalid @enderror" required autofocus>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted text-uppercase" for="password">Password</label>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-login-box-line me-1"></i> Login
                    </button>
                    <a href="{{ route('public.forms.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
