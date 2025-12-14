<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gambar extends Model
{
    //
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'gambars';

    protected $fillable = [
        'nama',
        'path',
        'album_id',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
