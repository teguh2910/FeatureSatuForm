@extends('feature-satu-form::public.layout')

@section('title', 'Public Login')

@section('content')
    <div class="top-links">
        <h1 class="headline">Public Login</h1>
        <a href="{{ route('public.forms.index') }}">Back</a>
    </div>

    <p class="sub">Login dulu sebelum isi form. Akun guest akan otomatis mengisi data pengguna saat form dibuka.</p>

    @if (session('error'))
        <div class="card" style="border-color:#fecaca;background:#fff1f2;color:#b91c1c;">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('public.login.submit') }}">
        @csrf
        <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" name="username" value="{{ old('username') }}" required>
            @error('username')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
            @error('password')<div style="color:#b91c1c;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="{{ route('public.forms.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
@endsection
