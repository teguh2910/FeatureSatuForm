<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SATU FORM - @yield('title')</title>
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
            color: var(--fs-text);
            background: var(--fs-light);
            min-height: 100vh;
        }

        .brand-bar {
            background: linear-gradient(135deg, var(--fs-primary), var(--fs-accent));
            padding: 12px 0;
        }

        .brand-bar .brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
        }

        .public-panel {
            max-width: 860px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 26, 114, 0.1);
            overflow: hidden;
        }

        .public-panel-header {
            background: linear-gradient(135deg, var(--fs-primary), var(--fs-accent));
            color: #fff;
            padding: 32px;
            text-align: center;
        }

        .public-panel-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .public-panel-body {
            padding: 32px;
        }

        .btn-primary {
            background-color: var(--fs-primary);
            border-color: var(--fs-primary);
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: var(--fs-accent);
            border-color: var(--fs-accent);
            color: #fff;
        }

        .btn-outline-primary {
            border-color: var(--fs-primary);
            color: var(--fs-primary);
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-outline-primary:hover {
            background-color: var(--fs-primary);
            border-color: var(--fs-primary);
            color: #fff;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border-color: var(--fs-border);
            padding: 10px 14px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--fs-accent);
            box-shadow: 0 0 0 0.2rem rgba(0, 56, 224, 0.1);
        }

        .badge-success { background-color: #dcfce7; color: var(--fs-success); }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-info { background-color: #dbeafe; color: var(--fs-accent); }
        .badge-secondary { background-color: #e2e8f0; color: var(--fs-text); }

        /* DataTables Bootstrap 5 */
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

        @media (max-width: 768px) {
            .public-panel { margin: 16px; }
            .public-panel-body { padding: 20px; }
            .public-panel-header { padding: 24px 20px; }
        }
    </style>

    @stack('css')
</head>
<body>

    <!-- Brand Bar -->
    <div class="brand-bar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('public.forms.index') }}" class="brand d-flex align-items-center gap-2">
                    <i class="ri-file-list-3-line"></i>
                    SATU FORM
                </a>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('public.forms.track') }}" class="text-white text-decoration-none small">Tracking</a>
                    @if(session('public_auth'))
                        <span class="text-white-50 small">{{ session('public_name') }} ({{ strtoupper(session('public_level', 'guest')) }})</span>
                        <form method="POST" action="{{ route('public.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light text-primary fw-semibold">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('public.login') }}" class="btn btn-sm btn-light text-primary fw-semibold">Login</a>
                    @endif
                    <a href="{{ route('admin.login') }}" class="text-white text-decoration-none small">Admin</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Panel -->
    <div class="public-panel">
        <div class="public-panel-header">
            <h1>@yield('panel_title', 'SATU Form')</h1>
            @hasSection('panel_subtitle')
                <p class="mb-0 opacity-75">@yield('panel_subtitle')</p>
            @endif
        </div>
        <div class="public-panel-body">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"/>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(document).ready(function() {
            if ($('#formsTable').length) {
                $('#formsTable').DataTable({
                    "pageLength": 10,
                    "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                    "language": {
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "paginate": { "first": "First", "last": "Last", "next": "Next", "previous": "Previous" }
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
