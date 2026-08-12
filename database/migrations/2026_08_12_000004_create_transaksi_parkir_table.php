<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_parkir', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket', 30)->unique();
            $table->string('plat_nomor', 15);
            $table->foreignId('kategori_kendaraan_id')->constrained('kategori_kendaraan');
            $table->foreignId('area_parkir_id')->constrained('area_parkir');
            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_keluar')->nullable();
            $table->integer('durasi_jam')->default(0);
            $table->decimal('total_bayar', 10, 2)->default(0);
            $table->decimal('bayar', 10, 2)->default(0);
            $table->decimal('kembalian', 10, 2)->default(0);
            $table->enum('status', ['masuk', 'selesai'])->default('masuk');
            $table->foreignId('petugas_masuk_id')->constrained('users');
            $table->foreignId('petugas_keluar_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_parkir');
    }
};
