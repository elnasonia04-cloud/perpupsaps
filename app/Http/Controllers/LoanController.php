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

        $book = Book::findOrFail($validated['book_id']);

        if ($book->stok < 1) {
            return back()->withErrors([
                'book_id' => 'Stok buku tidak tersedia.',
            ]);
        }

        DB::transaction(function () use ($validated, $book) {

            Loan::create([
                'member_id' => $validated['member_id'],
                'book_id' => $validated['book_id'],
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(7)->toDateString(),
                'status' => 'dipinjam',
            ]);

            $book->decrement('stok');
        });

        return redirect()
            ->route('loans.index')
            ->with('success', 'Transaksi peminjaman berhasil disimpan.');
    }
}
