@extends('layouts.app')

@section('title', 'Tambah Tagihan')

@section('content')

<div class="container py-4">

    <!-- Title -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('piutang.index') }}" class="me-3 text-dark text-decoration-none">
            ←
        </a>
        <h4 class="mb-0 fw-semibold">Tambah Tagihan Baru</h4>
    </div>

    <!-- Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <h5 class="mb-4 fw-semibold">Data Tagihan Baru</h5>

            {{-- ERROR VALIDATION --}}
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
                        <input type="text" name="no_tagihan"
                            value="{{ old('no_tagihan') }}"
                            class="form-control rounded-3 @error('no_tagihan') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Klien *</label>
                        <input type="text" name="nama_klien"
                            value="{{ old('nama_klien') }}"
                            class="form-control rounded-3 @error('nama_klien') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Proyek *</label>
                        <input type="text" name="nama_proyek"
                            value="{{ old('nama_proyek') }}"
                            class="form-control rounded-3 @error('nama_proyek') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Termin *</label>
                        <input type="text" name="termin"
                            value="{{ old('termin') }}"
                            class="form-control rounded-3 @error('termin') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nilai Tagihan (Rp) *</label>
                        <input type="number" name="nilai_tagihan"
                            value="{{ old('nilai_tagihan') }}"
                            class="form-control rounded-3 @error('nilai_tagihan') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Metode Pembayaran *</label>
                        <select name="metode_pembayaran"
                            class="form-control rounded-3 @error('metode_pembayaran') is-invalid @enderror">
                            <option value="">-- Pilih Metode --</option>
                            <option value="Reguler" {{ old('metode_pembayaran') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                            <option value="SKBDN" {{ old('metode_pembayaran') == 'SKBDN' ? 'selected' : '' }}>SKBDN</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Terbit *</label>
                        <input type="date" name="tanggal_terbit"
                            value="{{ old('tanggal_terbit') }}"
                            class="form-control rounded-3 @error('tanggal_terbit') is-invalid @enderror">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Jatuh Tempo *</label>
                        <input type="date" name="tanggal_jatuh_tempo"
                            value="{{ old('tanggal_jatuh_tempo') }}"
                            class="form-control rounded-3 @error('tanggal_jatuh_tempo') is-invalid @enderror">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Catatan / Keterangan</label>
                        <textarea name="catatan" rows="2"
                            class="form-control rounded-3 @error('catatan') is-invalid @enderror">{{ old('catatan') }}</textarea>
                    </div>

                </div>

                <!-- Button -->
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
