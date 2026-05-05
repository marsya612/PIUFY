<?php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Piutang;
use App\Models\User; // ← tambah ini
use App\Mail\ReminderPiutangMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::call(function () {
//     $data = Piutang::where('status', '!=', 'lunas')->get();

//     foreach ($data as $item) {
//         $sisaHari = (int) Carbon::now() // ← cast ke int
//             ->diffInDays($item->tanggal_jatuh_tempo, false);

//         if (in_array($sisaHari, [7, 5, 3])) {
//             $user = User::find($item->user_id); // ← ambil user pemilik piutang saja

//             if ($user && $user->email) {
//                 Mail::to($user->email)
//                     ->send(new ReminderPiutangMail($item, $sisaHari));
//             }
//         }
//     }
// })->dailyAt('07.00'); // ← tentukan jam pengiriman

Schedule::call(function () {
    $data = Piutang::where('status', '!=', 'lunas')->get();

    foreach ($data as $item) {
        $today = \Carbon\Carbon::now()->startOfDay(); // ← startOfDay()
        $sisaHari = (int) $today->diffInDays(
            \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay(), // ← startOfDay()
            false
        );

        if (in_array($sisaHari, [7, 5, 3])) {
            $user = \App\Models\User::find($item->user_id);

            if ($user && $user->email) {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new \App\Mail\ReminderPiutangMail($item, $sisaHari));
            }
        }
    }
})->dailyAt('07:00');
// <?php

// use Illuminate\Foundation\Inspiring;
// use Illuminate\Support\Facades\Artisan;
// use Illuminate\Support\Facades\Schedule;
// use App\Models\Piutang;
// use App\Mail\ReminderPiutangMail;
// use Illuminate\Support\Facades\Mail;
// use Carbon\Carbon;

// // command default
// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');


// // 🔥 SCHEDULER REMINDER PIUTANG
// Schedule::call(function () {

//     $data = Piutang::where('status', '!=', 'lunas')->get();

//     foreach ($data as $item) {

//         $sisaHari = Carbon::now()
//             ->diffInDays($item->tanggal_jatuh_tempo, false);

//         if (in_array($sisaHari, [7, 5, 3])) {

//             $users = User::all();

//             foreach ($users as $user) {

//                 if ($user->email) {

//                     Mail::to($user->email)
//                         ->send(new ReminderPiutangMail($item, $sisaHari));
//                 }
//             }
//         }
//     }

// })->daily();
