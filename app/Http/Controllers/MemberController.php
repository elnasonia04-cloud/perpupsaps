<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    // Menampilkan daftar anggota dan pencarian
    public function index(Request $request)
    {
        $members = Member::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis_nim', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        })->latest()->paginate(10)->withQueryString();

        return inertia('Members/Index', [
            'members' => $members,
            'search' => $request->search,
        ]);
    }

    /**
     * Endpoint JSON untuk AJAX Live Search anggota.
     */
    public function search(Request $request)
    {
        $kataKunci = $request->query('q', '');

        $members = Member::where(function ($query) use ($kataKunci) {
            $query->where('nama', 'like', "%{$kataKunci}%")
                ->orWhere('nis_nim', 'like', "%{$kataKunci}%");
        })
            ->latest()
            ->limit(10)
            ->get([
                'id',
                'nama',
                'nis_nim',
                'no_hp',
                'foto',
            ]);

        return response()->json($members);
    }

    /**
     * Endpoint JSON untuk validasi realtime NIS/NIM.
     */
    public function cekNis(Request $request)
    {
        // NIS/NIM yang sedang diketik
        $nisNim = $request->query('nis_nim', '');

        // ID anggota yang diabaikan saat edit
        $abaikanId = $request->query('abaikan');

        // Memeriksa apakah NIS/NIM sudah digunakan
        $sudahDipakai = Member::where('nis_nim', $nisNim)
            ->when($abaikanId, function ($query, $id) {
                $query->where('id', '!=', $id);
            })
            ->exists();

        return response()->json([
            'tersedia' => ! $sudahDipakai,
        ]);
    }

    // Menampilkan halaman tambah anggota
    public function create()
    {
        return inertia('Members/Create');
    }

    // Menyimpan data anggota dan foto
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis_nim' => 'required|string|max:50|unique:members,nis_nim',
            'no_hp' => 'nullable|digits_between:8,15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')
                ->store('members', 'public');
        }

        Member::create($validated);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan');
    }

    // Menampilkan detail anggota
    public function show(Member $member)
    {
        //
    }

    // Menampilkan halaman edit anggota
    public function edit(Member $member)
    {
        return inertia('Members/Edit', [
            'member' => $member,
        ]);
    }

    // Memperbarui data anggota dan foto
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis_nim' => 'required|string|max:50|unique:members,nis_nim,' . $member->id,
            'no_hp' => 'nullable|digits_between:8,15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        if ($request->hasFile('foto')) {
            if ($member->foto) {
                Storage::disk('public')->delete($member->foto);
            }

            $validated['foto'] = $request->file('foto')
                ->store('members', 'public');
        } else {
            unset($validated['foto']);
        }

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui');
    }

    // Menghapus anggota dan foto
    public function destroy(Member $member)
    {
        if ($member->foto) {
            Storage::disk('public')->delete($member->foto);
        }

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil dihapus');
    }
}
