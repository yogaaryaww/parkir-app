<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TarifParkir;
use App\Models\KategoriKendaraan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class TarifParkirController extends Controller
{
    public function index()
    {
        $tarif = TarifParkir::with('kategoriKendaraan')->latest()->get();
        return view('admin.tarif.index', compact('tarif'));
    }

    public function create()
    {
        $kategoriList = KategoriKendaraan::whereDoesntHave('tarif')->get();
        return view('admin.tarif.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_kendaraan_id' => 'required|exists:kategori_kendaraan,id|unique:tarif_parkir,kategori_kendaraan_id',
            'tarif_jam_pertama' => 'required|numeric|min:0',
            'tarif_jam_berikutnya' => 'required|numeric|min:0',
            'tarif_maksimal' => 'nullable|numeric|min:0',
        ]);

        $tarif = TarifParkir::create([
            'kategori_kendaraan_id' => $request->kategori_kendaraan_id,
            'tarif_jam_pertama' => $request->tarif_jam_pertama,
            'tarif_jam_berikutnya' => $request->tarif_jam_berikutnya,
            'tarif_maksimal' => $request->tarif_maksimal ?? 0,
        ]);

        LogAktivitas::catat('Tambah Tarif Parkir', 'Menambahkan tarif parkir untuk kategori ID: ' . $tarif->kategori_kendaraan_id);

        return redirect()->route('admin.tarif.index')->with('success', 'Tarif parkir berhasil ditambahkan.');
    }

    public function edit(TarifParkir $tarif)
    {
        $tarif->load('kategoriKendaraan');
        return view('admin.tarif.edit', compact('tarif'));
    }

    public function update(Request $request, TarifParkir $tarif)
    {
        $request->validate([
            'tarif_jam_pertama' => 'required|numeric|min:0',
            'tarif_jam_berikutnya' => 'required|numeric|min:0',
            'tarif_maksimal' => 'nullable|numeric|min:0',
        ]);

        $tarif->update([
            'tarif_jam_pertama' => $request->tarif_jam_pertama,
            'tarif_jam_berikutnya' => $request->tarif_jam_berikutnya,
            'tarif_maksimal' => $request->tarif_maksimal ?? 0,
        ]);

        LogAktivitas::catat('Update Tarif Parkir', 'Memperbarui tarif parkir ID: ' . $tarif->id);

        return redirect()->route('admin.tarif.index')->with('success', 'Tarif parkir berhasil diperbarui.');
    }

    public function destroy(TarifParkir $tarif)
    {
        $id = $tarif->id;
        $tarif->delete();

        LogAktivitas::catat('Hapus Tarif Parkir', 'Menghapus tarif parkir ID: ' . $id);

        return redirect()->route('admin.tarif.index')->with('success', 'Tarif parkir berhasil dihapus.');
    }
}
