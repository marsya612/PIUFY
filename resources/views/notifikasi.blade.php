@extends('layouts.app')
@section('content')
<div class="container p-4">
    <h4 class="fw-semibold mb-3">Notifikasi Pengingat</h4>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @forelse($notifikasi as $item)
                <div class="border-bottom py-2 d-flex justify-content-between align-items-center"
                     id="notif-{{ $item->id }}">
                    <div>
                        <strong>{{ $item->nama_klien }}</strong><br>
                        {{ $item->nama_proyek }}<br>
                        <span class="text-muted">
                            Jatuh tempo {{ $item->sisaHari }} hari lagi
                        </span>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary"
                            onclick="bacaNotif({{ $item->id }})">
                        ✓ Tandai Dibaca
                    </button>
                </div>
            @empty
                <div class="text-center text-muted">
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
            
            // ← ganti tombol jadi tanda sudah dibaca
            el.querySelector('button').outerHTML = 
                '<span class="text-success fw-semibold">✓ Sudah Dibaca</span>';
            
            // ← redup itemnya
            el.style.opacity = '0.4';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection
