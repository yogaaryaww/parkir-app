<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKendaraan extends Model
{
    use HasFactory;

    protected $table = 'kategori_kendaraan';

    protected $fillable = [
        'nama_kategori',
        'icon_class',
        'keterangan',
    ];

    public function tarif()
    {
        return $this->hasOne(TarifParkir::class, 'kategori_kendaraan_id');
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiParkir::class, 'kategori_kendaraan_id');
    }
}
