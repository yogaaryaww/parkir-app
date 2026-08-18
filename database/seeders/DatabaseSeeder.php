<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KategoriKendaraan;
use App\Models\Kendaraan;
use App\Models\TarifParkir;
use App\Models\AreaParkir;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =================================================
        // 1. DATA USER
        // =================================================
        User::create([
            'nama'     => 'Administrator Parkir',
            'username' => 'admin',
            'email'    => 'admin@parkir.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'status'   => 'aktif',
        ]);

        User::create([
            'nama'     => 'agoy Petugas',
            'username' => 'petugas',
            'email'    => 'petugas@parkir.com',
            'password' => Hash::make('password'),
            'role'     => 'petugas',
            'status'   => 'aktif',
        ]);

        User::create([
            'nama'     => 'Owner Parkir',
            'username' => 'owner',
            'email'    => 'owner@parkir.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
            'status'   => 'aktif',
        ]);

        // =================================================
        // 2. KATEGORI KENDARAAN & TARIF
        // =================================================
        $motor = KategoriKendaraan::create([
            'nama_kategori' => 'Motor',
            'keterangan'    => 'Kendaraan roda 2 / sepeda motor',
        ]);
        TarifParkir::create([
            'kategori_kendaraan_id'  => $motor->id,
            'tarif_jam_pertama'      => 2000,
            'tarif_jam_berikutnya'   => 1000,
            'tarif_maksimal'         => 10000,
        ]);

        $mobil = KategoriKendaraan::create([
            'nama_kategori' => 'Mobil',
            'keterangan'    => 'Kendaraan roda 4',
        ]);
        TarifParkir::create([
            'kategori_kendaraan_id'  => $mobil->id,
            'tarif_jam_pertama'      => 5000,
            'tarif_jam_berikutnya'   => 3000,
            'tarif_maksimal'         => 30000,
        ]);

        $truk = KategoriKendaraan::create([
            'nama_kategori' => 'Truk / Bus',
            'keterangan'    => 'Kendaraan besar roda 6 atau lebih',
        ]);
        TarifParkir::create([
            'kategori_kendaraan_id'  => $truk->id,
            'tarif_jam_pertama'      => 10000,
            'tarif_jam_berikutnya'   => 5000,
            'tarif_maksimal'         => 60000,
        ]);

        // =================================================
        // 3. AREA PARKIR
        // =================================================
        AreaParkir::create([
            'nama_area' => 'Blok A - Motor',
            'kapasitas' => 50,
            'terisi'    => 0,
        ]);
        AreaParkir::create([
            'nama_area' => 'Blok B - Mobil',
            'kapasitas' => 30,
            'terisi'    => 0,
        ]);
        AreaParkir::create([
            'nama_area' => 'Blok C - Bus & Truk',
            'kapasitas' => 15,
            'terisi'    => 0,
        ]);

        // =================================================
        // 4. DATA CONTOH KENDARAAN TERDAFTAR
        // =================================================
        Kendaraan::create([
            'plat_nomor'            => 'B 1234 ABC',
            'kategori_kendaraan_id' => $motor->id,
            'nama_pemilik'          => 'Andi Santoso',
            'keterangan'            => 'Motor Honda Beat merah',
        ]);
        Kendaraan::create([
            'plat_nomor'            => 'D 5678 XY',
            'kategori_kendaraan_id' => $mobil->id,
            'nama_pemilik'          => 'Siti Nurhaliza',
            'keterangan'            => 'Mobil Toyota Avanza putih',
        ]);
        Kendaraan::create([
            'plat_nomor'            => 'Z 9999 BB',
            'kategori_kendaraan_id' => $truk->id,
            'nama_pemilik'          => 'CV Maju Jaya',
            'keterangan'            => 'Truk pengiriman barang',
        ]);
    }
}
