<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - FormFlow</title>
    <style>
        :root {
            --primary: #001a72;
            --accent: #0038e0;
            --light: #f0f2f8;
            --text: #334155;
            --danger: #dc2626;
            --success: #16a34a;
            --border: #d7deee;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", "Helvetica Neue", sans-serif;
            color: var(--text);
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: grid;
            place-items: center;
            padding: 20px;
        }
        .card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 26, 114, 0.2);
            padding: 24px;
        }
        h1 { margin: 0 0 8px; color: var(--primary); font-size: 24px; }
        p { margin: 0 0 20px; color: #64748b; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
        }
        .row { margin-bottom: 14px; }
        .btn {
            width: 100%;
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            background: var(--primary);
            cursor: pointer;
        }
        .flash {
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 13px;
        }
        .flash-error { background: #fee2e2; color: var(--danger); }
        .flash-success { background: #dcfce7; color: var(--success); }
        .back {
            margin-top: 14px;
            text-align: center;
        }
        .back a { color: var(--primary); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <section class="card">
        <h1>Admin Login</h1>
        <p>Masuk untuk kelola submission, approve, atau reject.</p>

        @if (session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="row">
                <label for="username">Username</label>
                <input id="username" name="username" value="{{ old('username') }}" required>
                @error('username')
                    <div class="flash flash-error" style="margin-top: 8px; margin-bottom: 0;">{{ $message }}</div>
                @enderror
            </div>
            <div class="row">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
                @error('password')
                    <div class="flash flash-error" style="margin-top: 8px; margin-bottom: 0;">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <div class="back">
            <a href="{{ url('/') }}">Kembali ke Form User</a>
        </div>
    </section>
</body>
</html>
