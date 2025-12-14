<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamars';

    protected $fillable = [
    'nama_kamar', 'kapasitas','jenis_kelamin'
    ];

    public function riwayat() {
        return $this->hasMany(KamarSiswa::class);
    }
    public function penghuniAktif() {
        return $this->hasMany(KamarSiswa::class, 'kamar_id')->whereNull('tgl_keluar');
    }
}
