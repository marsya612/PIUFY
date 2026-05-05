<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Piutang;
use App\Mail\ReminderPiutangMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class KirimReminderPiutang extends Command
{
    protected $signature = 'reminder:piutang';
    protected $description = 'Kirim email reminder piutang jatuh tempo';

    public function handle()
    {
        $data = Piutang::where('status', '!=', 'lunas')->get();

        foreach ($data as $item) {
            $sisaHari = (int) Carbon::now()
                ->diffInDays($item->tanggal_jatuh_tempo, false);

            if (in_array($sisaHari, [7, 5, 3])) {
                $user = \App\Models\User::find($item->user_id);

                if ($user) {
                    Mail::to($user->email)
                        ->send(new ReminderPiutangMail($item, $sisaHari));

                    $this->info("Reminder terkirim ke: {$user->email} untuk tagihan {$item->no_tagihan}");
                }
            }
        }

        $this->info('Selesai!');
    }
}
