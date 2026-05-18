<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Dashboard Operasional GP Dumai</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-left">
                <div class="login-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo PT Semen Padang" class="login-logo">
                    <div>
                        <h1>Dashboard Operasional GP Dumai</h1>
                        <p>PT Semen Padang Unit Pabrik Dumai</p>
                    </div>
                </div>
            </div>

            <div class="login-right">
                <div class="login-card">
                    <div class="login-card-header">
                        <h2>Daftar Akun</h2>
                        <p>Silakan lengkapi data berikut untuk membuat akun baru.</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- <div class="login-form-group">
                            <label for="name">Nama</label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Masukkan nama lengkap"
                            >

                            @error('name')
                                <div class="login-error">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="login-form-group">
                            <label for="email">Email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="Masukkan email"
                            >

                            @error('email')
                                <div class="login-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="login-form-group">
                            <label for="password">Password</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Masukkan password"
                            >

                            @error('password')
                                <div class="login-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="login-form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password"
                            >

                            @error('password_confirmation')
                                <div class="login-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="login-button">
                            <i class="bi bi-person-plus"></i>
                            Daftar
                        </button>

                        <div class="auth-switch-link">
                            Sudah punya akun?
                            <a href="{{ route('login') }}">Masuk di sini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>