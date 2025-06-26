<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Edukasi Platform')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #7c3aed;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            padding: 2rem;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .progress-card {
            background: white;
            border-left: 4px solid var(--success-color);
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @auth
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Edukasi Platform
                    </h4>
                    
                    <nav class="nav flex-column">
                        @if(auth()->user()->role === 'admin')
                            <a class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                            <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                <i class="fas fa-users me-2"></i> Manajemen User
                            </a>
                            <a class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}">
                                <i class="fas fa-tags me-2"></i> Kategori
                            </a>
                            <a class="nav-link {{ request()->is('admin/materials*') ? 'active' : '' }}" href="{{ route('admin.materials') }}">
                                <i class="fas fa-book me-2"></i> Materi
                            </a>
                            <a class="nav-link {{ request()->is('admin/quizzes*') ? 'active' : '' }}" href="{{ route('admin.quizzes') }}">
                                <i class="fas fa-question-circle me-2"></i> Kuis
                            </a>
                            <a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                                <i class="fas fa-chart-bar me-2"></i> Laporan
                            </a>
                        @else
                            <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-2"></i> Dashboard
                            </a>
                            <a class="nav-link {{ request()->is('materials*') ? 'active' : '' }}" href="{{ route('materials') }}">
                                <i class="fas fa-book me-2"></i> Materi Belajar
                            </a>
                            <a class="nav-link {{ request()->is('quizzes*') ? 'active' : '' }}" href="{{ route('quizzes') }}">
                                <i class="fas fa-question-circle me-2"></i> Kuis
                            </a>
                            <a class="nav-link {{ request()->is('progress*') ? 'active' : '' }}" href="{{ route('progress') }}">
                                <i class="fas fa-chart-line me-2"></i> Progress
                            </a>
                            <a class="nav-link {{ request()->is('profile*') ? 'active' : '' }}" href="{{ route('profile') }}">
                                <i class="fas fa-user me-2"></i> Profile
                            </a>
                        @endif
                        
                        <hr class="text-white">
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </nav>
                </div>
            </div>
            @endauth
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                @auth
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">@yield('page-title', 'Dashboard')</span>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-1"></i>
                                    {{ auth()->user()->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                @endauth
                
                <!-- Content -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @yield('scripts')
</body>
</html> 