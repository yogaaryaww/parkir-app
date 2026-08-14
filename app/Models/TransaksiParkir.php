<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiParkir extends Model
{
    use HasFactory;

    protected $table = 'transaksi_parkir';

    protected $fillable = [
        'kode_tiket',
        'plat_nomor',
        'kendaraan_id',
        'kategori_kendaraan_id',
        'area_parkir_id',
        'waktu_masuk',
        'waktu_keluar',
        'durasi_jam',
        'total_bayar',
        'bayar',
        'kembalian',
        'status',
        'petugas_masuk_id',
        'petugas_keluar_id',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function kategoriKendaraan()
    {
        return $this->belongsTo(KategoriKendaraan::class, 'kategori_kendaraan_id');
    }

    public function areaParkir()
    {
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id');
    }

    public function petugasMasuk()
    {
        return $this->belongsTo(User::class, 'petugas_masuk_id');
    }

    public function petugasKeluar()
    {
        return $this->belongsTo(User::class, 'petugas_keluar_id');
    }
}
