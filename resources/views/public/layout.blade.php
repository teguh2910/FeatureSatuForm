<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SATU FORM - @yield('title')</title>
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
            background: var(--light);
        }

        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .panel {
            width: 100%;
            max-width: 760px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 26, 114, 0.12);
            padding: 24px;
        }

        h1, h2, h3, p { margin: 0; }
        .headline { color: var(--primary); font-size: 24px; margin-bottom: 8px; }
        .sub { color: #64748b; margin-bottom: 20px; font-size: 14px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead th {
            background: var(--primary);
            color: #fff;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }

        table tbody td {
            padding: 12px;
            color : black;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }

        table tbody tr:hover {
            background: #f8f9fc;
        }

        table tbody tr:last-child td {
            border-bottom: 0;
        }

        .form-name {
            font-weight: 700;
            color: var(--primary);
        }

        .form-desc {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .btn {
            border: 0;
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-outline { border: 2px solid var(--primary); background: transparent; color: var(--primary); }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            color:black;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
            font-family: inherit;
            font-size: 14px;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .top-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .top-links a {
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success { background: #dcfce7; color: var(--success); }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: var(--accent); }

        .card {
            margin-top: 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px;
            background: #fafbff;
        }

        .success-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #dcfce7;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
        }

        .tracking-id {
            padding: 12px;
            background: var(--light);
            border-radius: 8px;
            font-family: monospace;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin: 12px 0;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .panel { padding: 18px; }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        /* DataTables Customization to match design */
        .dataTables_wrapper {
            color: var(--text);
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 16px;
            color: var(--text);
        }

        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            color: var(--text);
            font-weight: 600;
            font-size: 13px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13px;
            color: var(--text);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 13px;
            color: var(--text);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 4px 8px;
            margin: 0 2px;
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            color: var(--text);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--light);
            border-color: var(--primary);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <main class="hero">
        <section class="panel">
            @yield('content')
        </section>
    </main>
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable if exists
            if ($('#formsTable').length) {
                $('#formsTable').DataTable({
                    "pageLength": 10,
                    "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                    "language": {
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
