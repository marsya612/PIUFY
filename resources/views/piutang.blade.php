@extends('layouts.app')
@section('title', 'Manajemen Piutang')
@section('content')

<div class="p-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold" style="color: #13094d;">Manajemen Piutang</h4>
        <button type="button"
            onclick="window.location='{{ route('piutang.create') }}'"
            class="btn rounded-pill px-4"
            style="background-color: #13094d; color: white; font-size: 14px;">
            <i class="bi bi-plus"></i> Tambah Tagihan
        </button>
    </div>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('piutang.index') }}" class="d-flex gap-2 mb-3">
        <input type="text" name="search" value="{{ request('search') }}"
            class="form-control" placeholder="Cari klien, proyek, atau no tagihan..."
            style="border: 1.5px solid #e0dff5; border-radius: 10px; font-size: 14px;"
            oninput="clearTimeout(window.searchTimer); window.searchTimer = setTimeout(() => this.form.submit(), 500)">

        <select name="status" class="form-select w-auto" onchange="this.form.submit()"
            style="border: 1.5px solid #e0dff5; border-radius: 10px; font-size: 14px;">
            <option value="">Semua Status</option>
            <option value="tertunggak" {{ request('status') == 'tertunggak' ? 'selected' : '' }}>Tertunggak</option>
            <option value="segera" {{ request('status') == 'segera' ? 'selected' : '' }}>Segera</option>
            <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Tempo</option>
            <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
        </select>

        <select name="klien" class="form-select w-auto" onchange="this.form.submit()"
            style="border: 1.5px solid #e0dff5; border-radius: 10px; font-size: 14px;">
            <option value="">Semua Klien</option>
            @foreach ($klienList as $klien)
                <option value="{{ $klien }}" {{ request('klien') == $klien ? 'selected' : '' }}>
                    {{ $klien }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- TABLE --}}
    <div class="card border-0 rounded-3" style="box-shadow: 0 4px 16px rgba(19,9,77,0.08);">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr style="border-bottom: 2px solid #eeedfb;">
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">No. Tagihan</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Klien</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Proyek</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Termin</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Metode</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;" class="text-end">Nilai (Rp)</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Tgl Terbit</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Jatuh Tempo</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Sisa Hari</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Status</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($piutangs as $item)
                            @php
                                $today = \Carbon\Carbon::today();
                                $jatuhTempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo);
                                $sisaHari = $today->diffInDays($jatuhTempo, false);
                            @endphp
                            <tr style="border-bottom: 1px solid #f3f2fd; font-size: 14px;">
                                <td style="color: #13094d; font-weight: 600;">{{ $item->no_tagihan }}</td>
                                <td style="color: #444;">{{ $item->nama_klien }}</td>
                                <td style="color: #444;">{{ $item->nama_proyek }}</td>
                                <td style="color: #444;">{{ $item->termin }}</td>
                                <td style="color: #444;">{{ $item->metode_pembayaran }}</td>
                                <td class="text-end" style="color: #2d2580; font-weight: 600;">
                                    {{ number_format($item->nilai_tagihan ?? 0, 0, ',', '.') }}
                                </td>
                                <td style="color: #444;">
                                    {{ \Carbon\Carbon::parse($item->tanggal_terbit)->format('d M Y') }}
                                </td>
                                <td style="color: #444;">
                                    {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}
                                </td>

                                {{-- SISA HARI --}}
                                <td>
                                    @if ($item->status === 'lunas')
                                        <span class="text-muted">-</span>
                                    @elseif ($sisaHari < 0)
                                        <span class="badge rounded-pill" style="background-color: #fdecea; color: #922b21;">{{ abs($sisaHari) }} hari lewat</span>
                                    @elseif ($sisaHari == 0)
                                        <span class="badge rounded-pill" style="background-color: #fdecea; color: #922b21;">Hari ini</span>
                                    @elseif ($sisaHari <= 7)
                                        <span class="badge rounded-pill" style="background-color: #fef9e7; color: #9a6a00;">{{ $sisaHari }} hari lagi</span>
                                    @else
                                        <span class="badge rounded-pill" style="background-color: #eafaf1; color: #145a32;">{{ $sisaHari }} hari lagi</span>
                                    @endif
                                </td>

                                {{-- STATUS --}}
                                <td>
                                    @if ($item->status === 'lunas')
                                        <span class="badge rounded-pill" style="background-color: #eafaf1; color: #145a32;">Lunas</span>
                                    @elseif ($sisaHari < 0)
                                        <span class="badge rounded-pill" style="background-color: #fdecea; color: #922b21;">Tertunggak</span>
                                    @elseif ($sisaHari <= 7)
                                        <span class="badge rounded-pill" style="background-color: #fef9e7; color: #9a6a00;">Segera</span>
                                    @else
                                        <span class="badge rounded-pill" style="background-color: #f3f2fd; color: #6b63c4;">Belum Tempo</span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td>
                                    <div class="d-flex gap-2 align-items-center">
                                        <a href="{{ route('piutang.edit', $item->id) }}"
                                            style="color: #6b63c4;">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        @if ($item->status !== 'lunas')
                                            <form action="{{ route('piutang.lunas', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="btn p-0 border-0 bg-transparent"
                                                    style="color: #1e8449;"
                                                    onclick="return confirm('Tandai piutang sebagai lunas?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('piutang.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus data?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn p-0 border-0 bg-transparent"
                                                style="color: #c0392b;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
