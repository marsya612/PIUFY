<!DOCTYPE html>
<html>
<head>
    <title>Register - Piufy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; }

        .left-panel {
            width: 45%;
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
            align-self: flex-start;
        }

        .left-panel .logo span {
            font-size: 26px;
            font-weight: 800;
            color: white;
            letter-spacing: -1px;
        }

        .left-panel h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .left-panel p {
            font-size: 1rem;
            color: rgba(255,255,255,0.6);
            max-width: 320px;
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
            width: 55%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            background: #f8f7ff;
            overflow-y: auto;
        }

        .form-box {
            width: 100%;
            max-width: 420px;
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

        .form-control[readonly] {
            background: #f0eff9;
            color: #999;
        }

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

        .btn-register {
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

        .btn-register:hover { background: #1e0f6e; }
        .btn-register:active { transform: scale(0.98); }

        .login-link {
            text-align: center;
            font-size: 13px;
            color: #888;
            margin-top: 1.2rem;
        }

        .login-link a {
            color: #13094d;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover { text-decoration: underline; }

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
        <span>piufy</span>
    </div>
    <div>
        <div class="badge-pill">✦ Piutang Management System</div>
        <h1>Mulai kelola<br>keuangan kamu<br>sekarang.</h1>
        <p>Daftar dan nikmati kemudahan monitor tagihan & laporan piutang dalam satu platform.</p>
    </div>
</div>

{{-- RIGHT PANEL --}}
<div class="right-panel">
    <div class="form-box">
        <h2>Buat akun baru ✨</h2>
        <p class="subtitle">Isi data diri kamu untuk mulai</p>

        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:13px; border-radius:10px;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 mb-3" style="font-size:13px; border-radius:10px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row g-3 mb-1">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control"
                        placeholder="Nama lengkap" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        placeholder="kamu@email.com" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Telepon</label>
                    <input type="text" name="phone" class="form-control"
                        placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control"
                        placeholder="Jabatan kamu" value="{{ old('jabatan') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Divisi</label>
                    <input type="text" class="form-control" value="Divisi Keuangan" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="••••••••" required style="padding-right: 42px;">
                        <button type="button" class="toggle-password" onclick="togglePassword('password', 'iconPassword')">
                            <i class="bi bi-eye" id="iconPassword" style="font-size: 16px;"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                            placeholder="••••••••" required style="padding-right: 42px;">
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', 'iconConfirm')">
                            <i class="bi bi-eye" id="iconConfirm" style="font-size: 16px;"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-register">Daftar Sekarang</button>
            </div>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk sekarang</a>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
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
