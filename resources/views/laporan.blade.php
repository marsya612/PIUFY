@extends('layouts.app')

@section('title', 'Laporan Piutang')

@section('content')

<div class="p-4">

    <h4 class="fw-semibold mb-3">Laporan Piutang</h4>

    <!-- FILTER -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <h6 class="fw-semibold mb-3">Filter Laporan</h6>

            <form id="filterForm">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" id="from" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" id="to" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status</label>

                        <select id="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="tertunggak">Tertunggak</option>
                            <option value="segera">Segera</option>
                            <option value="belum tempo">Belum Tempo</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Klien</label>
                        <select id="klien" class="form-select">
                            <option value="">Semua</option>
                            @foreach($klienList as $klien)
                                <option value="{{ $klien }}">{{ $klien }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-secondary me-2">
                        Tampilkan
                    </button>

                    <button onclick="exportPDF()" class="btn btn-danger">
                        Export PDF
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <h6 class="fw-semibold mb-3">Hasil Laporan</h6>

            <div class="table-responsive">
                <table class="table align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Status</th>
                            <th>Nama Klien</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end">Nilai Piutang</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Silakan pilih filter lalu klik Tampilkan
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>


<script>
document.getElementById('filterForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const from = document.getElementById('from').value || '';
    const to = document.getElementById('to').value || '';
    const statusFilter = document.getElementById('status').value || '';
    const klien = document.getElementById('klien').value || '';

    fetch(`{{ url('/laporan-data') }}?from=${from}&to=${to}&status=${statusFilter}&klien=${klien}`)
    // fetch(`{{ url('/laporan-piutang-data') }}?from=${from}&to=${to}&status=${statusFilter}&klien=${klien}`)
        .then(res => res.json())
        .then(data => {

            console.log("RAW DATA:", data);

            if (!Array.isArray(data) || data.length === 0) {
                document.getElementById('tableBody').innerHTML =
                    `<tr><td colspan="4" class="text-center text-muted">Data tidak ditemukan</td></tr>`;
                return;
            }

            let grouped = {};
            let totalQty = 0;
            let totalNilai = 0;

            data.forEach(item => {

                const status = (item.status || '-').trim();
                const nama = (item.nama_klien || '-').trim();

                let nilai = Number(item.nilai_tagihan ?? 0);
                if (isNaN(nilai)) nilai = 0;

                if (!grouped[status]) grouped[status] = [];

                grouped[status].push({
                    status,
                    nama_klien: nama,
                    nilai_tagihan: nilai
                });
            });

            let html = '';

            Object.keys(grouped).forEach(status => {

                const items = grouped[status];

                let subtotalQty = 0;
                let subtotalNilai = 0;

                items.forEach(item => {

                    subtotalQty++;
                    subtotalNilai += item.nilai_tagihan;

                    html += `
                        <tr>
                            <td>${status}</td>
                            <td>${item.nama_klien}</td>
                            <td class="text-center">1</td>
                            <td class="text-end">Rp${item.nilai_tagihan.toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                });

                html += `
                    <tr class="table-secondary fw-semibold">
                        <td colspan="2">Subtotal ${status}</td>
                        <td class="text-center">${subtotalQty}</td>
                        <td class="text-end">Rp${subtotalNilai.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                totalQty += subtotalQty;
                totalNilai += subtotalNilai;
            });

            html += `
                <tr class="fw-bold">
                    <td colspan="2">TOTAL KESELURUHAN</td>
                    <td class="text-center">${totalQty}</td>
                    <td class="text-end">Rp${totalNilai.toLocaleString('id-ID')}</td>
                </tr>
            `;

            document.getElementById('tableBody').innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            document.getElementById('tableBody').innerHTML =
                `<tr><td colspan="4" class="text-center text-danger">Gagal mengambil data</td></tr>`;
        });
});
</script>

<script>
function exportPDF() {
    const from = document.getElementById('from').value || '';
    const to = document.getElementById('to').value || '';
    const status = document.getElementById('status').value || '';
    const klien = document.getElementById('klien').value || '';

    window.open(`/laporan-pdf?from=${from}&to=${to}&status=${status}&klien=${klien}`, '_blank');
    // window.open(`/laporan-piutang-pdf?from=${from}&to=${to}&status=${status}&klien=${klien}`, '_blank');
}
</script>

@endsection
