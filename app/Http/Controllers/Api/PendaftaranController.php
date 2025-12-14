<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orangtua;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PendaftaranController extends Controller
{
    //
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'siswa.nama_lengkap' => 'required|string',
            'siswa.nama_panggilan' => 'required|string',
            'siswa.nis' => 'required|string',
            'siswa.tempat_lahir' => 'required|string',
            'siswa.tanggal_lahir' => 'required|string',
            'siswa.jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'siswa.kewarganegaraan' => 'required|in:WNI,WNA',
            'siswa.status_keluarga' => 'required|in:Kandung,Angkat',
            'siswa.status_orangtua' => 'required|in:Yatim,Piatu,Yatim piatu,Dhuafa',
            'siswa.anak_ke' => 'required|string',
            'siswa.tgl_masuk' => 'required|string',
            'siswa.kelas' => 'required|string',
            'siswa.foto' => 'nullable|image|max:2048',
            

            'orangtua.nama_ayah' => 'nullable|string',
            'orangtua.pend_ayah' => 'nullable|string',
            'orangtua.pekerjaan_ayah' => 'nullable|string',
            'orangtua.nama_ibu' => 'nullable|string',
            'orangtua.pend_ibu' => 'nullable|string',
            'orangtua.pekerjaan_ibu' => 'nullable|string',
            'orangtua.nama_wali' => 'nullable|string',
            'orangtua.pekerjaan_wali' => 'nullable|string',
            'orangtua.alamat_wali' => 'nullable|string',
            'orangtua.alamat' => 'nullable|string',
            'orangtua.no_hp' => 'nullable|string',
        ]);

        $fotoPath = null;
        if ($request->hasFile('siswa.foto')) {
            $fotoPath = $request->file('siswa.foto')->store('siswa', 'public');
        }
 
         DB::beginTransaction();
         try {
             // ambil data nested
             $orangtuaData = $validated['orangtua'] ?? [];
             $siswaData    = $validated['siswa'] ?? [];
 
             // simpan orangtua
             $orangtua = Orangtua::create(array_merge($orangtuaData, [
                 'user_id' => $user->id,
             ]));
 
             // simpan siswa
             $siswa = Siswa::create(array_merge($siswaData, [
                 'user_id'  => $user->id,
                 'kamar_id' => null,
                 'foto' => $fotoPath, // simpan path foto
             ]));
 
             DB::commit();
 
             return response()->json([
                 'message'  => 'Pendaftaran berhasil',
                 'orangtua' => $orangtua,
                 'siswa'    => $siswa,
                 'foto_url' => $fotoPath ? asset("storage/$fotoPath") : null
             ], 201);
 
         } catch (\Exception $e) {
             DB::rollBack();
             Log::error('Pendaftaran gagal: '.$e->getMessage());
 
             return response()->json([
                 'message' => 'Gagal menyimpan data',
                 'error'   => $e->getMessage(),
             ], 500);
         }

        // // simpan parent (jika belum ada)
        // $orangtua = $user->orangtua;
        // if (!$orangtua) {
        //     $orangtua = Orangtua::create(array_merge(
        //         $validated['orangtua'],
        //         ['user_id' => $user->id]
        //     ));
        // }

        // // simpan student
        // $siswa = Siswa::create(array_merge(
        //     $validated['siswa'],
        //     ['user_id' => $user->id, 'foto' => $fotoPath]
        // ));

        // return response()->json([
        //     'message' => 'Pendaftaran berhasil',
        //     'orangtua' => $orangtua,
        //     'siswa' => $siswa
        // ], 201);
    }
}
