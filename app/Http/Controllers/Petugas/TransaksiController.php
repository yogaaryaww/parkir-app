<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TransaksiParkir;
use App\Models\KategoriKendaraan;
use App\Models\AreaParkir;
use App\Models\TarifParkir;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    /**
     * Halaman Transaksi Kendaraan Masuk
     */
    public function masukForm()
    {
        $kategoriList = KategoriKendaraan::with('tarif')->get();
        $areaList = AreaParkir::all();
        $kendaraanAktif = TransaksiParkir::with(['kategoriKendaraan', 'areaParkir', 'petugasMasuk'])
            ->where('status', 'masuk')
            ->latest()
            ->take(10)
            ->get();

        return view('petugas.masuk', compact('kategoriList', 'areaList', 'kendaraanAktif'));
    }

    /**
     * Simpan Transaksi Kendaraan Masuk
     */
    public function masukStore(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'kategori_kendaraan_id' => 'required|exists:kategori_kendaraan,id',
            'area_parkir_id' => 'required|exists:area_parkir,id',
        ], [
            'plat_nomor.required' => 'Plat nomor kendaraan wajib diisi.',
            'kategori_kendaraan_id.required' => 'Pilih jenis/kategori kendaraan.',
            'area_parkir_id.required' => 'Pilih area lokasi parkir.',
        ]);

        $platNomor = strtoupper(trim(preg_replace('/\s+/', ' ', $request->plat_nomor)));

        // Cek apakah plat nomor sudah tercatat aktif di area parkir
        $existing = TransaksiParkir::where('plat_nomor', $platNomor)
            ->where('status', 'masuk')
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', "Kendaraan dengan Plat Nomor '{$platNomor}' masih tercatat aktif di area parkir (Kode Tiket: {$existing->kode_tiket}).");
        }

        // Cek Kapasitas Area Parkir
        $area = AreaParkir::findOrFail($request->area_parkir_id);
        if ($area->terisi >= $area->kapasitas) {
            return back()->withInput()->with('error', "Area Parkir '{$area->nama_area}' sudah penuh! Silakan pilih area lain.");
        }

        // Generate Kode Tiket Unik
        $todayStr = Carbon::now()->format('Ymd');
        $lastToday = TransaksiParkir::whereDate('created_at', Carbon::today())->count() + 1;
        $kodeTiket = 'PRK-' . $todayStr . '-' . str_pad($lastToday, 4, '0', STR_PAD_LEFT);

        while (TransaksiParkir::where('kode_tiket', $kodeTiket)->exists()) {
            $lastToday++;
            $kodeTiket = 'PRK-' . $todayStr . '-' . str_pad($lastToday, 4, '0', STR_PAD_LEFT);
        }

        // Simpan Transaksi Masuk
        $transaksi = TransaksiParkir::create([
            'kode_tiket' => $kodeTiket,
            'plat_nomor' => $platNomor,
            'kategori_kendaraan_id' => $request->kategori_kendaraan_id,
            'area_parkir_id' => $request->area_parkir_id,
            'waktu_masuk' => Carbon::now(),
            'status' => 'masuk',
            'petugas_masuk_id' => auth()->id(),
        ]);

        // Tambahkan jumlah terisi di Area Parkir
        $area->increment('terisi');

        LogAktivitas::catat('Parkir Masuk', "Plat {$platNomor} masuk area {$area->nama_area} (Tiket: {$kodeTiket})");

        return redirect()->route('petugas.struk.masuk', $transaksi->id)
            ->with('success', "Kendaraan plat {$platNomor} berhasil dicatat masuk! Tiket: {$kodeTiket}");
    }

    /**
     * Halaman Transaksi Kendaraan Keluar
     */
    public function keluarForm(Request $request)
    {
        $transaksi = null;
        $kalkulasi = null;

        $q = trim($request->get('q'));

        if (!empty($q)) {
            $transaksi = TransaksiParkir::with(['kategoriKendaraan.tarif', 'areaParkir', 'petugasMasuk'])
                ->where('status', 'masuk')
                ->where(function ($query) use ($q) {
                    $query->where('kode_tiket', $q)
                        ->orWhere('plat_nomor', strtoupper($q));
                })
                ->first();

            if ($transaksi) {
                $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
                $waktuKeluar = Carbon::now();

                // Hitung durasi jam (pembulatan ke atas, minimal 1 jam)
                $diffInSeconds = $waktuMasuk->diffInSeconds($waktuKeluar);
                $durasiJam = (int) ceil($diffInSeconds / 3600);
                if ($durasiJam < 1) {
                    $durasiJam = 1;
                }

                $tarifObj = TarifParkir::where('kategori_kendaraan_id', $transaksi->kategori_kendaraan_id)->first();
                $tarifJam1 = $tarifObj ? (float)$tarifObj->tarif_jam_pertama : 2000;
                $tarifBerikutnya = $tarifObj ? (float)$tarifObj->tarif_jam_berikutnya : 1000;
                $tarifMaksimal = $tarifObj ? (float)$tarifObj->tarif_maksimal : 0;

                if ($durasiJam == 1) {
                    $totalBayar = $tarifJam1;
                } else {
                    $totalBayar = $tarifJam1 + (($durasiJam - 1) * $tarifBerikutnya);
                }

                if ($tarifMaksimal > 0 && $totalBayar > $tarifMaksimal) {
                    $totalBayar = $tarifMaksimal;
                }

                $kalkulasi = [
                    'waktu_keluar' => $waktuKeluar,
                    'durasi_jam' => $durasiJam,
                    'tarif_jam_pertama' => $tarifJam1,
                    'tarif_jam_berikutnya' => $tarifBerikutnya,
                    'total_bayar' => $totalBayar,
                ];
            } else {
                session()->now('error', "Tiket atau Plat Nomor '{$q}' tidak ditemukan atau sudah diproses keluar.");
            }
        }

        $kendaraanAktif = TransaksiParkir::with(['kategoriKendaraan', 'areaParkir'])
            ->where('status', 'masuk')
            ->latest()
            ->get();

        return view('petugas.keluar', compact('transaksi', 'kalkulasi', 'kendaraanAktif', 'q'));
    }

    /**
     * Proses Transaksi Pembayaran Kendaraan Keluar
     */
    public function keluarProcess(Request $request, TransaksiParkir $transaksi)
    {
        if ($transaksi->status === 'selesai') {
            return redirect()->route('petugas.struk.keluar', $transaksi->id)
                ->with('info', 'Transaksi ini sudah selesai diproses sebelumnya.');
        }

        $request->validate([
            'bayar' => 'required|numeric|min:0',
        ], [
            'bayar.required' => 'Jumlah uang bayar harus diisi.',
            'bayar.numeric' => 'Nominal bayar harus berupa angka.',
        ]);

        $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
        $waktuKeluar = Carbon::now();
        $diffInSeconds = $waktuMasuk->diffInSeconds($waktuKeluar);
        $durasiJam = (int) ceil($diffInSeconds / 3600);
        if ($durasiJam < 1) {
            $durasiJam = 1;
        }

        $tarifObj = TarifParkir::where('kategori_kendaraan_id', $transaksi->kategori_kendaraan_id)->first();
        $tarifJam1 = $tarifObj ? (float)$tarifObj->tarif_jam_pertama : 2000;
        $tarifBerikutnya = $tarifObj ? (float)$tarifObj->tarif_jam_berikutnya : 1000;
        $tarifMaksimal = $tarifObj ? (float)$tarifObj->tarif_maksimal : 0;

        if ($durasiJam == 1) {
            $totalBayar = $tarifJam1;
        } else {
            $totalBayar = $tarifJam1 + (($durasiJam - 1) * $tarifBerikutnya);
        }

        if ($tarifMaksimal > 0 && $totalBayar > $tarifMaksimal) {
            $totalBayar = $tarifMaksimal;
        }

        $bayar = (float) $request->bayar;

        if ($bayar < $totalBayar) {
            return back()->withInput()->with('error', 'Jumlah uang pembayaran kurang! Total Tagihan: Rp ' . number_format($totalBayar, 0, ',', '.'));
        }

        $kembalian = $bayar - $totalBayar;

        // Update record transaksi
        $transaksi->update([
            'waktu_keluar' => $waktuKeluar,
            'durasi_jam' => $durasiJam,
            'total_bayar' => $totalBayar,
            'bayar' => $bayar,
            'kembalian' => $kembalian,
            'status' => 'selesai',
            'petugas_keluar_id' => auth()->id(),
        ]);

        // Kurangi jumlah terisi pada area parkir
        if ($transaksi->areaParkir && $transaksi->areaParkir->terisi > 0) {
            $transaksi->areaParkir->decrement('terisi');
        }

        LogAktivitas::catat('Parkir Keluar', "Plat {$transaksi->plat_nomor} keluar. Total: Rp " . number_format($totalBayar, 0, ',', '.'));

        return redirect()->route('petugas.struk.keluar', $transaksi->id)
            ->with('success', 'Pembayaran parkir berhasil diproses! Struk siap dicetak.');
    }

    /**
     * Tampilan Struk Masuk (Cetak Tiket)
     */
    public function strukMasuk(TransaksiParkir $transaksi)
    {
        $transaksi->load(['kategoriKendaraan', 'areaParkir', 'petugasMasuk']);
        return view('petugas.struk_masuk', compact('transaksi'));
    }

    /**
     * Tampilan Struk Keluar (Cetak Pembayaran)
     */
    public function strukKeluar(TransaksiParkir $transaksi)
    {
        $transaksi->load(['kategoriKendaraan', 'areaParkir', 'petugasMasuk', 'petugasKeluar']);
        return view('petugas.struk_keluar', compact('transaksi'));
    }
}
