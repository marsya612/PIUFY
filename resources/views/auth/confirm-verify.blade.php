<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Verifikasi - Piufy</title>
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
            align-items: flex-start;
            padding: 3rem;
            color: white;
        }

        .left-panel .logo {
            margin-bottom: 2rem;
            align-self: flex-start;
        }

        .left-panel .logo img {
            height: 120px;
            width: auto;
            max-width: 260px;
            filter: brightness(0) invert(1);
            display: block;
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
            align-self: center;
        }

        .left-panel h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            line-height: 1.4;
        }

        .left-panel p {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.6);
            max-width: 320px;
            line-height: 1.7;
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
            text-align: center;
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
            line-height: 1.6;
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            background: rgba(19,9,77,0.08);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .btn-verifikasi {
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

        .btn-verifikasi:hover { background: #1e0f6e; }
        .btn-verifikasi:active { transform: scale(0.98); }

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
    <div class="badge-pill">✦ Piutang Management System</div>
    <div style="text-align: left;">
        <h1>Hampir selesai!<br>Verifikasi email<br>kamu sekarang.</h1>
        <p>Setelah verifikasi, kamu bisa langsung menggunakan semua fitur Piufy.</p>
    </div>
</div>

{{-- RIGHT PANEL --}}
<div class="right-panel">
    <div class="form-box">

        <div class="icon-circle">
            <i class="bi bi-shield-check" style="font-size: 28px; color: #13094d;"></i>
        </div>

        <h2>Konfirmasi Verifikasi</h2>
        <p class="subtitle">
            Klik tombol di bawah untuk menyelesaikan<br>verifikasi email kamu.
        </p>

        <form method="POST" action="{{ route('verification.verify.post', ['id'=>$id,'hash'=>$hash]) }}">
            @csrf
            <button type="submit" class="btn-verifikasi">
                <i class="bi bi-patch-check me-2"></i> Verifikasi Sekarang
            </button>
        </form>

    </div>
</div>

</body>
</html>
