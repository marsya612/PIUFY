@extends('layouts.app')

@section('title', 'Tambah Tagihan')

@section('content')

<div class="container py-4">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('piutang.index') }}" class="me-3 text-dark text-decoration-none">←</a>
        <h4 class="mb-0 fw-semibold">Tambah Tagihan Baru</h4>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <h5 class="mb-4 fw-semibold">Data Tagihan Baru</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('piutang.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">No. Tagihan *</label>
                        <input type="text" id="no_tagihan" name="no_tagihan"
                            value="{{ old('no_tagihan') }}"
                            class="form-control rounded-3 @error('no_tagihan') is-invalid @enderror"
                            autocomplete="off">
                        <div id="no_tagihan_info" class="form-text text-success d-none">
                            ✓ Data tagihan sebelumnya ditemukan, field dikunci otomatis
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Klien *</label>
                        <input type="text" id="nama_klien" name="nama_klien"
                            value="{{ old('nama_klien') }}"
                            class="form-control rounded-3 @error('nama_klien') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Proyek *</label>
                        <input type="text" id="nama_proyek" name="nama_proyek"
                            value="{{ old('nama_proyek') }}"
                            class="form-control rounded-3 @error('nama_proyek') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Termin *</label>
                        <input type="text" id="termin" name="termin"
                            value="{{ old('termin') }}"
                            class="form-control rounded-3 @error('termin') is-invalid @enderror"
                            placeholder="Contoh: Termin 1">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nilai Tagihan (Rp) *</label>
                        <input type="number" name="nilai_tagihan"
                            value="{{ old('nilai_tagihan') }}"
                            class="form-control rounded-3 @error('nilai_tagihan') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Metode Pembayaran *</label>
                        {{-- Hidden input agar tetap terkirim saat select dikunci --}}
                        <input type="hidden" id="metode_hidden" name="metode_pembayaran" value="{{ old('metode_pembayaran') }}">
                        <select id="metode_pembayaran"
                            class="form-control rounded-3 @error('metode_pembayaran') is-invalid @enderror">
                            <option value="">-- Pilih Metode --</option>
                            <option value="Reguler" {{ old('metode_pembayaran') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                            <option value="SKBDN" {{ old('metode_pembayaran') == 'SKBDN' ? 'selected' : '' }}>SKBDN</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Terbit *</label>
                        <input type="date" id="tanggal_terbit" name="tanggal_terbit"
                            value="{{ old('tanggal_terbit') }}"
                            class="form-control rounded-3 @error('tanggal_terbit') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Jatuh Tempo *</label>
                        <input type="date" id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo"
                            value="{{ old('tanggal_jatuh_tempo') }}"
                            class="form-control rounded-3 @error('tanggal_jatuh_tempo') is-invalid @enderror"
                            readonly>
                        <div class="form-text text-muted" id="jatuh_tempo_hint"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Catatan / Keterangan</label>
                        <textarea name="catatan" rows="2"
                            class="form-control rounded-3 @error('catatan') is-invalid @enderror">{{ old('catatan') }}</textarea>
                    </div>

                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-secondary w-100 mb-2 rounded-3">
                        Simpan Tagihan
                    </button>
                    <a href="{{ route('piutang.index') }}" class="btn btn-outline-secondary w-100 rounded-3">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const noTagihanInput    = document.getElementById('no_tagihan');
    const namaKlienInput    = document.getElementById('nama_klien');
    const namaProyekInput   = document.getElementById('nama_proyek');
    const terminInput       = document.getElementById('termin');
    const metodeSelect      = document.getElementById('metode_pembayaran');
    const metodeHidden      = document.getElementById('metode_hidden');
    const tanggalTerbit     = document.getElementById('tanggal_terbit');
    const tanggalJatuhTempo = document.getElementById('tanggal_jatuh_tempo');
    const infoBox           = document.getElementById('no_tagihan_info');
    const jatuhTempoHint    = document.getElementById('jatuh_tempo_hint');

    // Sync select → hidden input (agar selalu terkirim)
    metodeSelect.addEventListener('change', function () {
        metodeHidden.value = this.value;
        hitungJatuhTempo();
    });

    // ─── 1. Lookup no. tagihan via AJAX ───────────────────────────────────
    let lookupTimeout;
    noTagihanInput.addEventListener('input', function () {
        const val = this.value.trim();
        clearTimeout(lookupTimeout);

        if (val === '') {
            resetAutoFill();
            return;
        }

        lookupTimeout = setTimeout(() => {
            fetch(`{{ route('piutang.lookup') }}?no_tagihan=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.found) {
                        // Isi otomatis
                        namaKlienInput.value  = data.nama_klien;
                        namaProyekInput.value = data.nama_proyek;
                        terminInput.value     = data.next_termin;
                        metodeSelect.value    = data.metode_pembayaran;
                        metodeHidden.value    = data.metode_pembayaran; // ← penting!

                        // Kunci field (readonly, bukan disabled)
                        namaKlienInput.readOnly  = true;
                        namaProyekInput.readOnly = true;
                        terminInput.readOnly     = true;
                        metodeSelect.style.pointerEvents = 'none';
                        metodeSelect.style.backgroundColor = '#e9ecef';

                        infoBox.classList.remove('d-none');
                        hitungJatuhTempo();
                    } else {
                        resetAutoFill();
                    }
                })
                .catch(() => resetAutoFill());
        }, 500);
    });

    function resetAutoFill() {
        namaKlienInput.readOnly  = false;
        namaProyekInput.readOnly = false;
        terminInput.readOnly     = false;
        metodeSelect.style.pointerEvents = '';
        metodeSelect.style.backgroundColor = '';
        infoBox.classList.add('d-none');
    }

    // ─── 2. Hitung tanggal jatuh tempo ────────────────────────────────────
    function hitungJatuhTempo() {
        const metode = metodeHidden.value || metodeSelect.value;
        const tglStr = tanggalTerbit.value;

        if (!metode || !tglStr) {
            tanggalJatuhTempo.value    = '';
            jatuhTempoHint.textContent = '';
            return;
        }

        const tgl  = new Date(tglStr);
        const hari = metode === 'SKBDN' ? 160 : 30;
        tgl.setDate(tgl.getDate() + hari);

        const yyyy = tgl.getFullYear();
        const mm   = String(tgl.getMonth() + 1).padStart(2, '0');
        const dd   = String(tgl.getDate()).padStart(2, '0');
        tanggalJatuhTempo.value    = `${yyyy}-${mm}-${dd}`;
        jatuhTempoHint.textContent = `Otomatis +${hari} hari dari tanggal terbit (${metode})`;
    }

    tanggalTerbit.addEventListener('change', hitungJatuhTempo);
});
</script>
@endpush
