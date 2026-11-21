<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LoanController extends Controller
{
    // Menampilkan daftar peminjaman
    public function index()
    {
        $loans = Loan::with(['member', 'book'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Loans/Index', [
            'loans' => $loans,
        ]);
    }

    // Menampilkan form peminjaman
    public function create()
    {
        $members = Member::orderBy('nama')->get();

        $books = Book::where('stok', '>', 0)
            ->orderBy('judul')
            ->get();

        return Inertia::render('Loans/Create', [
            'members' => $members,
            'books' => $books,
        ]);
    }

    // Menyimpan transaksi peminjaman
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
        ]);

        // Memeriksa batas maksimal 3 peminjaman aktif
        $totalPinjaman = Loan::where('member_id', $validated['member_id'])
            ->where('status', 'dipinjam')
            ->count();

        if ($totalPinjaman >= 3) {
            return back()->withErrors([
                'member_id' => 'Anggota sudah mencapai batas maksimal 3 buku yang sedang dipinjam.',
            ]);
        }

        $book = Book::findOrFail($validated['book_id']);

        // Memeriksa ketersediaan stok buku
        if ($book->stok < 1) {
            return back()->withErrors([
                'book_id' => 'Stok buku tidak tersedia.',
            ]);
        }

        // Menyimpan transaksi dan mengurangi stok
        DB::transaction(function () use ($validated, $book) {
            Loan::create([
                'member_id' => $validated['member_id'],
                'book_id' => $validated['book_id'],
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(14)->toDateString(),
                'status' => 'dipinjam',
            ]);

            $book->decrement('stok');
        });

        return redirect()
            ->route('loans.index')
            ->with('success', 'Transaksi peminjaman berhasil disimpan.');
    }

    // Memproses pengembalian buku
    public function kembalikan(Loan $loan)
    {
        // Memeriksa apakah buku sudah dikembalikan
        if ($loan->status === 'dikembalikan') {
            return back()->withErrors([
                'loan' => 'Buku ini sudah dikembalikan sebelumnya.',
            ]);
        }

        // Menghitung keterlambatan dan denda
        $hariTerlambat = $loan->hari_terlambat;
        $denda = $loan->perkiraan_denda;

        // Menyimpan pengembalian dan menambah stok buku
        DB::transaction(function () use ($loan, $denda) {
            $loan->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => today()->toDateString(),
                'denda' => $denda,
            ]);

            $loan->book->increment('stok');
        });

        // Membuat pesan berhasil
        $pesan = 'Buku berhasil dikembalikan. Stok buku bertambah 1.';

        if ($denda > 0) {
            $pesan .= ' Terlambat ' . $hariTerlambat
                . ' Hari, denda Rp'
                . number_format($denda, 0, ',', '.') . '.';
        }

        return back()->with('success', $pesan);
    }
}
