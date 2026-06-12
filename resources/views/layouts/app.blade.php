<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Brew & Co.') - Sistem Kasir</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <i class="ti ti-coffee brand-icon"></i>
                <div class="brand-text">
                    <span class="brand-name">Brew & Co.</span>
                    <span class="brand-sub">Sistem Kasir</span>
                </div>
            </div>
            
            <nav class="nav-menu">
                <a href="{{ route('menu') }}" class="nav-item {{ request()->routeIs('menu') ? 'active' : '' }}">
                    <i class="ti ti-layout-grid"></i>
                    <span>Menu Transaksi</span>
                </a>
                <a href="{{ route('history') }}" class="nav-item {{ request()->routeIs('history*') ? 'active' : '' }}">
                    <i class="ti ti-history"></i>
                    <span>Riwayat</span>
                </a>
                
                @if(auth()->user()->isAdmin())
                    <div class="nav-group-title">Pengaturan</div>
                    <a href="{{ route('setting.akun') }}" class="nav-item {{ request()->routeIs('setting.akun') ? 'active' : '' }}">
                        <i class="ti ti-user-cog"></i>
                        <span>Akun Saya</span>
                    </a>
                    <a href="{{ route('setting.toko') }}" class="nav-item {{ request()->routeIs('setting.toko') ? 'active' : '' }}">
                        <i class="ti ti-settings"></i>
                        <span>Informasi Toko</span>
                    </a>
                    <a href="{{ route('setting.menu') }}" class="nav-item {{ request()->routeIs('setting.menu*') ? 'active' : '' }}">
                        <i class="ti ti-tools-kitchen-2"></i>
                        <span>Kelola Menu</span>
                    </a>
                @else
                    <a href="{{ route('setting.akun') }}" class="nav-item {{ request()->routeIs('setting.akun') ? 'active' : '' }}">
                        <i class="ti ti-user-cog"></i>
                        <span>Pengaturan Akun</span>
                    </a>
                @endif
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="avatar">{{ auth()->user()->initial }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->nama }}</div>
                        <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="ti ti-logout"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                <div class="topbar-right">
                    <span class="current-date">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>
            
            <section class="content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="ti ti-circle-check"></i> {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-circle"></i>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('content')
            </section>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
