<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\KategoriKendaraan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::with('kategoriKendaraan');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('plat_nomor', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%");
            });
        }

        $kendaraan = $query->latest()->paginate(10)->withQueryString();

        return view('admin.kendaraan.index', compact('kendaraan'));
    }

    public function create()
    {
        $kategoriList = KategoriKendaraan::orderBy('nama_kategori')->get();
        return view('admin.kendaraan.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor'            => 'required|string|max:15|unique:kendaraan,plat_nomor',
            'kategori_kendaraan_id' => 'required|exists:kategori_kendaraan,id',
            'nama_pemilik'          => 'required|string|max:100',
            'keterangan'            => 'nullable|string',
        ], [
            'plat_nomor.required'            => 'Plat nomor wajib diisi.',
            'plat_nomor.unique'              => 'Plat nomor sudah terdaftar.',
            'kategori_kendaraan_id.required' => 'Kategori kendaraan wajib dipilih.',
            'nama_pemilik.required'          => 'Nama pemilik wajib diisi.',
        ]);

        $kendaraan = Kendaraan::create([
            'plat_nomor'            => strtoupper(trim($request->plat_nomor)),
            'kategori_kendaraan_id' => $request->kategori_kendaraan_id,
            'nama_pemilik'          => $request->nama_pemilik,
            'keterangan'            => $request->keterangan,
        ]);

        LogAktivitas::catat(
            'Tambah Kendaraan',
            "Menambahkan kendaraan {$kendaraan->plat_nomor} atas nama {$kendaraan->nama_pemilik}"
        );

        return redirect()->route('admin.kendaraan.index')
            ->with('success', "Kendaraan {$kendaraan->plat_nomor} berhasil ditambahkan.");
    }

    public function edit(Kendaraan $kendaraan)
    {
        $kategoriList = KategoriKendaraan::orderBy('nama_kategori')->get();
        return view('admin.kendaraan.edit', compact('kendaraan', 'kategoriList'));
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $request->validate([
            'plat_nomor'            => ['required', 'string', 'max:15', Rule::unique('kendaraan')->ignore($kendaraan->id)],
            'kategori_kendaraan_id' => 'required|exists:kategori_kendaraan,id',
            'nama_pemilik'          => 'required|string|max:100',
            'keterangan'            => 'nullable|string',
        ], [
            'plat_nomor.unique'              => 'Plat nomor sudah digunakan kendaraan lain.',
            'kategori_kendaraan_id.required' => 'Kategori kendaraan wajib dipilih.',
        ]);

        $kendaraan->update([
            'plat_nomor'            => strtoupper(trim($request->plat_nomor)),
            'kategori_kendaraan_id' => $request->kategori_kendaraan_id,
            'nama_pemilik'          => $request->nama_pemilik,
            'keterangan'            => $request->keterangan,
        ]);

        LogAktivitas::catat(
            'Update Kendaraan',
            "Memperbarui data kendaraan {$kendaraan->plat_nomor}"
        );

        return redirect()->route('admin.kendaraan.index')
            ->with('success', "Data kendaraan {$kendaraan->plat_nomor} berhasil diperbarui.");
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $plat = $kendaraan->plat_nomor;
        $kendaraan->delete();

        LogAktivitas::catat('Hapus Kendaraan', "Menghapus data kendaraan: {$plat}");

        return redirect()->route('admin.kendaraan.index')
            ->with('success', "Kendaraan {$plat} berhasil dihapus.");
    }
}
