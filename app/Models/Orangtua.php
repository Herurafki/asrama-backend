<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orangtua extends Model
{
    use HasFactory;
    
    protected $table = 'orangtuas';
    protected $fillable = [
        'nama_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'alamat',
        'nama_wali',
        'pekerjaan_wali',
        'alamat_wali',
        'no_hp',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
