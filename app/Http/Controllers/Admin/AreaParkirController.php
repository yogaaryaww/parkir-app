<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaParkir;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class AreaParkirController extends Controller
{
    public function index()
    {
        $area = AreaParkir::latest()->get();
        return view('admin.area.index', compact('area'));
    }

    public function create()
    {
        return view('admin.area.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_area' => 'required|string|max:50|unique:area_parkir,nama_area',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $area = AreaParkir::create([
            'nama_area' => $request->nama_area,
            'kapasitas' => $request->kapasitas,
            'terisi' => 0,
        ]);

        LogAktivitas::catat('Tambah Area Parkir', 'Menambahkan area parkir: ' . $area->nama_area . ' (Kapasitas: ' . $area->kapasitas . ')');

        return redirect()->route('admin.area.index')->with('success', 'Area parkir berhasil ditambahkan.');
    }

    public function edit(AreaParkir $area)
    {
        return view('admin.area.edit', compact('area'));
    }

    public function update(Request $request, AreaParkir $area)
    {
        $request->validate([
            'nama_area' => 'required|string|max:50|unique:area_parkir,nama_area,' . $area->id,
            'kapasitas' => 'required|integer|min:' . $area->terisi,
        ], [
            'kapasitas.min' => 'Kapasitas tidak boleh kurang dari jumlah kendaraan yang sedang terisi (' . $area->terisi . ').',
        ]);

        $area->update([
            'nama_area' => $request->nama_area,
            'kapasitas' => $request->kapasitas,
        ]);

        LogAktivitas::catat('Update Area Parkir', 'Memperbarui area parkir: ' . $area->nama_area);

        return redirect()->route('admin.area.index')->with('success', 'Area parkir berhasil diperbarui.');
    }

    public function destroy(AreaParkir $area)
    {
        if ($area->terisi > 0) {
            return back()->with('error', 'Area parkir tidak dapat dihapus karena masih terisi kendaraan.');
        }

        $nama = $area->nama_area;
        $area->delete();

        LogAktivitas::catat('Hapus Area Parkir', 'Menghapus area parkir: ' . $nama);

        return redirect()->route('admin.area.index')->with('success', 'Area parkir berhasil dihapus.');
    }
}
