@extends('layouts.app')

@section('title', 'Dashboard Monitoring Piutang')

@section('content')

@php
function rupiah($angka){
    return 'Rp' . number_format($angka,0,',','.');
}
@endphp

<div class="container-fluid p-4">

    <h3 class="fw-semibold">Dashboard Monitoring Piutang</h3>
    <p class="text-muted mb-4">Selamat datang, PT Trocon Indah Perkasa</p>

    <!-- CARDS -->
    <!-- <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="p-3 bg-light rounded-3">
                <small class="text-muted">Total Piutang</small>
                <h5 class="fw-bold">{{ rupiah($totalPiutang) }}</h5>
                <span class="badge bg-white text-dark">{{ $totalTagihanAktif }} Tagihan Aktif</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-light rounded-3">
                <small class="text-muted">Tertunggak</small>
                <h5 class="fw-bold">{{ rupiah($totalTertunggak) }}</h5>
                <span class="badge bg-white text-dark">{{ $countTertunggak }} Tagihan</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-light rounded-3">
                <small class="text-muted">Jatuh Tempo ≤ 7 Hari</small>
                <h5 class="fw-bold">{{ rupiah($totalJatuhTempo) }}</h5>
                <span class="badge bg-white text-dark">{{ $countJatuhTempo }} Tagihan</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-light rounded-3">
                <small class="text-muted">Lunas Bulan Ini</small>
                <h5 class="fw-bold">{{ rupiah($totalLunas) }}</h5>
                <span class="badge bg-white text-dark">{{ $countLunas }} Tagihan</span>
            </div>
        </div>

    </div> -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="p-3 rounded-3" style="background: white; box-shadow: 0 4px 16px rgba(106,99,196,0.15); border-left: 4px solid #6b63c4;">
                <small style="color: #6b63c4;">Total Piutang</small>
                <h5 class="fw-bold" style="color: #2d2580;">{{ rupiah($totalPiutang) }}</h5>
                <span class="badge" style="background-color: #eeedfb; color: #2d2580;">
                    {{ $totalTagihanAktif }} Tagihan Aktif
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded-3" style="background: white; box-shadow: 0 4px 16px rgba(192,57,43,0.15); border-left: 4px solid #c0392b;">
                <small style="color: #c0392b;">Tertunggak</small>
                <h5 class="fw-bold" style="color: #922b21;">{{ rupiah($totalTertunggak) }}</h5>
                <span class="badge" style="background-color: #fdecea; color: #922b21;">
                    {{ $countTertunggak }} Tagihan
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded-3" style="background: white; box-shadow: 0 4px 16px rgba(212,160,23,0.15); border-left: 4px solid #d4a017;">
                <small style="color: #d4a017;">Jatuh Tempo ≤ 7 Hari</small>
                <h5 class="fw-bold" style="color: #9a6a00;">{{ rupiah($totalJatuhTempo) }}</h5>
                <span class="badge" style="background-color: #fef9e7; color: #9a6a00;">
                    {{ $countJatuhTempo }} Tagihan
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded-3" style="background: white; box-shadow: 0 4px 16px rgba(30,132,73,0.15); border-left: 4px solid #1e8449;">
                <small style="color: #1e8449;">Lunas Bulan Ini</small>
                <h5 class="fw-bold" style="color: #145a32;">{{ rupiah($totalLunas) }}</h5>
                <span class="badge" style="background-color: #eafaf1; color: #145a32;">
                    {{ $countLunas }} Tagihan
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- TABLE -->

<div class="col-lg-7">
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <h5 class="fw-semibold">Ringkasan Status Piutang</h5>

            <div class="table-responsive mt-3">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No Tagihan</th>
                            <th>Proyek</th>
                            <th>Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($latest as $item)
                        <tr>
                            <td>
                                {{ $item->no_tagihan }} <br>
                                <small class="text-muted">
                                    {{ $item->nama_klien }}
                                </small>
                            </td>

                            <td>{{ $item->nama_proyek }}</td>

                            <td>
                                Rp {{ number_format($item->nilai_tagihan ?? 0, 0, ',', '.') }}
                            </td>

                            <td>
                                @if($item->status_label == 'tertunggak')
                                    <span class="badge bg-danger">Tertunggak</span>

                                @elseif($item->status_label == 'segera')
                                    <span class="badge bg-warning text-dark">Segera</span>

                                @elseif($item->status_label == 'jatuh_tempo')
                                    <span class="badge bg-info text-dark">Jatuh Tempo</span>

                                @elseif($item->status_label == 'belum')
                                    <span class="badge bg-secondary">Belum Tempo</span>

                                @else
                                    <span class="badge bg-success">Lunas</span>
                                @endif

                                <br>
<small class="text-muted">
    @if($item->status_label == 'lunas')
        Sudah dibayar
    @elseif($item->sisaHari < 0)
        Terlambat {{ abs($item->sisaHari) }} hari
    @else
        {{ $item->sisaHari }} hari lagi
    @endif
</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Tidak ada data piutang
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

        <!-- RIGHT -->
        <div class="col-lg-5">

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold">Persentase Penagihan</h6>

                    <div class="mt-3">

                        <div class="d-flex justify-content-between small">
                            <span>Lunas</span>
                            <span>{{ round($persenLunas) }}%</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" style="width:{{ $persenLunas }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between small">
                            <span>Tertunggak</span>
                            <span>{{ round($persenTertunggak) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width:{{ $persenTertunggak }}%"></div>
                        </div>

                    </div>
                </div>
            </div>


        </div>

    </div>

</div>


@endsection
