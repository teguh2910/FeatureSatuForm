<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - SATU FORM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --fs-primary: #001a72;
            --fs-accent: #0038e0;
            --fs-light: #f0f2f8;
            --fs-text: #334155;
            --fs-danger: #dc2626;
            --fs-success: #16a34a;
            --fs-border: #d7deee;
        }
        body {
            font-family: "Segoe UI", "Helvetica Neue", sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--fs-primary), var(--fs-accent));
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 26, 114, 0.25);
        }
        .login-card .card-body {
            padding: 40px 32px;
        }
        .form-control {
            border-radius: 8px;
            border-color: var(--fs-border);
            padding: 10px 14px;
        }
        .form-control:focus {
            border-color: var(--fs-accent);
            box-shadow: 0 0 0 0.2rem rgba(0, 56, 224, 0.1);
        }
        .btn-primary {
            background-color: var(--fs-primary);
            border-color: var(--fs-primary);
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 16px;
        }
        .btn-primary:hover {
            background-color: var(--fs-accent);
            border-color: var(--fs-accent);
        }
        .brand-logo {
            width: 48px;
            height: 48px;
            background: var(--fs-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100 p-4">
        <div class="card login-card">
            <div class="card-body">
                <div class="brand-logo mx-auto">
                    <i class="ri-file-list-3-line text-white fs-4"></i>
                </div>
                <h3 class="text-center text-primary mb-1">Admin Login</h3>
                <p class="text-center text-muted small mb-4">Masuk untuk kelola submission dan form.</p>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf
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
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="ri-login-box-line me-2"></i> Login
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="text-primary text-decoration-none small fw-semibold">
                        <i class="ri-arrow-left-line me-1"></i> Kembali ke Form User
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
