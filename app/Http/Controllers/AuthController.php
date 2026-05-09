<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
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
    
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
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
            <!DOCTYPE html>
            <html>
            <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            </head>
            <body style='margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,sans-serif;'>
              <table width='100%' cellpadding='0' cellspacing='0' style='padding:40px 20px;'>
                <tr>
                  <td align='center'>
                    <table width='520' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);'>
                      
                      <!-- Header -->
                      <tr>
                        <td style='background:#0f1b3d;padding:36px 40px;text-align:center;'>
                          <h1 style='margin:0;color:#ffffff;font-size:28px;font-weight:800;letter-spacing:2px;'>PiUFY</h1>
                          <p style='margin:6px 0 0;color:#a0aec0;font-size:13px;letter-spacing:1px;'>Piutang Management System</p>
                        </td>
                      </tr>
            
                      <!-- Body -->
                      <tr>
                        <td style='padding:40px 40px 20px;'>
                          <h2 style='margin:0 0 12px;color:#0f1b3d;font-size:22px;'>Halo, {$user->name} 👋</h2>
                          <p style='margin:0 0 24px;color:#4a5568;font-size:15px;line-height:1.7;'>
                            Terima kasih telah mendaftar di <strong>Piufy</strong>. Klik tombol di bawah untuk memverifikasi email kamu dan mulai menggunakan akun.
                          </p>
            
                          <!-- Button -->
                          <table cellpadding='0' cellspacing='0' style='margin:0 0 32px;'>
                            <tr>
                              <td style='background:#0f1b3d;border-radius:10px;'>
                                <a href='{$verificationUrl}' 
                                   style='display:inline-block;padding:14px 32px;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;letter-spacing:0.5px;'>
                                  ✦ Verifikasi Email Sekarang
                                </a>
                              </td>
                            </tr>
                          </table>
            
                          <p style='margin:0;color:#718096;font-size:13px;line-height:1.6;'>
                            Link ini akan kedaluwarsa dalam <strong>60 menit</strong>. Jika kamu tidak merasa mendaftar, abaikan email ini.
                          </p>
                        </td>
                      </tr>
            
                      <!-- Footer -->
                      <tr>
                        <td style='padding:24px 40px;border-top:1px solid #e2e8f0;text-align:center;'>
                          <p style='margin:0;color:#a0aec0;font-size:12px;'>© 2026 Piufy · Piutang Management System</p>
                        </td>
                      </tr>
            
                    </table>
                  </td>
                </tr>
              </table>
            </body>
            </html>
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
