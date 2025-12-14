<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    //
    use HasFactory;

    protected $table = 'donasi';

    protected $fillable = [
        'nama',
        'alamat',
        'no_hp',
        'tgl_kirim',
        'jumlah',
        'bukti_tf',
    ];

     
}
