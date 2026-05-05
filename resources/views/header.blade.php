<header class="bg-white border-bottom px-4 py-2">
    <div class="d-flex align-items-center justify-content-between">

        <!-- LEFT: Logo -->
        <div class="d-flex align-items-center">
            <h5 class="mb-0 fw-bold">piufy</h5>
        </div>


        <!-- RIGHT: Notification + User -->
        <div class="d-flex align-items-center gap-3">

            <!-- Notification -->
            {{-- <div class="position-relative">
                <span style="font-size: 18px;">🔔</span>
            </div> --}}
            <!-- <a href="{{ route('notifikasi') }}" class="position-relative text-decoration-none">
                <span style="font-size: 18px;">🔔</span>
            </a> -->
            <a href="{{ route('notifikasi') }}" class="position-relative text-decoration-none">
                <span style="font-size: 18px;">🔔</span>
                
                @php
                    $today = now()->startOfDay();
                    $jumlahNotif = \App\Models\Piutang::where('status', '!=', 'lunas')
                        ->where('user_id', Auth::id())
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
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="font-size: 10px;">
                        {{ $jumlahNotif }}
                    </span>
                @endif
            </a>

            <!-- User Info -->
            <a href="{{ route('profile') }}" class="text-decoration-none text-dark">
                <div class="d-flex align-items-center bg-light px-3 py-1 rounded-pill profile-hover">

                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                            class="rounded-circle me-2 profile-img"
                            style="width:30px; height:30px; object-fit:cover;">
                    @else
                        <span class="me-2">👤</span>
                    @endif

                    <span class="fw-medium">
                        {{ auth()->user()->name ?? 'User' }}
                    </span>

                </div>
            </a>

        </div>

    </div>
</header>
