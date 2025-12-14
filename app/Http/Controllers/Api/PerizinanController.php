<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerizinanResource;
use App\Models\Perizinan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PerizinanController extends Controller
{
    // list semua perizinan untuk siswa milik user login
    public function index(Request $request)
    {
        $uid = $request->user()->id;

        $q = Perizinan::with('siswa')
            ->whereHas('siswa', fn($x) => $x->where('user_id', $uid))
            ->latest()
            ->paginate(20);

        return PerizinanResource::collection($q);
    }

    // detail (hanya milik user)
    public function show(Request $request, Perizinan $permit)
    {
        $this->authorizePermit($request, $permit);
        $permit->load('siswa');
        return new PerizinanResource($permit);
    }

    // buat izin oleh user (status default Menunggu)
    public function store(Request $request)
    {
        $uid = $request->user()->id;

        $data = $request->validate([
            'siswa_id'       => 'required|exists:siswas,id',
            'alasan'         => 'required|string',
            'tanggal_keluar' => 'required|string',
            'jam_keluar'     => 'required|date_format:H:i',
        ]);

        // pastikan siswa milik user
        $siswa = Siswa::where('id', $data['siswa_id'])
            ->where('user_id', $uid)->firstOrFail();

        

        $permit = Perizinan::create([
            'siswa_id'       => $siswa->id,
            'user_id'       => $uid,
            'alasan'         => $data['alasan'],
            'tanggal_keluar' => $data['tanggal_keluar'],
            'waktu_keluar'   => $data['jam_keluar'],
            'status'         => 'Menunggu',
        ]);

        $permit->load('siswa');

        return (new PerizinanResource($permit))
            ->additional(['message' => 'Permohonan izin dibuat'])
            ->response()
            ->setStatusCode(201);
    }

    private function authorizePermit(Request $request, Perizinan $permit)
    {
        abort_unless(
            $permit->siswa()->where('user_id', $request->user()->id)->exists(),
            403, 'Unauthorized'
        );
    }
}
