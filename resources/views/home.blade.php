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


        <!-- TABLE -->
    <div class="row g-4">
        {{-- TABLE --}}
        <div class="col-lg-7">
            <div class="card border-0 rounded-3" style="box-shadow: 0 4px 16px rgba(19,9,77,0.08);">
                <div class="card-body">
                    <h5 class="fw-semibold" style="color: #13094d;">Ringkasan Status Piutang</h5>
                    <div class="table-responsive mt-3">
                        <table class="table align-middle">
                            <thead>
                                <tr style="border-bottom: 2px solid #eeedfb;">
                                    <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">No Tagihan</th>
                                    <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Proyek</th>
                                    <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Nilai</th>
                                    <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latest as $item)
                                <tr style="border-bottom: 1px solid #f3f2fd;">
                                    <td>
                                        <span class="fw-medium" style="color: #13094d;">{{ $item->no_tagihan }}</span><br>
                                        <small class="text-muted">{{ $item->nama_klien }}</small>
                                    </td>
                                    <td style="color: #444;">{{ $item->nama_proyek }}</td>
                                    <td style="color: #2d2580; font-weight: 600;">
                                        Rp {{ number_format($item->nilai_tagihan ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($item->status_label == 'tertunggak')
                                            <span class="badge rounded-pill" style="background-color: #fdecea; color: #922b21;">Tertunggak</span>
                                        @elseif($item->status_label == 'segera')
                                            <span class="badge rounded-pill" style="background-color: #fef9e7; color: #9a6a00;">Segera</span>
                                        @elseif($item->status_label == 'jatuh_tempo')
                                            <span class="badge rounded-pill" style="background-color: #eaf4fb; color: #1a6a9a;">Jatuh Tempo</span>
                                        @elseif($item->status_label == 'belum')
                                            <span class="badge rounded-pill" style="background-color: #f3f2fd; color: #6b63c4;">Belum Tempo</span>
                                        @else
                                            <span class="badge rounded-pill" style="background-color: #eafaf1; color: #145a32;">Lunas</span>
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
                                    <td colspan="4" class="text-center text-muted py-4">
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
    
        {{-- RIGHT --}}
        <div class="col-lg-5">
            <div class="card border-0 rounded-3" style="box-shadow: 0 4px 16px rgba(19,9,77,0.08);">
                <div class="card-body">
                    <h6 class="fw-semibold mb-4" style="color: #13094d;">Persentase Penagihan</h6>
    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size: 13px; color: #444;">Lunas</span>
                            <span style="font-size: 13px; font-weight: 600; color: #145a32;">{{ round($persenLunas) }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 8px; background-color: #eafaf1;">
                            <div class="progress-bar rounded-pill" 
                                 style="width:{{ $persenLunas }}%; background-color: #1e8449;"></div>
                        </div>
                    </div>
    
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size: 13px; color: #444;">Tertunggak</span>
                            <span style="font-size: 13px; font-weight: 600; color: #922b21;">{{ round($persenTertunggak) }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 8px; background-color: #fdecea;">
                            <div class="progress-bar rounded-pill" 
                                 style="width:{{ $persenTertunggak }}%; background-color: #c0392b;"></div>
                        </div>
                    </div>
    
                </div>
            </div>
        </div>
    </div>

</div>


@endsection
