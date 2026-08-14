<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = [
        'plat_nomor',
        'kategori_kendaraan_id',
        'nama_pemilik',
        'keterangan',
    ];

    public function kategoriKendaraan()
    {
        return $this->belongsTo(KategoriKendaraan::class, 'kategori_kendaraan_id');
    }

    public function transaksiParkir()
    {
        return $this->hasMany(TransaksiParkir::class, 'kendaraan_id');
    }
}
