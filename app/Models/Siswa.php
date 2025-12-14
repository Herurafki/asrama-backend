<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Siswa extends Model
{
    use HasFactory;
    
    protected $table = 'siswas';

    protected $casts = [
        'tanggal_lahir' => 'date:Y-m-d',
        'tgl_masuk' => 'date:Y-m-d',
    ];

    protected $fillable = [
        'nama_lengkap',
        'nama_panggilan',
        'nis',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'kewarganegaraan',
        'status_keluarga',
        'status_orangtua',
        'anak_ke',
        'tgl_masuk',
        'kelas',
        'user_id',
        'foto', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orangtua(): HasOne
    {
        return $this->hasOne(Orangtua::class, 'user_id', 'user_id');
    }

    protected $appends = ['foto_url'];
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) return null;

        // Storage::url() -> "/storage/...."
        $path = Storage::url($this->foto);

        // jadikan ABSOLUT: http://127.0.0.1:8000/storage/...
        return url($path); // memakai APP_URL dari .env
    }


    public function perizinan()
    {
        return $this->hasMany(perizinan::class);
    }

    public function riwayatKamar() {
        return $this->hasMany(KamarSiswa::class);
    }
    public function kamarAktif() {
        return $this->hasOne(KamarSiswa::class, 'siswa_id')->whereNull('tgl_keluar');
    }
}
