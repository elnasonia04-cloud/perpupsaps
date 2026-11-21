<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Menampilkan daftar buku dan fitur pencarian.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $books = Book::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_buku', 'like', "%{$search}%")
                        ->orWhere('judul', 'like', "%{$search}%")
                        ->orWhere('pengarang', 'like', "%{$search}%")
                        ->orWhere('penerbit', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Books/Index', [
            'books' => $books,
            'search' => $search,
        ]);
    }

    /**
     * Endpoint JSON untuk AJAX Live Search buku.
     */
    public function search(Request $request)
    {
        $kataKunci = $request->query('q', '');

        $books = Book::where(function ($query) use ($kataKunci) {
            $query->where('kode_buku', 'like', "%{$kataKunci}%")
                ->orWhere('judul', 'like', "%{$kataKunci}%")
                ->orWhere('pengarang', 'like', "%{$kataKunci}%")
                ->orWhere('penerbit', 'like', "%{$kataKunci}%");
        })
            ->latest()
            ->limit(10)
            ->get([
                'id',
                'kode_buku',
                'judul',
                'pengarang',
                'penerbit',
                'stok',
            ]);

        return response()->json($books);
    }

    /**
     * Endpoint AJAX untuk validasi realtime kode buku.
     * Parameter abaikan digunakan saat mengedit buku.
     */
    public function cekKode(Request $request)
    {
        $kodeBuku = $request->query('kode_buku', '');
        $abaikanId = $request->query('abaikan');

        $query = Book::where('kode_buku', $kodeBuku);

        // Abaikan kode milik buku yang sedang diedit
        if ($abaikanId) {
            $query->where('id', '!=', $abaikanId);
        }

        $sudahDigunakan = $query->exists();

        return response()->json([
            'tersedia' => !$sudahDigunakan,
        ]);
    }

    /**
     * Menampilkan formulir tambah buku.
     */
    public function create()
    {
        return inertia('Books/Create');
    }

    /**
     * Menyimpan buku baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_buku' => 'required|string|max:255|unique:books,kode_buku',
            'judul'     => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit'  => 'nullable|string|max:255',
            'tahun'     => 'nullable|digits:4',
            'stok'      => 'required|integer|min:0',
        ]);

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(string $id)
    {
        $book = Book::findOrFail($id);

        return inertia('Books/Show', [
            'book' => $book,
        ]);
    }

    /**
     * Menampilkan formulir edit buku.
     */
    public function edit(string $id)
    {
        $book = Book::findOrFail($id);

        return inertia('Books/Edit', [
            'book' => $book,
        ]);
    }

    /**
     * Memperbarui data buku.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'kode_buku' => 'required|string|max:255|unique:books,kode_buku,' . $book->id,
            'judul'     => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit'  => 'nullable|string|max:255',
            'tahun'     => 'nullable|digits:4',
            'stok'      => 'required|integer|min:0',
        ]);

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Menghapus buku.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
