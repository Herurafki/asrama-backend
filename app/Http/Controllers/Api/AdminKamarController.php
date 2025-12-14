<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\KamarSiswa;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminKamarController extends Controller
{
    //

    public function index()
    {
        $kamar = Kamar::withCount(['penghuniAktif as terisi'])->get()
            ->map(function ($k) {
                $k->sisa = max(0, $k->kapasitas - $k->terisi);
                return $k;
            });

        $belumL = Siswa::where('jenis_kelamin','Laki-laki')->whereDoesntHave('kamarAktif')->count();
        $belumP = Siswa::where('jenis_kelamin','Perempuan')->whereDoesntHave('kamarAktif')->count();

        return response()->json([
            'kamar'  => $kamar,
            'belum_memiliki_kamar' => ['Laki-laki' => $belumL, 'Perempuan' => $belumP],
        ]);
    }


    public function move(Request $request)
    {
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'kamar_id' => 'required|exists:kamar,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data, $request) {
            // tutup baris aktif lama
            $aktif = KamarSiswa::where('siswa_id',$data['siswa_id'])
                ->whereNull('tgl_keluar')->lockForUpdate()->first();
            if ($aktif) {
                $aktif->tgl_keluar = now();
                $aktif->save();
            }

            // cek kapasitas kamar baru
            $k = Kamar::where('id',$data['kamar_id'])->lockForUpdate()
                ->withCount(['penghuniAktif as terisi'])->first();

            if ($k->terisi >= $k->kapasitas) {
                abort(422, 'Kamar penuh.');
            }

            // kunci gender netral
            $siswa = Siswa::findOrFail($data['siswa_id']);
            if (is_null($k->jenis_kelamin)) {
                $k->jenis_kelamin = $siswa->jenis_kelamin;
                $k->save();
            }

            KamarSiswa::create([
                'siswa_id'    => $siswa->id,
                'kamar_id'    => $k->id,
                'tgl_masuk'    => now(),
                'keterangan'  => $data['keterangan'] ?? null,
            ]);
        });

        return back()->with('success', 'Siswa berhasil dipindahkan.');
    }


    public function release(Request $request)
    {
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data) {
            $aktif = KamarSiswa::where('siswa_id',$data['siswa_id'])
                ->whereNull('tgl_keluar')->lockForUpdate()->first();

            if (!$aktif) {
                abort(404, 'Siswa tidak memiliki kamar aktif.');
            }

            $aktif->tgl_keluar = now();
            if (!empty($data['keterangan'])) {
                $aktif->keterangan = $data['keterangan'];
            }
            $aktif->save();
        });

        return back()->with('success', 'Siswa dikeluarkan dari kamar.');
    }
}
