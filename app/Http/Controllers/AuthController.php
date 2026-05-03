<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    // 🔹 FORM LOGIN
    public function showLogin()
    {
        return view('auth.login');
    }

    // 🔹 PROSES LOGIN
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::attempt($credentials, $request->remember)) {
    //         $request->session()->regenerate();
    //         return redirect()->route('piutang.index');
    //     }

    //     return back()->withErrors([
    //         'email' => 'Email atau password salah',
    //     ])->withInput();
    // }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {

            // 🔥 CEK VERIFIKASI EMAIL
            if (!Auth::user()->hasVerifiedEmail()) {

                Auth::logout();

                return redirect()->route('verification.notice')
                    ->with('error', 'Silakan verifikasi email terlebih dahulu');
            }

            $request->session()->regenerate();

            return redirect()->route('piutang.index');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->withInput();
    }

    // 🔹 FORM REGISTER
    public function showRegister()
    {
        return view('auth.register');
    }

    // 🔹 PROSES REGISTER (FIXED)
    // public function register(Request $request)
    // {

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'phone' => 'required',
    //         'divisi' => 'required',
    //         'jabatan' => 'required',
    //         'password' => 'required|min:6|confirmed',
    //     ]);

    //     $user = User::create([
    //         'name'     => $request->name,
    //         'email'    => $request->email,
    //         'password' => Hash::make($request->password),
    //         'phone'    => $request->phone,
    //         'jabatan'  => $request->jabatan,
    //         'divisi'   => 'Keuangan', // 🔥 hardcode di sini
    //         'photo'    => $photoPath,
    //     ]);

    //     return redirect()->route('login')->with('success', 'Register berhasil');
    // }
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users',
    //         'phone' => 'required',
    //         'jabatan' => 'required',
    //         'password' => 'required|confirmed|min:6',
    //     ]);

    //     User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'divisi' => 'Keuangan', // default
    //         'jabatan' => $request->jabatan,
    //         'password' => bcrypt($request->password),
    //     ]);

    //     // 🔥 redirect + kirim pesan sukses
    //     // return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    //     event(new Registered($user));

    //     return redirect()->route('verification.notice')
    //         ->with('success', 'Silakan cek email untuk verifikasi akun');
    // }
    
// public function register(Request $request)
// {
//     $request->validate([
//         'name' => 'required',
//         'email' => 'required|email|unique:users,email',
//         'phone' => 'required',
//         'jabatan' => 'required',
//         'password' => 'required|confirmed|min:6',
//     ]);

//     $user = User::create([
//         'name' => $request->name,
//         'email' => $request->email,
//         'phone' => $request->phone,
//         'jabatan' => $request->jabatan,
//         'divisi' => 'Keuangan',
//         'password' => $request->password,
//     ]);

//     // ✅ LOGIN DULU
//     Auth::login($user);

//     // ✅ KIRIM EMAIL
//     event(new Registered($user));

//     return redirect()->route('verification.notice')
//         ->with('success', 'Silakan cek email untuk verifikasi akun');
// }
    
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email',
    //         'phone' => 'required',
    //         'jabatan' => 'required',
    //         'password' => 'required|confirmed|min:6',
    //     ]);
    
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'jabatan' => $request->jabatan,
    //         'divisi' => 'Keuangan',
    //         'password' => Hash::make($request->password), // ✅ WAJIB
    //     ]);
    
    //     event(new Registered($user)); // ✅ kirim email
    
    //     return redirect()->route('login')
    //         ->with('success', 'Registrasi berhasil! Cek email untuk verifikasi.');
    // }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'jabatan' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'jabatan' => $request->jabatan,
            'divisi' => 'Keuangan',
            'password' => Hash::make($request->password),
        ]);
    
        // 🔥 BUAT LINK VERIFIKASI
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );
    
        // 🔥 KIRIM EMAIL KE USER
        Http::withHeaders([
            'api-key' => env('MAIL_PASSWORD'),
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => env('MAIL_FROM_ADDRESS'),
                'name' => env('MAIL_FROM_NAME'),
            ],
            'to' => [
                [
                    'email' => $user->email,
                    'name' => $user->name
                ]
            ],
            'subject' => 'Verifikasi Email',
            'htmlContent' => "
                <h2>Halo {$user->name}</h2>
                <p>Silakan klik tombol di bawah untuk verifikasi akun:</p>
                <a href='{$verificationUrl}' 
                   style='padding:10px 15px;background:black;color:white;text-decoration:none;'>
                   Verifikasi Email
                </a>
            ",
        ]);
    
        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Cek email untuk verifikasi.');
    }
    // 🔹 LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
