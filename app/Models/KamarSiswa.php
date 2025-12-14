<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KamarSiswa extends Model
{
    //
    use HasFactory;

    protected $table = 'siswa_kamars';

    protected $fillable = [
        'siswa_id','kamar_id','tgl_masuk','tgl_keluar','keterangan'
    ];

    protected $casts = [
        'tgl_masuk' => 'datetime', 
        'tgl_keluar' => 'datetime',
    ];
    
    public function siswa(){ 
        return $this->belongsTo(Siswa::class, 'siswa_id'); 
    }

    public function kamar(){ 
        return $this->belongsTo(Kamar::class, 'kamar_id'); 
    }

    
}
