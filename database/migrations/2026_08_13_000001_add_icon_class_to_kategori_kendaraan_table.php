<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kategori_kendaraan', function (Blueprint $table) {
            $table->string('icon_class', 100)->nullable()->default('fa-solid fa-car')->after('nama_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_kendaraan', function (Blueprint $table) {
            $table->dropColumn('icon_class');
        });
    }
};
