<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderPiutangMail extends Mailable
{
    use Queueable, SerializesModels;

    public $piutang;
    public $sisaHari;

    public function __construct($piutang, $sisaHari)
    {
        $this->piutang = $piutang;
        $this->sisaHari = $sisaHari;
    }

    // public function build()
    // {
    //     $subject = $this->getSubject();

    //     return $this->subject($subject)
    //         ->view('notifikasi')
    //         ->with([
    //             'piutang' => $this->piutang,
    //             'sisaHari' => $this->sisaHari
    //         ]);
    // }
    public function build()
    {
        $subject = $this->getSubject();
        return $this->subject($subject)
            ->view('resources.views.reminder_piutang') // ← ganti ini
            ->with([
                'piutang' => $this->piutang,
                'sisaHari' => $this->sisaHari
            ]);
    }

    private function getSubject()
    {
        return match ($this->sisaHari) {
            7 => "Reminder: 7 Hari Menuju Jatuh Tempo",
            5 => "Reminder: 5 Hari Menuju Jatuh Tempo",
            3 => "Reminder: 3 Hari Menuju Jatuh Tempo",
            default => "Pengingat Pembayaran",
        };
    }
}
