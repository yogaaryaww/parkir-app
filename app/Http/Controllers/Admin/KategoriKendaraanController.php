<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriKendaraan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class KategoriKendaraanController extends Controller
{
    public function index()
    {
        $kategori = KategoriKendaraan::with('tarif')->latest()->get();
        return view('admin.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori_kendaraan,nama_kategori',
            'keterangan' => 'nullable|string',
        ]);

        $kat = KategoriKendaraan::create([
            'nama_kategori' => $request->nama_kategori,
            'keterangan' => $request->keterangan,
        ]);

        LogAktivitas::catat('Tambah Kategori Kendaraan', 'Menambahkan kategori: ' . $kat->nama_kategori);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori kendaraan berhasil ditambahkan.');
    }

    public function edit(KategoriKendaraan $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriKendaraan $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori_kendaraan,nama_kategori,' . $kategori->id,
            'keterangan' => 'nullable|string',
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'keterangan' => $request->keterangan,
        ]);

        LogAktivitas::catat('Update Kategori Kendaraan', 'Memperbarui kategori: ' . $kategori->nama_kategori);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori kendaraan berhasil diperbarui.');
    }

    public function destroy(KategoriKendaraan $kategori)
    {
        $nama = $kategori->nama_kategori;
        $kategori->delete();

        LogAktivitas::catat('Hapus Kategori Kendaraan', 'Menghapus kategori: ' . $nama);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori kendaraan berhasil dihapus.');
    }
}
