@extends('layouts.app')
@section('content')
<div class="container p-4">
    <h4 class="fw-semibold mb-3" style="color:#0f1b3d;">Notifikasi Pengingat</h4>
    <div class="card border-0" style="border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        <div class="card-body p-0">

            @forelse($notifikasi as $item)
            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                id="notif-{{ $item->id }}"
                style="
                    border-bottom: 1px solid #e2e8f0;
                    opacity: {{ $item->is_read ? '0.45' : '1' }};
                    transition: opacity 0.3s;
                    background: {{ $item->is_read ? '#f8fafc' : '#ffffff' }};
                ">
                <div>
                    <span class="small" style="color:#a0aec0;">{{ $item->no_tagihan }}</span><br>
                    <strong style="color:#0f1b3d;">{{ $item->nama_klien }}</strong>
                    <span style="color:#4a5568;"> &mdash; {{ $item->nama_proyek }}</span><br>
                    <span class="small" style="color:#718096;">
                        🕐 Jatuh tempo <strong>{{ $item->sisaHari }} hari</strong> lagi
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($item->is_read)
                        <span id="status-{{ $item->id }}" 
                              class="small fw-semibold px-3 py-1"
                              style="background:#e6f4ea;color:#2d7a4f;border-radius:20px;">
                            ✓ Sudah Dibaca
                        </span>
                    @else
                        <button id="status-{{ $item->id }}"
                            onclick="bacaNotif({{ $item->id }})"
                            class="btn btn-sm fw-semibold"
                            style="background:#0f1b3d;color:#fff;border-radius:8px;border:none;padding:6px 14px;">
                            ✓ Tandai Dibaca
                        </button>
                    @endif

                    <form action="{{ route('notifikasi.hapus', $item->id) }}" method="POST"
                        onsubmit="return confirm('Hapus notifikasi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm fw-semibold"
                            style="background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6;border-radius:8px;padding:6px 14px;">
                            🗑 Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-5" id="empty-state">
                <p style="color:#a0aec0;font-size:15px;">🔔 Tidak ada notifikasi</p>
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
            statusEl.outerHTML = `
                <span id="status-${id}" class="small fw-semibold px-3 py-1"
                      style="background:#e6f4ea;color:#2d7a4f;border-radius:20px;">
                    ✓ Sudah Dibaca
                </span>`;
            el.style.opacity = '0.45';
            el.style.background = '#f8fafc';
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
