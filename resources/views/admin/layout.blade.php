<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - SATU FORM</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --fs-primary: #001a72;
            --fs-accent: #0038e0;
            --fs-light: #f0f2f8;
            --fs-text: #334155;
            --fs-danger: #dc2626;
            --fs-success: #16a34a;
            --fs-warn: #f59e0b;
            --fs-border: #d7deee;
        }

        body {
            font-family: "Segoe UI", "Helvetica Neue", sans-serif;
            color: var(--fs-text);
            background: var(--fs-light);
            min-height: 100vh;
        }

        /* Override Bootstrap defaults */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 16px;
        }

        .btn-primary {
            background-color: var(--fs-primary);
            border-color: var(--fs-primary);
            color: #fff;
        }

        .btn-primary:hover {
            background-color: var(--fs-accent);
            border-color: var(--fs-accent);
            color: #fff;
        }

        .btn-outline-primary {
            border-color: var(--fs-primary);
            color: var(--fs-primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--fs-primary);
            border-color: var(--fs-primary);
            color: #fff;
        }

        .btn-success {
            background-color: var(--fs-success);
            border-color: var(--fs-success);
            color: #fff;
        }

        .btn-danger {
            background-color: var(--fs-danger);
            border-color: var(--fs-danger);
            color: #fff;
        }

        .btn-warning {
            background-color: var(--fs-warn);
            border-color: var(--fs-warn);
            color: #fff;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--fs-primary);
            color: var(--fs-primary);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 26, 114, 0.08);
        }

        .table {
            font-size: 14px;
            color: var(--fs-text);
        }

        .table thead th {
            color: #64748b;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            border-bottom: 2px solid var(--fs-border);
            background: #f8f9fc;
        }

        .table tbody td {
            border-bottom: 1px solid var(--fs-border);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border-color: var(--fs-border);
            padding: 9px 12px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--fs-accent);
            box-shadow: 0 0 0 0.2rem rgba(0, 56, 224, 0.1);
        }

        .badge {
            border-radius: 999px;
            font-weight: 600;
            padding: 4px 10px;
            font-size: 11px;
        }

        .bg-published,
        .badge-published { background-color: #dcfce7; color: var(--fs-success); }
        .bg-draft,
        .badge-draft { background-color: #e2e8f0; color: var(--fs-text); }

        .status-approved { background-color: #dcfce7; color: var(--fs-success); }
        .status-rejected { background-color: #fee2e2; color: var(--fs-danger); }
        .status-in_review { background-color: #fef3c7; color: #92400e; }

        .flash {
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .flash-error {
            background-color: #fee2e2;
            color: var(--fs-danger);
            border: 1px solid #fecaca;
        }

        .flash-success {
            background-color: #dcfce7;
            color: var(--fs-success);
            border: 1px solid #bbf7d0;
        }

        /* DataTables Bootstrap 5 integration */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 16px;
        }

        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            color: var(--fs-text);
            font-weight: 600;
            font-size: 13px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--fs-border);
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--fs-border);
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 13px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 4px 8px;
            margin: 0 2px;
            border: 1px solid var(--fs-border);
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            color: var(--fs-text);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--fs-light);
            border-color: var(--fs-primary);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--fs-primary);
            color: #fff !important;
            border-color: var(--fs-primary);
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 12px;
            color: #64748b;
        }

        .pagination {
            margin: 0;
        }

        /* Page-level custom styles */
        @yield('page_styles')
    </style>

    @stack('css')
</head>
<body>

    <!-- Top Navigation Bar -->
    <header class="navbar navbar-expand-lg" style="background-color: var(--fs-primary);">
        <div class="container-fluid px-4">
            <a class="navbar-brand text-white fw-bold d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                <i class="ri-file-list-3-line ri-lg"></i>
                SATU FORM
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="ri-menu-line text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ trim($__env->yieldContent('active_nav')) === 'dashboard' ? 'active text-white bg-white bg-opacity-10 rounded-3' : '' }} px-3 py-2 rounded-3 mx-1" href="{{ route('admin.dashboard') }}">
                            <i class="ri-dashboard-line me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ trim($__env->yieldContent('active_nav')) === 'forms' ? 'active text-white bg-white bg-opacity-10 rounded-3' : '' }} px-3 py-2 rounded-3 mx-1" href="{{ route('admin.forms.index') }}">
                            <i class="ri-file-text-line me-1"></i> Form Builder
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ trim($__env->yieldContent('active_nav')) === 'submissions' ? 'active text-white bg-white bg-opacity-10 rounded-3' : '' }} px-3 py-2 rounded-3 mx-1" href="{{ route('admin.submissions.index') }}">
                            <i class="ri-inbox-line me-1"></i> Submissions
                        </a>
                    </li>
                    @if(session('admin_username') === env('SUPER_ADMIN_USERNAME', 'admin'))
                    <li class="nav-item">
                        <a class="nav-link text-white-50 {{ trim($__env->yieldContent('active_nav')) === 'users' ? 'active text-white bg-white bg-opacity-10 rounded-3' : '' }} px-3 py-2 rounded-3 mx-1" href="{{ route('admin.users.index') }}">
                            <i class="ri-user-settings-line me-1"></i> User Management
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-3">
                    @yield('topbar_actions')
                    <div class="text-white-50 small d-none d-lg-inline">
                        <i class="ri-user-line me-1"></i>
                        {{ session('admin_name', 'admin') }} &bull; {{ session('admin_department', '-') }}
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light text-primary fw-semibold">
                            <i class="ri-logout-box-r-line me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container-fluid py-4 px-4">
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"/>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
