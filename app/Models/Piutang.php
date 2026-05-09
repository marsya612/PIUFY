<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Piutang extends Model
{

    // protected $fillable = [
    //     'no_tagihan',
    //     'nama_klien',
    //     'nama_proyek',
    //     'termin',
    //     'nilai_tagihan',
    //     'metode_pembayaran',
    //     'tanggal_terbit',
    //     'tanggal_jatuh_tempo',
    //     'catatan',
    //     'status', // 🔥 WAJIB TAMBAH INI
    // ];
//     protected $fillable = [
//     'user_id',
//     'no_tagihan',
//     'nama_klien',
//     'nama_proyek',
//     'termin',
//     'nilai_tagihan',
//     'metode_pembayaran',
//     'tanggal_terbit',
//     'tanggal_jatuh_tempo',
//     'status',
//     'catatan',
// ];
    protected $fillable = [
        'user_id',
        'no_tagihan',
        'nama_klien',
        'nama_proyek',
        'termin',
        'nilai_tagihan',
        'metode_pembayaran',
        'tanggal_terbit',
        'tanggal_jatuh_tempo',
        'status',
        'catatan',
        'is_read',        // ← tambahkan
        'is_dismissed',   // ← tambahkan
    ];


    public function getStatusAttribute($value)
    {
        // PRIORITAS: kalau sudah lunas, jangan diubah
        if ($value === 'lunas') {
            return 'lunas';
        }

        $today = Carbon::today();

        if ($this->tanggal_jatuh_tempo < $today) {
            return 'tertunggak';
        }

        if ($this->tanggal_jatuh_tempo <= $today->copy()->addDays(7)) {
            return 'segera';
        }

        return 'belum';
    }

    public function getTanggalFormatAttribute()
    {
        return Carbon::parse($this->tanggal)->format('d-m-Y');
    }
}

