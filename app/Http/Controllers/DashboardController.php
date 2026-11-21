<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // ---- Bagian 1: menghitung data ringkasan ----

        // Menghitung jumlah judul buku
        $totalBuku = Book::count();

        // Menghitung total seluruh stok buku
        $totalStok = (int) Book::sum('stok');

        // Menghitung jumlah anggota perpustakaan
        $totalAnggota = Member::count();

        // Menghitung jumlah peminjaman yang masih aktif
        $sedangDipinjam = Loan::where('status', 'dipinjam')
            ->count();

        // Menghitung jumlah peminjaman hari ini
        $peminjamanHariIni = Loan::where(
            'tanggal_pinjam',
            today()->toDateString()
        )->count();

        // Menghitung peminjaman aktif yang sudah melewati jatuh tempo
        $terlambat = Loan::where('status', 'dipinjam')
            ->whereDate('tanggal_kembali_rencana', '<', today())
            ->count();

        // Menghitung seluruh denda yang sudah tercatat
        $totalDenda = (int) Loan::sum('denda');


        // ---- Bagian 2: data grafik peminjaman 6 bulan terakhir ----

        // Nama bulan untuk label grafik
        $namaBulan = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ];

        // Menyiapkan daftar label dan jumlah peminjaman
        $label = [];
        $jumlah = [];

        // Mengambil data dari 5 bulan lalu sampai bulan ini
        for ($i = 11; $i >= 0; $i--) {

            // Tanggal awal bulan
            $awal = today()->startOfMonth()->subMonths($i);

            // Tanggal akhir bulan
            $akhir = $awal->copy()->endOfMonth();

            // Menambahkan nama bulan dan tahun ke label grafik
            $label[] = $namaBulan[$awal->month - 1]
                . ' ' . $awal->year;

            // Menghitung jumlah peminjaman pada bulan tersebut
            $jumlah[] = Loan::whereBetween('tanggal_pinjam', [
                $awal->toDateString(),
                $akhir->toDateString(),
            ])->count();
        }


        // ---- Bagian 3: mengirim data ke Dashboard.vue ----

        return inertia('Dashboard', [
            'statistik' => [
                'total_buku' => $totalBuku,
                'total_stok' => $totalStok,
                'total_anggota' => $totalAnggota,
                'sedang_dipinjam' => $sedangDipinjam,
                'peminjaman_hari_ini' => $peminjamanHariIni,
                'terlambat' => $terlambat,
                'total_denda' => $totalDenda,
            ],

            'grafik' => [
                'label' => $label,
                'jumlah' => $jumlah,
            ],
        ]);
    }
}
