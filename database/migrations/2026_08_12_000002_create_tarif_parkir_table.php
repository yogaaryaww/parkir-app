<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_parkir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_kendaraan_id')->constrained('kategori_kendaraan')->onDelete('cascade');
            $table->decimal('tarif_jam_pertama', 10, 2);
            $table->decimal('tarif_jam_berikutnya', 10, 2);
            $table->decimal('tarif_maksimal', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_parkir');
    }
};
