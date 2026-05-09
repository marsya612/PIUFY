@extends('layouts.app')
@section('content')
<div class="container p-4">
    <h4 class="fw-semibold mb-3">Notifikasi Pengingat</h4>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @forelse($notifikasi as $item)
            <div class="border-bottom py-2 d-flex justify-content-between align-items-center"
                id="notif-{{ $item->id }}"
                style="{{ $item->is_read ? 'opacity: 0.4' : '' }}">
                <div>
                    <span class="text-muted small">{{ $item->no_tagihan }}</span><br>
                    <strong>{{ $item->nama_klien }}</strong> &mdash; {{ $item->nama_proyek }}<br>
                    <span class="text-muted small">Jatuh tempo {{ $item->sisaHari }} hari lagi</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($item->is_read)
                        <span id="status-{{ $item->id }}" class="text-success fw-semibold">✓ Sudah Dibaca</span>
                    @else
                        <button id="status-{{ $item->id }}" class="btn btn-sm btn-outline-secondary"
                            onclick="bacaNotif({{ $item->id }})">
                            ✓ Tandai Dibaca
                        </button>
                    @endif
                    <!-- <button class="btn btn-sm btn-outline-danger"
                        onclick="hapusNotif({{ $item->id }})">
                        🗑 Hapus
                    </button> -->
                    <button class="btn btn-sm btn-outline-danger"
                        style="opacity: 1 !important;"
                        onclick="hapusNotif({{ $item->id }})">
                        🗑 Hapus
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center text-muted" id="empty-state">
                Tidak ada notifikasi
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function bacaNotif(id) {
    const token = document.querySelector('meta[name="csrf-token"]');

    fetch(`/notifikasi/baca/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            const el = document.getElementById(`notif-${id}`);
            const statusEl = document.getElementById(`status-${id}`);
            statusEl.outerHTML = `<span id="status-${id}" class="text-success fw-semibold">✓ Sudah Dibaca</span>`;
            el.style.opacity = '0.4';
        }
    })
    .catch(error => console.error('Error:', error));
}

// function hapusNotif(id) {
//     if (!confirm('Hapus notifikasi ini?')) return;

//     const token = document.querySelector('meta[name="csrf-token"]');

//     fetch(`/notifikasi/hapus/${id}`, {
//         method: 'DELETE',
//         headers: {
//             'X-CSRF-TOKEN': token ? token.getAttribute('content') : '{{ csrf_token() }}',
//             'Content-Type': 'application/json'
//         }
//     })
//     .then(response => {
//         if (response.ok) {
//             const el = document.getElementById(`notif-${id}`);
//             el.remove();

//             // Tampilkan empty state jika tidak ada notif tersisa
//             const cardBody = document.querySelector('.card-body');
//             if (!cardBody.querySelector('[id^="notif-"]')) {
//                 cardBody.innerHTML = '<div class="text-center text-muted">Tidak ada notifikasi</div>';
//             }
//         }
//     })
//     .catch(error => console.error('Error:', error));
// }
// function hapusNotif(id) {
//     if (!confirm('Hapus notifikasi ini?')) return;
//     const token = document.querySelector('meta[name="csrf-token"]');
//     fetch(`/notifikasi/hapus/${id}`, {
//         method: 'DELETE',
//         headers: {
//             'X-CSRF-TOKEN': token ? token.getAttribute('content') : '{{ csrf_token() }}',
//             'Content-Type': 'application/json'
//         }
//     })
//     .then(response => response.json())  // ← parse dulu
//     .then(data => {
//         if (data.success) {             // ← cek dari data, bukan response
//             const el = document.getElementById(`notif-${id}`);
//             if (el) {
//                 el.remove();
//                 const cardBody = document.querySelector('.card-body');
//                 if (!cardBody.querySelector('[id^="notif-"]')) {
//                     cardBody.innerHTML = '<div class="text-center text-muted">Tidak ada notifikasi</div>';
//                 }
//             } else {
//                 console.error('Element tidak ditemukan: notif-' + id);
//             }
//         }
//     })
//     .catch(error => console.error('Error:', error));
// }
function hapusNotif(id) {
    if (!confirm('Hapus notifikasi ini?')) return;
    const token = document.querySelector('meta[name="csrf-token"]');
    fetch(`/notifikasi/hapus/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Status code:', response.status); // ← cek ini
        return response.json();
    })
    .then(data => {
        console.log('Data dari server:', data); // ← cek ini
        if (data.success) {
            const el = document.getElementById(`notif-${id}`);
            console.log('Element ditemukan:', el); // ← cek ini
            if (el) {
                el.remove();
                const cardBody = document.querySelector('.card-body');
                if (!cardBody.querySelector('[id^="notif-"]')) {
                    cardBody.innerHTML = '<div class="text-center text-muted">Tidak ada notifikasi</div>';
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
