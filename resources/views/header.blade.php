<!-- <header class="bg-white border-bottom px-4 py-2"> -->
<header style="background-color: #13094d;" class="border-bottom px-4 py-2">
    <div class="d-flex align-items-center justify-content-between">

        <!-- LEFT: Logo -->
        <!-- <div class="d-flex align-items-center">
            <h5 class="mb-0 fw-bold">piufy</h5>
        </div> -->
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/logo.png') }}" alt="Piufy" style="height: 40px; width: auto;">
        </div>


        <!-- RIGHT: Notification + User -->
        <div class="d-flex align-items-center gap-3">
            
            {{-- Notification --}}
            <a href="{{ route('notifikasi') }}" class="position-relative text-decoration-none notif-bell">
                @php
                    $today = now()->startOfDay();
                    $jumlahNotif = \App\Models\Piutang::where('status', '!=', 'lunas')
                        ->where('user_id', Auth::id())
                        ->where('is_read', false)
                        ->get()
                        ->filter(function ($item) use ($today) {
                            $sisaHari = (int) $today->diffInDays(
                                \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay(),
                                false
                            );
                            return in_array($sisaHari, [7, 5, 3]);
                        })->count();
                @endphp
        
                @if($jumlahNotif > 0)
                    <i class="bi bi-bell-fill notif-bell-icon"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="font-size: 10px;">
                        {{ $jumlahNotif }}
                    </span>
                @else
                    <i class="bi bi-bell notif-bell-icon"></i>
                @endif
            </a>
        
            {{-- Profile --}}
            <a href="{{ route('profile') }}" class="text-decoration-none text-white">
                <div class="d-flex align-items-center px-3 py-1 rounded-pill profile-hover"
                     style="border: 1px solid rgba(255,255,255,0.3);">
                    <span class="fw-medium" style="font-size: 14px;">
                        {{ auth()->user()->name ?? 'User' }}
                    </span>
                </div>
            </a>
        
        </div>

    </div>
</header>
