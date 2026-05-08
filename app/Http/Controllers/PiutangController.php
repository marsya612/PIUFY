<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Piutang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // ✅ tambahkan ini
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\ReminderPiutangMail;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PiutangController extends Controller
{

    public function index(Request $request)
    {
        $query = Piutang::where('user_id', Auth::id());

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_klien', 'like', '%' . $request->search . '%')
                ->orWhere('nama_proyek', 'like', '%' . $request->search . '%')
                ->orWhere('no_tagihan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->klien) {
            $query->where('nama_klien', $request->klien);
        }

        if ($request->status) {
            if ($request->status == 'lunas') {
                $query->where('status', 'lunas');
            } else {
                $query->where('status', '!=', 'lunas');

                if ($request->status == 'tertunggak') {
                    $query->whereDate('tanggal_jatuh_tempo', '<', now());
                } elseif ($request->status == 'segera') {
                    $query->whereBetween('tanggal_jatuh_tempo', [now(), now()->addDays(7)]);
                } elseif ($request->status == 'belum') {
                    $query->whereDate('tanggal_jatuh_tempo', '>', now()->addDays(7));
                }
            }
        }

        $piutangs = $query->latest()->get();

        $klienList = Piutang::where('user_id', Auth::id())
            ->select('nama_klien')
            ->distinct()
            ->pluck('nama_klien');

        return view('piutang', compact('piutangs', 'klienList')); // ✅ BALIK KE PIUTANG
    }

    public function dashboard()
    {
        // 🔥 ambil data sesuai user login
        $data = Piutang::where('user_id', Auth::id())->get();

        $today = now()->startOfDay();

        // 🔥 hitung status dinamis + sisa hari
        $data = $data->map(function ($item) use ($today) {

            $jatuhTempo = Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay();
            $sisaHari = (int) $today->diffInDays($jatuhTempo, false);

            // STATUS DINAMIS
            if ($item->status == 'lunas') {
                $status_label = 'lunas';
            } elseif ($sisaHari < 0) {
                $status_label = 'tertunggak';
            } elseif ($sisaHari <= 7) {
                $status_label = 'segera';
            } else {
                $status_label = 'belum';
            }

            $item->status_label = $status_label;
            $item->sisaHari = $sisaHari;

            return $item;
        });

        // 🔥 TOTAL PIUTANG (belum lunas)
        $totalPiutang = $data->where('status', '!=', 'lunas')->sum('nilai_tagihan');
        $totalTagihanAktif = $data->where('status', '!=', 'lunas')->count();

        // 🔥 TERTUNGGAK
        $totalTertunggak = $data->where('status_label', 'tertunggak')->sum('nilai_tagihan');
        $countTertunggak = $data->where('status_label', 'tertunggak')->count();

        // 🔥 JATUH TEMPO (≤ 7 hari)
        $totalJatuhTempo = $data->where('status_label', 'segera')->sum('nilai_tagihan');
        $countJatuhTempo = $data->where('status_label', 'segera')->count();

        // 🔥 LUNAS BULAN INI
        $lunasBulanIni = $data->filter(function ($item) {
            return $item->status == 'lunas' &&
                Carbon::parse($item->updated_at)->isCurrentMonth();
        });

        $totalLunas = $lunasBulanIni->sum('nilai_tagihan');
        $countLunas = $lunasBulanIni->count();

        // 🔥 DATA TERBARU
        $latest = $data->sortByDesc('tanggal_terbit')->take(5);

        // 🔥 PERSENTASE
        $totalAll = $data->count();

        $persenLunas = $totalAll > 0
            ? ($countLunas / $totalAll) * 100
            : 0;

        $persenTertunggak = $totalAll > 0
            ? ($countTertunggak / $totalAll) * 100
            : 0;

        return view('home', compact(
            'totalPiutang',
            'totalTagihanAktif',
            'totalTertunggak',
            'countTertunggak',
            'totalJatuhTempo',
            'countJatuhTempo',
            'totalLunas',
            'countLunas',
            'latest',
            'persenLunas',
            'persenTertunggak'
        ));
    }
    public function create()
    {
        return view('tambahtagihan');
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'no_tagihan' => 'required|unique:piutangs,no_tagihan',
    //         'nama_klien' => 'required',
    //         'nama_proyek' => 'required',
    //         'termin' => 'required',
    //         'nilai_tagihan' => 'required|numeric',
    //         'metode_pembayaran' => 'required',
    //         'tanggal_terbit' => 'required|date',
    //         'tanggal_jatuh_tempo' => 'required|date',
    //         'catatan' => 'nullable',
    //     ]);

    //     // default status
    //     $validated['status'] = 'belum';

    //     // 🔥 WAJIB: simpan sesuai user login
    //     $validated['user_id'] = Auth::id();

    //     Piutang::create($validated);

    //     return redirect()->route('piutang.index')
    //         ->with('success', 'Tagihan berhasil ditambahkan');
    // }
    public function store(Request $request)
    {
        $request->validate([
            'no_tagihan'          => 'required|string',
            // ⚠️ HAPUS 'unique:piutangs' — no_tagihan boleh sama (multi termin)
            // Tapi kombinasi no_tagihan + termin harus unik:
            'no_tagihan'          => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('piutangs')->where(function ($query) use ($request) {
                    return $query->where('termin', $request->termin);
                }),
            ],
            'nama_klien'          => 'required|string|max:255',
            'nama_proyek'         => 'required|string|max:255',
            'termin'              => 'required|string|max:100',
            'nilai_tagihan'       => 'required|numeric|min:0',
            'metode_pembayaran'   => 'required|in:Reguler,SKBDN',
            'tanggal_terbit'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_terbit',
            'catatan'             => 'nullable|string',
        ], [
            'no_tagihan.unique' => 'Kombinasi No. Tagihan dan Termin ini sudah ada.',
        ]);
    
        Piutang::create($request->only([
            'no_tagihan', 'nama_klien', 'nama_proyek', 'termin',
            'nilai_tagihan', 'metode_pembayaran',
            'tanggal_terbit', 'tanggal_jatuh_tempo', 'catatan',
        ]));
    
        return redirect()->route('piutang.index')
            ->with('success', 'Tagihan berhasil ditambahkan.');
    }
    
    /**
     * Lookup data tagihan berdasarkan no_tagihan.
     * Dipakai oleh AJAX di form create.
     */
    public function lookup(Request $request)
    {
        $no = $request->query('no_tagihan');
    
        // Ambil semua tagihan dengan no_tagihan yang sama, urutkan termin terbaru
        $tagihans = \App\Models\Piutang::where('no_tagihan', $no)
            ->orderBy('created_at', 'desc')
            ->get();
    
        if ($tagihans->isEmpty()) {
            return response()->json(['found' => false]);
        }
    
        $latest = $tagihans->first(); // data terbaru (untuk termin & metode)
        $first  = $tagihans->last();  // data pertama (untuk klien & proyek)
    
        // ── Hitung next termin ──────────────────────────────────────────────────
        // Asumsi format termin: "Termin 1", "Termin 2", dst.
        // Jika format berbeda, sesuaikan regex di bawah.
        $lastTermin  = $latest->termin ?? '';
        $nextTermin  = $lastTermin;   // fallback
    
        if (preg_match('/(\d+)\s*$/', $lastTermin, $matches)) {
            $num        = (int) $matches[1] + 1;
            $nextTermin = preg_replace('/\d+\s*$/', $num, $lastTermin);
        } else {
            // Jika tidak ada angka di akhir, tambahkan " 2"
            $nextTermin = trim($lastTermin) . ' 2';
        }
    
        return response()->json([
            'found'              => true,
            'nama_klien'         => $first->nama_klien,
            'nama_proyek'        => $first->nama_proyek,
            'metode_pembayaran'  => $first->metode_pembayaran,
            'next_termin'        => $nextTermin,
        ]);
    }
    public function edit($id)
    {
        $piutang = Piutang::findOrFail($id);
        return view('edittagihan', compact('piutang'));
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([

            'nama_klien' => 'required',
            'nama_proyek' => 'required',
            'termin' => 'required',
            'nilai_tagihan' => 'required|numeric',
            'metode_pembayaran' => 'required|in:Reguler,SKBDN',
            'tanggal_terbit' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'catatan' => 'nullable',
        ]);

        $piutang = Piutang::findOrFail($id);
        $piutang->update($request->all());
        $request->validate([
            'no_tagihan' => 'required|unique:piutangs,no_tagihan,' . $id,
        ]);

        return redirect()->route('piutang.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function data(Request $request)
    {
        // 🔥 FILTER BERDASARKAN USER LOGIN
        $query = Piutang::where('user_id', Auth::id());
        

        // FILTER TANGGAL
        if ($request->from) {
            $query->whereDate('tanggal_terbit', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('tanggal_terbit', '<=', $request->to);
        }

        // FILTER KLIEN
        if ($request->klien) {
            $query->where('nama_klien', $request->klien);
        }

        $data = $query->get()->map(function ($item) {

            $today = now()->startOfDay();
            $jatuhTempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay();

            $sisaHari = (int) $today->diffInDays($jatuhTempo, false);

            // STATUS DINAMIS
            if ($item->status == 'lunas') {
                $status = 'lunas';

            } elseif ($sisaHari < 0) {
                $status = 'tertunggak';

            } elseif ($sisaHari <= 3) {
                $status = 'segera';

            } else {
                $status = 'belum tempo';
            }

            return [
                'status' => $status,
                'nama_klien' => $item->nama_klien ?? '-',
                'nilai_tagihan' => (float) ($item->nilai_tagihan ?? 0),
                // 'nilai_piutang' => (float) ($item->nilai_tagihan ?? 0),
            ];
        });

        // FILTER STATUS
        if ($request->status) {
            $data = $data->where('status', $request->status)->values();
        }

        return response()->json($data);
    }

    public function laporan(Request $request)
    {
        // 🔥 FILTER DATA BERDASARKAN USER
        $query = Piutang::where('user_id', Auth::id());

        // FILTER TANGGAL
        if ($request->from && $request->to) {
            $query->whereBetween('tanggal_terbit', [$request->from, $request->to]);
        }

        // FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // FILTER KLIEN
        if ($request->klien) {
            $query->where('nama_klien', $request->klien);
        }

        $data = $query->get();

        // 🔥 FILTER KLIEN JUGA BERDASARKAN USER
        $klienList = Piutang::where('user_id', Auth::id())
            ->select('nama_klien')
            ->distinct()
            ->orderBy('nama_klien')
            ->pluck('nama_klien');

        return view('laporan', compact('data', 'klienList'));
    }

    public function markLunas($id)
    {
        $piutang = Piutang::findOrFail($id);

        $piutang->status = 'lunas';
        $piutang->save();

        return redirect()->route('piutang.index');
    }

    public function destroy($id)
    {
        $piutang = Piutang::findOrFail($id);
        $piutang->delete();

        return redirect()->route('piutang.index')
            ->with('success', 'Data piutang berhasil dihapus');
    }


    // 🔹 HALAMAN PROFILE
    // public function profile()
    // {
    //     $user = auth()->user();
    //     return view('profile', compact('user'));
    // }

    public function profile()
    {
        $user = auth()->user();
        // dd($user->photo); // ← cek hasilnya
        return view('profile', compact('user'));
    }

    // 🔹 HALAMAN EDIT PROFILE
    public function editProfile()
    {
        $user = auth()->user();
        return view('edit_profile', compact('user'));
    }

    // 🔹 UPDATE PROFILE


    // public function updateProfile(Request $request)
    // {
    //     $user = auth()->user();

    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email,' . $user->id,
    //         'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     // HANDLE UPLOAD FOTO
    //     if ($request->hasFile('photo')) {

    //         // hapus foto lama (optional)
    //         if ($user->photo) {
    //             Storage::delete($user->photo);
    //         }

    //         $path = $request->file('photo')->store('profile', 'public');

    //         $user->photo = $path;
    //     }

    //     // update data lain
    //     $user->update([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'jabatan' => $request->jabatan,
    //         // 'divisi' => $request->divisi,
    //         'photo' => $user->photo,
    //     ]);

    //     return redirect()->route('profile')->with('success', 'Profile updated');
    // }

    // public function updateProfile(Request $request)
    // {
    //     $user = auth()->user();
    
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email,' . $user->id,
    //         'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);
    
    //     if ($request->hasFile('photo')) {
    
    //         // hapus foto lama (FIXED)
    //         if ($user->photo) {
    //             Storage::disk('public')->delete($user->photo);
    //         }
    
    //         $path = $request->file('photo')->store('profile', 'public');
    //         $user->photo = $path;
    //     }
    
    //     $user->update([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'jabatan' => $request->jabatan,
    //         'photo' => $user->photo,
    //     ]);
    
    //     return redirect()->route('profile')->with('success', 'Profile updated');
    // }
    // public function updateProfile(Request $request)
    // {
    //     $user = auth()->user();
    
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email,' . $user->id,
    //         'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);
    
    //     if ($request->hasFile('photo')) {
    //         // hapus foto lama di Cloudinary
    //         if ($user->photo) {
    //             Cloudinary::destroy($user->photo);
    //         }
        
    //         // upload ke Cloudinary
    //         $result = Cloudinary::upload($request->file('photo')->getRealPath(), [
    //             'folder' => 'profile'
    //         ]);
        
    //         $user->photo = $result->getSecurePath();
    //     }
    
    //     $user->update([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'jabatan' => $request->jabatan,
    //         'photo' => $user->photo,
    //     ]);
    
    //     return redirect()->route('profile')->with('success', 'Profile updated');
    // }
    
    // public function updateProfile(Request $request)
    // {
    //     $user = auth()->user();
    
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email,' . $user->id,
    //         'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);
    
    //     if ($request->hasFile('photo')) {
    //         $imageData = base64_encode(
    //             file_get_contents($request->file('photo')->getRealPath())
    //         );
    
    //         $response = \Illuminate\Support\Facades\Http::withHeaders([
    //             'Authorization' => 'Client-ID ' . env('IMGUR_CLIENT_ID'),
    //         ])->post('https://api.imgur.com/3/image', [
    //             'image' => $imageData,
    //             'type' => 'base64',
    //         ]);
    
    //         if ($response->successful()) {
    //             $user->photo = $response->json()['data']['link'];
    //         }
    //     }
    
    //     $user->update([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'jabatan' => $request->jabatan,
    //         'photo' => $user->photo,
    //     ]);
    
    //     return redirect()->route('profile')->with('success', 'Profile updated');
    // }


    public function updateProfile(Request $request)
    {
        $user = auth()->user();
    
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('photo')) {
            $imageData = base64_encode(
                file_get_contents($request->file('photo')->getRealPath())
            );
        
            $response = \Illuminate\Support\Facades\Http::asForm()
                ->post('https://api.imgbb.com/1/upload?key=' . env('IMGBB_API_KEY'), [
                    'image' => $imageData,
                ]);
        
            // dd(env('IMGBB_API_KEY'), $response->json());
            if ($response->successful()) {
                $user->photo = $response->json()['data']['display_url'];
            }
        }
    
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'jabatan' => $request->jabatan,
            'photo' => $user->photo,
        ]);
    
        return redirect()->route('profile')->with('success', 'Profile updated');
    }
    
    public function exportPdf(Request $request)
    {
        // $query = Piutang::query();
        $query = Piutang::where('user_id', Auth::id());

        if ($request->from) {
            $query->whereDate('tanggal_terbit', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('tanggal_terbit', '<=', $request->to);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->klien) {
            $query->where('nama_klien', $request->klien);
        }

        $data = $query->get();

        // 🔥 ambil periode dari tanggal_terbit
        $minDate = $data->min('tanggal_terbit');
        $maxDate = $data->max('tanggal_terbit');

        // 🔥 load sekali aja
        $pdf = Pdf::loadView('laporan_pdf', [
            'data' => $data,
            'minDate' => $minDate,
            'maxDate' => $maxDate,
        ]);

        return $pdf->download('laporan-piutang.pdf');
    }

    // public function kirimReminder()
    // {
    //     $data = Piutang::where('status', '!=', 'lunas')->get();

    //     foreach ($data as $item) {

    //         $sisaHari = \Carbon\Carbon::now()
    //             ->diffInDays($item->tanggal_jatuh_tempo, false);

    //         if (in_array($sisaHari, [7,5,3])) {

    //             Mail::to('email@klien.com') // 🔥 ganti dengan email klien
    //                 ->send(new ReminderPiutangMail($item, $sisaHari));
    //         }
    //     }

    //     return "Reminder terkirim";
    // }

    public function kirimReminder()
    {
        $data = Piutang::where('status', '!=', 'lunas')
                       ->where('user_id', Auth::id())
                       ->get();
    
        foreach ($data as $item) {
            $sisaHari = (int) \Carbon\Carbon::now()
                ->diffInDays($item->tanggal_jatuh_tempo, false);
    
            if (in_array($sisaHari, [7, 5, 3])) {
                Mail::to(Auth::user()->email) // ← ambil email user yang login
                    ->send(new ReminderPiutangMail($item, $sisaHari));
            }
        }
    
        return "Reminder terkirim";
    }

    // public function notifikasi()
    // {
    //     $today = now();

    //     $notifikasi = Piutang::where('status', '!=', 'lunas')
    //         ->get()
    //         ->filter(function ($item) use ($today) {
    //             $sisaHari = $today->diffInDays($item->tanggal_jatuh_tempo, false);
    //             return in_array($sisaHari, [7,5,3]);
    //         })
    //         ->map(function ($item) use ($today) {
    //             $item->sisaHari = $today->diffInDays($item->tanggal_jatuh_tempo, false);
    //             return $item;
    //         });

    //     return view('notifikasi', compact('notifikasi'));
    // }
    // public function notifikasi()
    // {
    //     $today = now();
    
    //     $notifikasi = Piutang::where('status', '!=', 'lunas')
    //         ->where('user_id', Auth::id()) // ← tambah filter user
    //         ->get()
    //         ->filter(function ($item) use ($today) {
    //             $sisaHari = (int) $today->diffInDays($item->tanggal_jatuh_tempo, false);
    //             return in_array($sisaHari, [7, 5, 3]);
    //         })
    //         ->map(function ($item) use ($today) {
    //             $item->sisaHari = (int) $today->diffInDays($item->tanggal_jatuh_tempo, false);
    //             return $item;
    //         });
    
    //     return view('notifikasi', compact('notifikasi'));
    // }
    // public function notifikasi()
    // {
    //     $today = now()->startOfDay(); // ← tambah startOfDay()
    
    //     $notifikasi = Piutang::where('status', '!=', 'lunas')
    //         ->where('user_id', Auth::id())
    //         ->get()
    //         ->filter(function ($item) use ($today) {
    //             $sisaHari = (int) $today->diffInDays(
    //                 \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay(), // ← tambah startOfDay()
    //                 false
    //             );
    //             return in_array($sisaHari, [7, 5, 3]);
    //         })
    //         ->map(function ($item) use ($today) {
    //             $item->sisaHari = (int) $today->diffInDays(
    //                 \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay(), // ← tambah startOfDay()
    //                 false
    //             );
    //             return $item;
    //         });
    
    //     return view('notifikasi', compact('notifikasi'));
    // }
    public function notifikasi()
    {
        $today = now()->startOfDay();
    
        $notifikasi = Piutang::where('status', '!=', 'lunas')
            ->where('user_id', Auth::id())
            ->get()
            ->filter(function ($item) use ($today) {
                $sisaHari = (int) $today->diffInDays(
                    \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay(),
                    false
                );
                return in_array($sisaHari, [7, 5, 3]);
            })
            ->map(function ($item) use ($today) {
                $item->sisaHari = (int) $today->diffInDays(
                    \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay(),
                    false
                );
                return $item;
            });
    
        return view('notifikasi', compact('notifikasi'));
    }
    
    public function bacaNotif($id)
    {
        $piutang = Piutang::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();
    
        $piutang->is_read = true;
        $piutang->save();
    
        return response()->json(['success' => true]);
    }

}
