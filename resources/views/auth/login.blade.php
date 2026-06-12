<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ \App\Models\Setting::getValue('nama_toko', 'Brew & Co.') }} - Sistem Kasir</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="ti ti-coffee"></i>
                </div>
                <h2 class="login-title">{{ \App\Models\Setting::getValue('nama_toko', 'Brew & Co.') }}</h2>
                <p class="login-subtitle">Sistem Kasir Coffee Shop</p>
            </div>
            
            <div class="login-body">
                @error('login')
                    <div class="login-alert">
                        <i class="ti ti-alert-circle"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
                
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
                        @error('username')
                            <span style="color: var(--danger); font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                        @error('password')
                            <span style="color: var(--danger); font-size: 11px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <button type="submit" class="login-btn">
                        <i class="ti ti-login"></i> Masuk ke Sistem
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
