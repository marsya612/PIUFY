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
                        <strong>{{ $item->nama_klien }}</strong><br>
                        {{ $item->nama_proyek }}<br>
                        <span class="text-muted">
                            Jatuh tempo {{ $item->sisaHari }} hari lagi
                        </span>
                    </div>
                    @if($item->is_read)
                        <span class="text-success fw-semibold">✓ Sudah Dibaca</span>
                    @else
                        <button class="btn btn-sm btn-outline-secondary"
                                onclick="bacaNotif({{ $item->id }})">
                            ✓ Tandai Dibaca
                        </button>
                    @endif
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
            el.querySelector('button').outerHTML = 
                '<span class="text-success fw-semibold">✓ Sudah Dibaca</span>';
            el.style.opacity = '0.4';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection
