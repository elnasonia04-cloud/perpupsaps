<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    // Tarif denda per hari: Rp2.000
    public const DENDA_PER_HARI = 2000;

    protected $fillable = [
        'book_id',
        'member_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali',
        'status',
        'denda',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali' => 'date',
        'denda' => 'integer',
    ];

    // Agar atribut virtual ikut dikirim ke Vue
    protected $appends = [
        'hari_terlambat',
        'perkiraan_denda',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Menghitung jumlah hari keterlambatan.
     */
    protected function hariTerlambat(): Attribute
    {
        return Attribute::make(get: function () {
            $jatuhTempo = Carbon::parse($this->tanggal_kembali_rencana)
                ->startOfDay();

            // Jika sudah dikembalikan, gunakan tanggal kembali.
            // Jika belum, gunakan tanggal hari ini.
            $acuan = $this->tanggal_kembali
                ? Carbon::parse($this->tanggal_kembali)->startOfDay()
                : today();

            // Jika belum melewati jatuh tempo, tidak terlambat.
            if ($acuan->lessThanOrEqualTo($jatuhTempo)) {
                return 0;
            }

            // Hitung selisih hari keterlambatan.
            return (int) $jatuhTempo->diffInDays($acuan);
        });
    }

    /**
     * Menghitung perkiraan denda:
     * hari terlambat x tarif denda per hari.
     */
    protected function perkiraanDenda(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->hari_terlambat * self::DENDA_PER_HARI
        );
    }
}
