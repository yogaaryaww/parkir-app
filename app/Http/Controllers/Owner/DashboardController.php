<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\TransaksiParkir;
use App\Models\KategoriKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tglMulai = $request->get('tgl_mulai', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $tglSelesai = $request->get('tgl_selesai', Carbon::today()->format('Y-m-d'));
        $kategoriId = $request->get('kategori_kendaraan_id');

        $query = TransaksiParkir::with(['kategoriKendaraan', 'areaParkir', 'petugasMasuk', 'petugasKeluar'])
            ->whereDate('waktu_masuk', '>=', $tglMulai)
            ->whereDate('waktu_masuk', '<=', $tglSelesai);

        if (!empty($kategoriId)) {
            $query->where('kategori_kendaraan_id', $kategoriId);
        }

        $transaksiList = (clone $query)->latest()->paginate(15)->withQueryString();

        // Statistik Ringkasan Rekap
        $totalTransaksi = (clone $query)->count();
        $totalMasukAktif = (clone $query)->where('status', 'masuk')->count();
        $totalSelesai = (clone $query)->where('status', 'selesai')->count();
        $totalPendapatan = (clone $query)->where('status', 'selesai')->sum('total_bayar');

        $kategoriList = KategoriKendaraan::all();

        return view('owner.dashboard', compact(
            'transaksiList',
            'totalTransaksi',
            'totalMasukAktif',
            'totalSelesai',
            'totalPendapatan',
            'tglMulai',
            'tglSelesai',
            'kategoriId',
            'kategoriList'
        ));
    }

    public function printRekap(Request $request)
    {
        $tglMulai = $request->get('tgl_mulai', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $tglSelesai = $request->get('tgl_selesai', Carbon::today()->format('Y-m-d'));
        $kategoriId = $request->get('kategori_kendaraan_id');

        $query = TransaksiParkir::with(['kategoriKendaraan', 'areaParkir', 'petugasMasuk', 'petugasKeluar'])
            ->whereDate('waktu_masuk', '>=', $tglMulai)
            ->whereDate('waktu_masuk', '<=', $tglSelesai);

        if (!empty($kategoriId)) {
            $query->where('kategori_kendaraan_id', $kategoriId);
        }

        $transaksiList = $query->orderBy('waktu_masuk', 'asc')->get();

        $totalTransaksi = $transaksiList->count();
        $totalSelesai = $transaksiList->where('status', 'selesai')->count();
        $totalPendapatan = $transaksiList->where('status', 'selesai')->sum('total_bayar');

        $kategoriSelected = $kategoriId ? KategoriKendaraan::find($kategoriId) : null;

        return view('owner.print_rekap', compact(
            'transaksiList',
            'totalTransaksi',
            'totalSelesai',
            'totalPendapatan',
            'tglMulai',
            'tglSelesai',
            'kategoriSelected'
        ));
    }
}
