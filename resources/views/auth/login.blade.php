<!DOCTYPE html>
<html>
<head>
    <title>Login - Piufy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; }

        .left-panel {
            width: 55%;
            background-color: #13094d;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            color: white;
        }

        .left-panel .logo {
            margin-bottom: 3rem;
            /* align-self: flex-start; */
            align-self: center;
            text-align: center;
        }

        .left-panel .logo img {
            height: 60px;
            width: auto;
            max-width: 200px;
            filter: brightness(0) invert(1);
            display: block;
        }

        .left-panel h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .left-panel p {
            font-size: 1rem;
            color: rgba(255,255,255,0.6);
            max-width: 360px;
            line-height: 1.7;
        }

        .badge-pill {
            display: inline-block;
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.8);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 13px;
            margin-bottom: 1.5rem;
        }

        .right-panel {
            width: 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            background: #f8f7ff;
        }

        .form-box {
            width: 100%;
            max-width: 360px;
        }

        .form-box h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #13094d;
            margin-bottom: 0.4rem;
        }

        .form-box .subtitle {
            font-size: 14px;
            color: #888;
            margin-bottom: 2rem;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #e0dff5;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            background: white;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #13094d;
            box-shadow: 0 0 0 3px rgba(19,9,77,0.08);
        }

        .btn-login {
            background: #13094d;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px;
            font-size: 15px;
            font-weight: 600;
            width: 100%;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-login:hover { background: #1e0f6e; }
        .btn-login:active { transform: scale(0.98); }

        .register-link {
            text-align: center;
            font-size: 13px;
            color: #888;
            margin-top: 1.2rem;
        }

        .register-link a {
            color: #13094d;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover { text-decoration: underline; }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #888;
            padding: 0;
            line-height: 1;
        }

        .toggle-password:hover { color: #13094d; }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; }
        }
    </style>
</head>
<body>

{{-- LEFT PANEL --}}
<div class="left-panel">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="Piufy">
    </div>
    <div>
        <div class="badge-pill">✦ Piutang Management System</div>
        <h1>Kelola piutang<br>lebih mudah<br>& efisien.</h1>
        <p>Monitor tagihan, pantau jatuh tempo, dan kelola laporan keuangan dalam satu platform.</p>
    </div>
</div>

{{-- RIGHT PANEL --}}
<div class="right-panel">
    <div class="form-box">
        <h2>Selamat datang 👋</h2>
        <p class="subtitle">Masuk ke akun Piufy kamu</p>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3" style="font-size:13px; border-radius:10px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2 px-3" style="font-size:13px; border-radius:10px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                    placeholder="kamu@email.com" value="{{ old('email') }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="passwordInput" class="form-control"
                        placeholder="••••••••" required style="padding-right: 42px;">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon" style="font-size: 16px;"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <div class="register-link">
            Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

</body>
</html>
