<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    //
    public function index()
    {
        // Ambil album urut terbaru + hitung jumlah gambarnya
        $albums = Album::withCount('gambars')
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Transform data agar sesuai persis dengan Frontend TypeScript
        $formattedAlbums = $albums->map(function ($album) {
            return [
                'id'          => $album->id,
                'judul'       => $album->judul,
                'slug'        => $album->slug,
                'cover'       => $album->cover, // Pastikan ini URL lengkap (asset/storage)
                'deskripsi'   => $album->deskripsi,
                
                // MAPPING PENTING:
                // Laravel memberi nama default 'nama_relasi_count'
                // Kita ubah jadi 'total_foto' sesuai frontend
                'total_foto'  => $album->gambars_count, 
                'cover_url'   => $album->cover_url,
                'gambars'     => $album->gambars->map(function($foto) {
                return [
                    'id'  => $foto->id,
                    // Kita generate URL lengkapnya di sini agar Frontend tinggal pakai
                    'url' => asset('storage/' . $foto->path) 
                ];
            }),
                
                'created_at'  => $album->created_at->toIso8601String(),
                'updated_at'  => $album->updated_at->toIso8601String(),
            ];
        });

        return response()->json($formattedAlbums);
    }
}
