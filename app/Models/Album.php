<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    //
    use HasFactory;
    protected $guarded = [];

    protected $table = 'albums';

    protected $fillable = [
        'judul',
        'slug',
        'deskripsi',
        'cover',
    ];

    public function gambars()
    {
        return $this->hasMany(Gambar::class);
    }

    protected $appends = ['cover_url'];


    public function getCoverUrlAttribute()
    {
        if (!$this->cover) return null;
        return url('storage/' . ltrim($this->cover, '/'));
    }

    // ✅ Pastikan nilai asli (path) tetap disimpan di database untuk Filament
    public function getCoverAttribute($value)
    {
        return $value; // jangan ubah jadi URL
    }
}
