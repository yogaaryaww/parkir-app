<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifParkir extends Model
{
    use HasFactory;

    protected $table = 'tarif_parkir';

    protected $fillable = [
        'kategori_kendaraan_id',
        'tarif_jam_pertama',
        'tarif_jam_berikutnya',
        'tarif_maksimal',
    ];

    public function kategoriKendaraan()
    {
        return $this->belongsTo(KategoriKendaraan::class, 'kategori_kendaraan_id');
    }
}
