<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Verifikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow text-center" style="width:400px;">
        <h5>Konfirmasi Verifikasi</h5>

        <p>Klik tombol di bawah untuk menyelesaikan verifikasi email</p>

        <form method="POST" action="{{ route('verification.verify.post', ['id'=>$id,'hash'=>$hash]) }}">
            @csrf
            <button class="btn btn-dark w-100">
                Verifikasi Sekarang
            </button>
        </form>
    </div>
</div>

</body>
</html>
