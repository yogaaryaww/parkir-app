<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TransaksiParkir;
use App\Models\Kendaraan;
use App\Models\KategoriKendaraan;
use App\Models\AreaParkir;
use App\Models\TarifParkir;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Halaman Transaksi Kendaraan Masuk
     */
    public function masukForm()
    {
        $kategoriList = KategoriKendaraan::with('tarif')->get();
        $areaList = AreaParkir::all();
        $kendaraanList = Kendaraan::with('kategoriKendaraan')->orderBy('plat_nomor')->get();
        $kendaraanAktif = TransaksiParkir::with(['kendaraan.kategoriKendaraan', 'kategoriKendaraan', 'areaParkir', 'petugasMasuk'])
            ->where('status', 'masuk')
            ->latest()
            ->take(10)
            ->get();

        return view('petugas.masuk', compact('kategoriList', 'areaList', 'kendaraanList', 'kendaraanAktif'));
    }

    /**
     * Simpan Transaksi Kendaraan Masuk
     */
    public function masukStore(Request $request)
    {
        $platNomor = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $request->plat_nomor)));
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();

        // Validasi sesuai kondisi: kendaraan sudah ada vs kendaraan baru
        if (!$kendaraan) {
            $request->validate([
                'plat_nomor'            => 'required|string|max:15',
                'area_parkir_id'        => 'required|exists:area_parkir,id',
                'kategori_kendaraan_id' => 'required|exists:kategori_kendaraan,id',
                'nama_pemilik'          => 'required|string|max:100',
                'keterangan'            => 'nullable|string|max:255',
            ], [
                'plat_nomor.required'            => 'Plat nomor kendaraan wajib diisi.',
                'area_parkir_id.required'        => 'Pilih area lokasi parkir.',
                'kategori_kendaraan_id.required' => 'Pilih kategori jenis kendaraan untuk kendaraan baru.',
                'nama_pemilik.required'          => 'Nama pemilik wajib diisi untuk kendaraan baru.',
            ]);
        } else {
            $request->validate([
                'plat_nomor'     => 'required|string|max:15',
                'area_parkir_id' => 'required|exists:area_parkir,id',
            ], [
                'plat_nomor.required'     => 'Plat nomor kendaraan wajib diisi.',
                'area_parkir_id.required' => 'Pilih area lokasi parkir.',
            ]);
        }

        try {
            $transaksi = DB::transaction(function () use ($request, $platNomor, &$kendaraan) {
                // 1. Jika kendaraan belum terdaftar, buat record kendaraan baru
                if (!$kendaraan) {
                    $kendaraan = Kendaraan::create([
                        'plat_nomor'            => $platNomor,
                        'kategori_kendaraan_id' => $request->kategori_kendaraan_id,
                        'nama_pemilik'          => trim($request->nama_pemilik),
                        'keterangan'            => $request->keterangan ? trim($request->keterangan) : null,
                    ]);

                    LogAktivitas::catat(
                        'Registrasi Kendaraan Masuk',
                        "Mendaftarkan kendaraan baru {$kendaraan->plat_nomor} a.n {$kendaraan->nama_pemilik} melalui form Transaksi Masuk"
                    );
                }

                // 2. Cek apakah kendaraan masih tercatat aktif di area parkir
                $existing = TransaksiParkir::where('kendaraan_id', $kendaraan->id)
                    ->where('status', 'masuk')
                    ->first();

                if ($existing) {
                    throw new \Exception("Kendaraan dengan Plat Nomor '{$platNomor}' ({$kendaraan->nama_pemilik}) masih tercatat aktif di area parkir (Kode Tiket: {$existing->kode_tiket}).");
                }

                // 3. Cek Kapasitas Area Parkir dengan lock
                $area = AreaParkir::lockForUpdate()->findOrFail($request->area_parkir_id);
                if ($area->terisi >= $area->kapasitas) {
                    throw new \Exception("Area Parkir '{$area->nama_area}' sudah penuh! Silakan pilih area lain.");
                }

                // 4. Generate Kode Tiket Unik
                $todayStr = Carbon::now()->format('Ymd');
                $lastToday = TransaksiParkir::whereDate('created_at', Carbon::today())->count() + 1;
                $kodeTiket = 'PRK-' . $todayStr . '-' . str_pad($lastToday, 4, '0', STR_PAD_LEFT);

                while (TransaksiParkir::where('kode_tiket', $kodeTiket)->exists()) {
                    $lastToday++;
                    $kodeTiket = 'PRK-' . $todayStr . '-' . str_pad($lastToday, 4, '0', STR_PAD_LEFT);
                }

                // 5. Simpan Transaksi Masuk terhubung ke master kendaraan
                $transaksi = TransaksiParkir::create([
                    'kode_tiket'            => $kodeTiket,
                    'plat_nomor'            => $kendaraan->plat_nomor,
                    'kendaraan_id'          => $kendaraan->id,
                    'kategori_kendaraan_id' => $kendaraan->kategori_kendaraan_id,
                    'area_parkir_id'        => $area->id,
                    'waktu_masuk'           => Carbon::now(),
                    'status'                => 'masuk',
                    'petugas_masuk_id'      => auth()->id(),
                ]);

                // 6. Tambahkan jumlah terisi di Area Parkir
                $area->increment('terisi');

                LogAktivitas::catat('Parkir Masuk', "Plat {$kendaraan->plat_nomor} ({$kendaraan->nama_pemilik}) masuk area {$area->nama_area} (Tiket: {$kodeTiket})");

                return $transaksi;
            });

            return redirect()->route('petugas.struk.masuk', $transaksi->id)
                ->with('success', "Kendaraan plat {$kendaraan->plat_nomor} berhasil dicatat masuk! Tiket: {$transaksi->kode_tiket}");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
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
            $transaksi = TransaksiParkir::with(['kendaraan.kategoriKendaraan.tarif', 'kategoriKendaraan.tarif', 'areaParkir', 'petugasMasuk'])
                ->where('status', 'masuk')
                ->where(function ($query) use ($q) {
                    $query->where('kode_tiket', $q)
                        ->orWhere('plat_nomor', strtoupper($q))
                        ->orWhereHas('kendaraan', function ($kQuery) use ($q) {
                            $kQuery->where('plat_nomor', strtoupper($q));
                        });
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

                $kategoriId = $transaksi->kendaraan ? $transaksi->kendaraan->kategori_kendaraan_id : $transaksi->kategori_kendaraan_id;
                $tarifObj = TarifParkir::where('kategori_kendaraan_id', $kategoriId)->first();
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

        $kendaraanAktif = TransaksiParkir::with(['kendaraan.kategoriKendaraan', 'kategoriKendaraan', 'areaParkir'])
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

        $kategoriId = $transaksi->kendaraan ? $transaksi->kendaraan->kategori_kendaraan_id : $transaksi->kategori_kendaraan_id;
        $tarifObj = TarifParkir::where('kategori_kendaraan_id', $kategoriId)->first();
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

        $platTampil = $transaksi->kendaraan ? $transaksi->kendaraan->plat_nomor : $transaksi->plat_nomor;
        LogAktivitas::catat('Parkir Keluar', "Plat {$platTampil} keluar. Total: Rp " . number_format($totalBayar, 0, ',', '.'));

        return redirect()->route('petugas.struk.keluar', $transaksi->id)
            ->with('success', 'Pembayaran parkir berhasil diproses! Struk siap dicetak.');
    }

    /**
     * Tampilan Struk Masuk (Cetak Tiket)
     */
    public function strukMasuk(TransaksiParkir $transaksi)
    {
        $transaksi->load(['kendaraan.kategoriKendaraan', 'kategoriKendaraan', 'areaParkir', 'petugasMasuk']);
        return view('petugas.struk_masuk', compact('transaksi'));
    }

    /**
     * Tampilan Struk Keluar (Cetak Pembayaran)
     */
    public function strukKeluar(TransaksiParkir $transaksi)
    {
        $transaksi->load(['kendaraan.kategoriKendaraan', 'kategoriKendaraan', 'areaParkir', 'petugasMasuk', 'petugasKeluar']);
        return view('petugas.struk_keluar', compact('transaksi'));
    }
}
