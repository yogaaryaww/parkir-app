<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaParkir extends Model
{
    use HasFactory;

    protected $table = 'area_parkir';

    protected $fillable = [
        'nama_area',
        'kapasitas',
        'terisi',
    ];

    public function transaksi()
    {
        return $this->hasMany(TransaksiParkir::class, 'area_parkir_id');
    }
}
