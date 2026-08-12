<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->string('plat_nomor', 15)->unique();
            $table->foreignId('kategori_kendaraan_id')->constrained('kategori_kendaraan')->onDelete('restrict');
            $table->string('nama_pemilik', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
