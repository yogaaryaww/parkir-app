<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'aktivitas',
        'deskripsi',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper static method untuk mencatat log aktivitas
     */
    public static function catat($aktivitas, $deskripsi = null)
    {
        try {
            self::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'aktivitas' => $aktivitas,
                'deskripsi' => $deskripsi,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Ignore error agar tidak menghambat flow utama
        }
    }
}
