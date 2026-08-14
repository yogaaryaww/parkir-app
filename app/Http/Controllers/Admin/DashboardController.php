<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KategoriKendaraan;
use App\Models\AreaParkir;
use App\Models\TransaksiParkir;
use App\Models\LogAktivitas;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUser = User::count();
        $totalKategori = KategoriKendaraan::count();
        $totalArea = AreaParkir::count();
        
        $transaksiHariIni = TransaksiParkir::whereDate('waktu_masuk', Carbon::today())->count();
        $pendapatanHariIni = TransaksiParkir::whereDate('waktu_keluar', Carbon::today())
            ->where('status', 'selesai')
            ->sum('total_bayar');

        $transaksiTerakhir = TransaksiParkir::with(['kendaraan.kategoriKendaraan', 'kategoriKendaraan', 'areaParkir'])
            ->latest()
            ->take(5)
            ->get();

        $logTerakhir = LogAktivitas::with('user')
            ->latest()
            ->take(5)
            ->get();

        $areaParkir = AreaParkir::all();

        return view('admin.dashboard', compact(
            'totalUser',
            'totalKategori',
            'totalArea',
            'transaksiHariIni',
            'pendapatanHariIni',
            'transaksiTerakhir',
            'logTerakhir',
            'areaParkir'
        ));
    }
}
