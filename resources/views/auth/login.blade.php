<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Dashboard Operasional GP Dumai</title>

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
                        <h2>Masuk Sistem</h2>
                        <p>Silakan login menggunakan akun yang telah terdaftar.</p>
                    </div>

                    @if (session('status'))
                        <div class="login-alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="login-form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="username" placeholder="Masukkan email">

                            @error('email')
                                <div class="login-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="login-form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password" placeholder="Masukkan password">

                            @error('password')
                                <div class="login-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="login-options">
                            <label class="remember-me">
                                <input type="checkbox" name="remember">
                                <span>Ingat saya</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">
                                    Lupa password?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="login-button">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Masuk
                        </button>
                        <div class="auth-switch-link">
                            Belum punya akun?
                            <a href="{{ route('register') }}">Daftar di sini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
