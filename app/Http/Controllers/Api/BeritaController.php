<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    //

    public function index()
    {
        $berita = Berita::with('author:id,name')
            ->where('status', 'publish')
            ->orderByDesc('published_at')
            ->get();

        return response()->json($berita);
    }

    public function show($slug)
    {
        $berita = Berita::with('author')->where('slug', $slug)->firstOrFail();
        return response()->json($berita);
    }
}
