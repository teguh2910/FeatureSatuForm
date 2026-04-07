<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - SATU FORM</title>
    <style>
        :root {
            --primary: #001a72;
            --accent: #0038e0;
            --light: #f0f2f8;
            --text: #334155;
            --danger: #dc2626;
            --success: #16a34a;
            --warn: #f59e0b;
            --border: #d7deee;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", "Helvetica Neue", sans-serif;
            color: var(--text);
            background: var(--light);
        }

        .topbar {
            background: var(--primary);
            color: #fff;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .nav {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav a {
            color: #fff;
            text-decoration: none;
            padding: 7px 11px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 13px;
        }

        .nav .active { background: rgba(255, 255, 255, 0.15); }

        .wrap {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 16px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 26, 114, 0.08);
            padding: 16px;
            margin-bottom: 16px;
        }

        .flash {
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .flash-error { background: #fee2e2; color: var(--danger); }
        .flash-success { background: #dcfce7; color: var(--success); }

        .btn {
            border: 0;
            border-radius: 8px;
            padding: 8px 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-outline { background: transparent; border: 2px solid var(--primary); color: var(--primary); }
        .btn-success { background: var(--success); color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-warning { background: var(--warn); color: #fff; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th,
        td {
            padding: 10px 8px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: top;
        }

        th {
            color: #64748b;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        input,
        select,
        textarea,
        button {
            font-family: inherit;
            font-size: 14px;
            border-radius: 8px;
            padding: 9px 11px;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--border);
            background: #fff;
        }

        @yield('page_styles')
    </style>
</head>
<body>
<header class="topbar">
    <div>
        <strong>SATU FORM</strong>
        <div style="font-size: 12px; opacity: .85;">Login: {{ session('admin_name', 'admin') }} • {{ session('admin_department', '-') }}</div>
    </div>

    <nav class="nav">
        <a href="{{ route('admin.dashboard') }}" class="@if(trim($__env->yieldContent('active_nav')) === 'dashboard')active @endif">Dashboard</a>
        <a href="{{ route('admin.forms.index') }}" class="@if(trim($__env->yieldContent('active_nav')) === 'forms')active @endif">Form Builder</a>
        <a href="{{ route('admin.submissions.index') }}" class="@if(trim($__env->yieldContent('active_nav')) === 'submissions')active @endif">Submissions</a>
        @if(session('admin_username') === env('SUPER_ADMIN_USERNAME', 'admin'))
            <a href="{{ route('admin.users.index') }}" class="@if(trim($__env->yieldContent('active_nav')) === 'users')active @endif">User Management</a>
        @endif
    </nav>

    <div style="display:flex; gap:8px; align-items:center;">
        @yield('topbar_actions')
        <form method="POST" action="{{ route('admin.logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn btn-primary">Logout</button>
        </form>
    </div>
</header>

<main class="wrap">
    @if (session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</main>
@stack('scripts')
</body>
</html>
