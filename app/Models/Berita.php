<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Berita extends Model
{
    //
    use HasFactory, HasSlug;

    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'isi',
        'cover',
        'published_at',
        'status',
        'author_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('judul')
            ->saveSlugsTo('slug');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
        
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
