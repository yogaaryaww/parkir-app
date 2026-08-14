<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom kendaraan_id sebagai nullable terlebih dahulu
        Schema::table('transaksi_parkir', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_parkir', 'kendaraan_id')) {
                $table->unsignedBigInteger('kendaraan_id')->nullable()->after('id');
            }
        });

        // 2. Backfill data transaksi lama yang belum memiliki kendaraan_id
        $transaksiTanpaKendaraan = DB::table('transaksi_parkir')
            ->whereNull('kendaraan_id')
            ->get();

        foreach ($transaksiTanpaKendaraan as $transaksi) {
            $platBersih = strtoupper(trim(preg_replace('/\s+/', ' ', $transaksi->plat_nomor)));

            // Cari kendaraan berdasarkan plat_nomor
            $kendaraan = DB::table('kendaraan')
                ->where('plat_nomor', $platBersih)
                ->first();

            if (!$kendaraan) {
                // Jika belum ada di master kendaraan, buat otomatis agar data lama tidak hilang / rusak
                $kategoriId = $transaksi->kategori_kendaraan_id;
                if (!$kategoriId || !DB::table('kategori_kendaraan')->where('id', $kategoriId)->exists()) {
                    $kategoriFirst = DB::table('kategori_kendaraan')->first();
                    $kategoriId = $kategoriFirst ? $kategoriFirst->id : 1;
                }

                $kendaraanId = DB::table('kendaraan')->insertGetId([
                    'plat_nomor' => $platBersih,
                    'kategori_kendaraan_id' => $kategoriId,
                    'nama_pemilik' => 'Tamu / Umum (Auto-Backfill)',
                    'keterangan' => 'Dibuat otomatis dari data transaksi lama',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $kendaraanId = $kendaraan->id;
            }

            // Update transaksi_parkir dengan kendaraan_id
            DB::table('transaksi_parkir')
                ->where('id', $transaksi->id)
                ->update(['kendaraan_id' => $kendaraanId]);
        }

        // 3. Tambahkan foreign key constraint
        Schema::table('transaksi_parkir', function (Blueprint $table) {
            $table->foreign('kendaraan_id')
                ->references('id')
                ->on('kendaraan')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_parkir', function (Blueprint $table) {
            $table->dropForeign(['kendaraan_id']);
            $table->dropColumn('kendaraan_id');
        });
    }
};
