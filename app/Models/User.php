<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transaksiMasuk()
    {
        return $this->hasMany(TransaksiParkir::class, 'petugas_masuk_id');
    }

    public function transaksiKeluar()
    {
        return $this->hasMany(TransaksiParkir::class, 'petugas_keluar_id');
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'user_id');
    }
}
