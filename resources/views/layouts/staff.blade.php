<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            /* Professional Blue Dark Theme for Admin */
            --primary-color: #3B82F6;
            /* Blue - Professional, trustworthy */
            --secondary-color: #2563EB;
            /* Darker blue for hover */
            --accent-success: #10B981;
            /* Green - Success states */
            --accent-warning: #F59E0B;
            /* Amber - Warnings */
            --accent-danger: #EF4444;
            /* Red - Errors/Delete */
            --accent-info: #06B6D4;
            /* Cyan - Info states */

            --dark-bg: #0F172A;
            /* Slate 900 - Main background */
            --darker-bg: #020617;
            /* Slate 950 - Header/Footer */
            --card-bg: #1E293B;
            /* Slate 800 - Cards */
            --sidebar-bg: #0F172A;
            /* Slate 900 - Sidebar */
            --hover-bg: #334155;
            /* Slate 700 - Hover state */

            --text-primary: #F8FAFC;
            /* Slate 50 - Primary text */
            --text-secondary: #E2E8F0;
            /* Slate 200 - Secondary text */
            --text-muted: #94A3B8;
            /* Slate 400 - Muted text */

            --border-color: rgba(148, 163, 184, 0.1);
            /* Subtle border */
            --shadow-color: rgba(59, 130, 246, 0.15);
            /* Blue shadow */
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-primary);
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(to bottom, var(--darker-bg), var(--sidebar-bg));
            border-right: 1px solid var(--border-color);
        }

        .sidebar .nav-link {
            color: var(--text-muted);
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            color: var(--text-primary);
            background-color: var(--hover-bg);
            border-left-color: var(--primary-color);
        }

        .sidebar .nav-link.active {
            color: var(--text-primary);
            background-color: var(--card-bg);
            border-left-color: var(--primary-color);
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .sidebar h5 {
            color: var(--text-primary);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }

        .sidebar h5 i {
            color: var(--primary-color);
        }

        .main-content {
            min-height: 100vh;
            background-color: var(--dark-bg);
        }

        .border-bottom {
            border-color: var(--border-color) !important;
        }

        .btn-outline-secondary {
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .dropdown-menu {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        .dropdown-item {
            color: var(--text-primary);
        }

        .dropdown-item:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .dropdown-divider {
            border-color: var(--border-color);
        }

        /* Table dark theme */
        .table {
            color: var(--text-primary);
        }

        .table-dark {
            --bs-table-bg: var(--card-bg);
            --bs-table-border-color: var(--border-color);
        }

        .table-light {
            --bs-table-bg: var(--hover-bg);
            --bs-table-color: var(--text-primary);
            --bs-table-border-color: var(--border-color);
        }

        .table-light thead th {
            color: var(--text-primary) !important;
            background-color: var(--hover-bg) !important;
            border-color: var(--border-color) !important;
        }

        .table tbody tr:hover {
            background-color: var(--hover-bg);
        }

        /* Card dark theme */
        .card {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .card-header {
            background-color: var(--hover-bg) !important;
            border-bottom-color: var(--border-color);
            color: var(--text-primary) !important;
        }

        .card-header.bg-white {
            background-color: var(--hover-bg) !important;
        }

        .card-footer {
            background-color: var(--card-bg) !important;
            border-top-color: var(--border-color);
            color: var(--text-muted) !important;
        }

        /* Form elements */
        .form-control,
        .form-select {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--card-bg);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.25rem var(--shadow-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            box-shadow: 0 4px 12px var(--shadow-color);
        }

        .btn-success {
            background-color: var(--accent-success);
            border-color: var(--accent-success);
        }

        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
        }

        .btn-danger {
            background-color: var(--accent-danger);
            border-color: var(--accent-danger);
        }

        .btn-danger:hover {
            background-color: #DC2626;
            border-color: #DC2626;
        }

        .btn-warning {
            background-color: var(--accent-warning);
            border-color: var(--accent-warning);
            color: #000;
        }

        .btn-warning:hover {
            background-color: #D97706;
            border-color: #D97706;
        }

        .btn-info {
            background-color: var(--accent-info);
            border-color: var(--accent-info);
        }

        .btn-info:hover {
            background-color: #0891B2;
            border-color: #0891B2;
        }

        /* Badge colors */
        .badge.bg-success {
            background-color: var(--accent-success) !important;
        }

        .badge.bg-warning {
            background-color: var(--accent-warning) !important;
            color: #000;
        }

        .badge.bg-danger {
            background-color: var(--accent-danger) !important;
        }

        .badge.bg-info {
            background-color: var(--accent-info) !important;
        }

        /* Alert colors */
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: var(--accent-success);
            color: var(--accent-success);
        }

        .alert-warning {
            background-color: rgba(245, 158, 11, 0.1);
            border-color: var(--accent-warning);
            color: var(--accent-warning);
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: var(--accent-danger);
            color: var(--accent-danger);
        }

        .alert-info {
            background-color: rgba(6, 182, 212, 0.1);
            border-color: var(--accent-info);
            color: var(--accent-info);
        }

        /* Stats cards */
        .stat-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .stat-card.primary {
            border-left: 4px solid var(--primary-color);
        }

        .stat-card.success {
            border-left: 4px solid var(--accent-success);
        }

        .stat-card.warning {
            border-left: 4px solid var(--accent-warning);
        }

        .stat-card.danger {
            border-left: 4px solid var(--accent-danger);
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="px-3 mb-3">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('staff.scanner.*') ? 'active' : '' }}"
                                href="{{ route('staff.scanner.index') }}">
                                <i class="bi bi-qr-code-scan"></i> Soát vé
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('staff.refund.*') ? 'active' : '' }}"
                                href="{{ route('staff.refund.index') }}">
                                <i class="bi bi-arrow-return-left"></i> Hoàn tiền
                            </a>
                        </li>
                    </ul>

                    <h5 class="px-3 mt-4 mb-3">
                        <i class="bi bi-gear"></i> Cài đặt
                    </h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-circle"></i> Hồ sơ
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4 main-content">
                <!-- Top Navbar -->
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h4>@yield('page-title', 'Dashboard')</h4>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @include('components.alert')

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
