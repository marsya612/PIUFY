<!DOCTYPE html>
<html>
<body>
    <h3>Reminder Pembayaran Piutang</h3>
    <p>Tagihan berikut akan jatuh tempo dalam <strong>{{ $sisaHari }} hari</strong>:</p>
    <ul>
        <li>Klien: {{ $piutang->nama_klien }}</li>
        <li>Proyek: {{ $piutang->nama_proyek }}</li>
        <li>No. Tagihan: {{ $piutang->no_tagihan }}</li>
        <li>Nilai: Rp {{ number_format($piutang->nilai_tagihan, 0, ',', '.') }}</li>
        <li>Jatuh Tempo: {{ $piutang->tanggal_jatuh_tempo }}</li>
    </ul>
    <p>Segera lakukan penagihan.</p>
</body>
</html>
