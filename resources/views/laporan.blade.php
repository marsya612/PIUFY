@extends('layouts.app')
@section('title', 'Laporan Piutang')
@section('content')

<div class="p-4">

    <h4 class="fw-semibold mb-3" style="color: #13094d;">Laporan Piutang</h4>

    {{-- FILTER --}}
    <div class="card border-0 rounded-3 mb-4" style="box-shadow: 0 4px 16px rgba(19,9,77,0.08);">
        <div class="card-body">
            <h6 class="fw-semibold mb-3" style="color: #13094d;">Filter Laporan</h6>
            <form id="filterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 13px; font-weight: 600; color: #444;">Dari Tanggal</label>
                        <input type="date" id="from" class="form-control"
                            style="border: 1.5px solid #e0dff5; border-radius: 10px; font-size: 14px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 13px; font-weight: 600; color: #444;">Sampai Tanggal</label>
                        <input type="date" id="to" class="form-control"
                            style="border: 1.5px solid #e0dff5; border-radius: 10px; font-size: 14px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 13px; font-weight: 600; color: #444;">Status</label>
                        <select id="status" class="form-select"
                            style="border: 1.5px solid #e0dff5; border-radius: 10px; font-size: 14px;">
                            <option value="">Semua</option>
                            <option value="tertunggak">Tertunggak</option>
                            <option value="segera">Segera</option>
                            <option value="belum tempo">Belum Tempo</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size: 13px; font-weight: 600; color: #444;">Nama Klien</label>
                        <select id="klien" class="form-select"
                            style="border: 1.5px solid #e0dff5; border-radius: 10px; font-size: 14px;">
                            <option value="">Semua</option>
                            @foreach($klienList as $klien)
                                <option value="{{ $klien }}">{{ $klien }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn rounded-pill px-4"
                        style="background-color: #13094d; color: white; font-size: 14px;">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                    <button type="button" onclick="exportPDF()" class="btn rounded-pill px-4"
                        style="background-color: #fdecea; color: #922b21; border: 1px solid #f5b7b1; font-size: 14px;">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 rounded-3" style="box-shadow: 0 4px 16px rgba(19,9,77,0.08);">
        <div class="card-body">
            <h6 class="fw-semibold mb-3" style="color: #13094d;">Hasil Laporan</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr style="border-bottom: 2px solid #eeedfb;">
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Status</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;">Nama Klien</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;" class="text-center">Jumlah</th>
                            <th style="color: #6b63c4; font-size: 13px; font-weight: 600;" class="text-end">Nilai Piutang</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
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
function badgeStatus(status) {
    const s = status.toLowerCase();
    if (s === 'tertunggak') return `<span class="badge rounded-pill" style="background-color:#fdecea;color:#922b21;">Tertunggak</span>`;
    if (s === 'segera') return `<span class="badge rounded-pill" style="background-color:#fef9e7;color:#9a6a00;">Segera</span>`;
    if (s === 'lunas') return `<span class="badge rounded-pill" style="background-color:#eafaf1;color:#145a32;">Lunas</span>`;
    return `<span class="badge rounded-pill" style="background-color:#f3f2fd;color:#6b63c4;">${status}</span>`;
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const from = document.getElementById('from').value || '';
        const to = document.getElementById('to').value || '';
        const statusFilter = document.getElementById('status').value || '';
        const klien = document.getElementById('klien').value || '';

        fetch(`/laporan-data?from=${from}&to=${to}&status=${statusFilter}&klien=${klien}`)
            .then(res => res.json())
            .then(data => {
                // console.log('Data dari API:', data); // DEBUG

                if (!Array.isArray(data) || data.length === 0) {
                    document.getElementById('tableBody').innerHTML =
                        `<tr><td colspan="4" class="text-center text-muted py-4">Data tidak ditemukan</td></tr>`;
                    return;
                }

                let grouped = {};
                let totalQty = 0;
                let totalNilai = 0;

                data.forEach(item => {
                    const status = (item.status || '-').trim();
                    const nama = (item.nama_klien || '-').trim();
                    let nilai = Number(item.nilai_tagihan ?? 0);
                    if (!grouped[status]) grouped[status] = [];
                    grouped[status].push({ status, nama_klien: nama, nilai_tagihan: nilai });
                });

                // console.log('Grouped:', grouped); // DEBUG

                let html = '';

                Object.keys(grouped).forEach(status => {
                    const items = grouped[status];
                    let subtotalQty = 0;
                    let subtotalNilai = 0;

                    items.forEach(item => {
                        subtotalQty++;
                        subtotalNilai += item.nilai_tagihan;
                        html += `
                            <tr style="border-bottom: 1px solid #f3f2fd; font-size: 14px;">
                                <td>${badgeStatus(item.status)}</td>
                                <td style="color:#444;">${item.nama_klien}</td>
                                <td class="text-center" style="color:#444;">1</td>
                                <td class="text-end" style="color:#2d2580;font-weight:600;">
                                    Rp${item.nilai_tagihan.toLocaleString('id-ID')}
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                        <tr style="background-color:#f3f2fd;">
                            <td colspan="2" style="color:#2d2580;font-weight:600;font-size:13px;">
                                Subtotal ${status}
                            </td>
                            <td class="text-center" style="color:#2d2580;font-weight:600;">${subtotalQty}</td>
                            <td class="text-end" style="color:#2d2580;font-weight:600;">
                                Rp${subtotalNilai.toLocaleString('id-ID')}
                            </td>
                        </tr>
                    `;

                    // ✅ PERBAIKAN: totalQty & totalNilai diupdate di dalam forEach
                    totalQty += subtotalQty;
                    totalNilai += subtotalNilai;
                }); // ✅ tutup Object.keys forEach dulu

                // ✅ PERBAIKAN: baris TOTAL ditambahkan ke html SEBELUM set innerHTML
                // html += `
                //     <tr style="background-color:#13094d;">
                //         <td colspan="2" style="color:white;font-weight:700;font-size:13px;">
                //             TOTAL KESELURUHAN
                //         </td>
                //         <td class="text-center" style="color:white;font-weight:700;">${totalQty}</td>
                //         <td class="text-end" style="color:white;font-weight:700;">
                //             Rp${totalNilai.toLocaleString('id-ID')}
                //         </td>
                //     </tr>
                // `;
                html += `
                    <tr style="background-color:#13094d !important; border: 2px solid #13094d !important;">
                        <td colspan="2" style="color:#ffffff !important; font-weight:700; font-size:13px; padding:12px 8px; background-color:#13094d !important;">
                            TOTAL KESELURUHAN
                        </td>
                        <td class="text-center" style="color:#ffffff !important; font-weight:700; padding:12px 8px; background-color:#13094d !important;">
                            ${totalQty}
                        </td>
                        <td class="text-end" style="color:#ffffff !important; font-weight:700; padding:12px 8px; background-color:#13094d !important;">
                            Rp${Number(totalNilai).toLocaleString('id-ID')}
                        </td>
                    </tr>
                `;

                // console.log('totalQty:', totalQty, '| totalNilai:', totalNilai); // DEBUG
                // console.log('HTML final:', html); // DEBUG

                // ✅ PERBAIKAN: innerHTML di-set PALING AKHIR setelah html lengkap
                document.getElementById('tableBody').innerHTML = html;
            })
            .catch(err => {
                // console.error(err);
                document.getElementById('tableBody').innerHTML =
                    `<tr><td colspan="4" class="text-center text-danger py-4">Gagal mengambil data</td></tr>`;
            });
    });
});

function exportPDF() {
    const from = document.getElementById('from').value || '';
    const to = document.getElementById('to').value || '';
    const status = document.getElementById('status').value || '';
    const klien = document.getElementById('klien').value || '';
    window.open(`/laporan-pdf?from=${from}&to=${to}&status=${status}&klien=${klien}`, '_blank');
}
</script>

@endsection
