<!DOCTYPE html>
<html>
<head>
    <title>Laporan Piutang</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #13094d;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo img {
            height: 50px;
        }

        .company {
            text-align: right;
        }

        .company h2 {
            margin: 0;
            color: #13094d;
        }

        .company p {
            margin: 2px 0;
            color: #6b63c4;
        }

        .info {
            margin-bottom: 15px;
        }

        .info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #13094d;
            color: white;
            padding: 8px;
            text-align: left;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #eeedfb;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .subtotal {
            background-color: #f3f2fd;
            font-weight: bold;
            color: #2d2580;
        }

        .total {
            background-color: #13094d;
            color: white;
            font-weight: bold;
        }

        .summary {
            margin-top: 15px;
            padding: 10px;
            background: #f3f2fd;
            border: 1px solid #e0dff5;
            border-left: 4px solid #13094d;
        }

        .summary p {
            margin: 3px 0;
            color: #2d2580;
        }

        /* Badge status */
        .badge-tertunggak {
            background-color: #fdecea;
            color: #922b21;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .badge-segera {
            background-color: #fef9e7;
            color: #9a6a00;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .badge-lunas {
            background-color: #eafaf1;
            color: #145a32;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .badge-default {
            background-color: #f3f2fd;
            color: #6b63c4;
            padding: 2px 8px;
            border-radius: 20px;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">
        <img src="{{ public_path('images/logo.png') }}">
    </div>

    <div class="company">
        <h2>PIUFY</h2>
        <p>Laporan Piutang</p>
    </div>
</div>

<!-- INFO -->
<div class="info">
    <p><strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    <p>
        <strong>Periode:</strong>
        @if($minDate && $maxDate)
            {{ \Carbon\Carbon::parse($minDate)->translatedFormat('d F Y') }}
            s/d
            {{ \Carbon\Carbon::parse($maxDate)->translatedFormat('d F Y') }}
        @else
            Semua periode
        @endif
    </p>
</div>

@php
    $grouped = [];
    $totalQty = 0;
    $totalNilai = 0;

    foreach ($data as $item) {
        $status = $item->status ?? '-';
        if (!isset($grouped[$status])) {
            $grouped[$status] = [];
        }
        $grouped[$status][] = $item;
    }

    function badgeClass($status) {
        $s = strtolower($status);
        if ($s === 'tertunggak') return 'badge-tertunggak';
        if ($s === 'segera') return 'badge-segera';
        if ($s === 'lunas') return 'badge-lunas';
        return 'badge-default';
    }
@endphp

<!-- TABLE -->
<table>
    <thead>
        <tr>
            <th>Status</th>
            <th>Nama Klien</th>
            <th class="text-center">Jumlah</th>
            <th class="text-end">Nilai Piutang</th>
        </tr>
    </thead>

    <tbody>

    @foreach($grouped as $status => $items)

        @php
            $subtotalQty = 0;
            $subtotalNilai = 0;
        @endphp

        @foreach($items as $item)

            @php
                $nilai = $item->nilai_tagihan ?? $item->nilai_piutang ?? 0;
                $subtotalQty++;
                $subtotalNilai += $nilai;
            @endphp

            <tr>
                <td>
                    <span class="{{ badgeClass($status) }}">
                        {{ ucfirst($status) }}
                    </span>
                </td>
                <td>{{ $item->nama_klien }}</td>
                <td class="text-center">1</td>
                <td class="text-end" style="color:#2d2580; font-weight:600;">
                    Rp{{ number_format($nilai, 0, ',', '.') }}
                </td>
            </tr>

        @endforeach

        <!-- SUBTOTAL -->
        <tr class="subtotal">
            <td colspan="2">Subtotal {{ ucfirst($status) }}</td>
            <td class="text-center">{{ $subtotalQty }}</td>
            <td class="text-end">
                Rp{{ number_format($subtotalNilai, 0, ',', '.') }}
            </td>
        </tr>

        @php
            $totalQty += $subtotalQty;
            $totalNilai += $subtotalNilai;
        @endphp

    @endforeach

    <!-- TOTAL -->
    <tr class="total">
        <td colspan="2">TOTAL KESELURUHAN</td>
        <td class="text-center">{{ $totalQty }}</td>
        <td class="text-end">
            Rp{{ number_format($totalNilai, 0, ',', '.') }}
        </td>
    </tr>

    </tbody>
</table>

<!-- SUMMARY -->
<div class="summary">
    <p><strong>Total Transaksi:</strong> {{ $totalQty }}</p>
    <p><strong>Total Nilai Piutang:</strong> Rp{{ number_format($totalNilai, 0, ',', '.') }}</p>
</div>

</body>
</html>
