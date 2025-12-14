<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiswaResource;
use App\Models\Orangtua;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    public function index(Request $request) {
        $students = Siswa::with('orangtua')              // ⬅️ penting
                    ->where('user_id', $request->user()->id)
                    ->latest()
                    ->paginate(20);

        $siswaIds = $students->pluck('id')->toArray();

        $kamars = DB::table('siswa_kamars')
            ->join('kamars', 'kamars.id', '=', 'siswa_kamars.kamar_id')
            ->whereIn('siswa_kamars.siswa_id', $siswaIds)
            ->select('siswa_kamars.siswa_id', 'kamars.id as kamar_id', 'kamars.nama_kamar')
            ->orderBy('siswa_kamars.id', 'DESC') // ambil yang terbaru
            ->get()
            ->keyBy('siswa_id');

        $students->getCollection()->transform(function ($s) use ($kamars) {
            $kamar = $kamars[$s->id] ?? null;
    
            $s->kamar = $kamar ? [
                'id' => $kamar->kamar_id,
                'nama_kamar' => $kamar->nama_kamar,
            ] : null;
    
            return $s;
        });

        // kembalikan sebagai resource collection (tetap ada meta pagination)
        return SiswaResource::collection($students);
    }

    public function show(Request $request, Siswa $student) {
        $this->authorizeStudent($request, $student);
        $student->load('orangtua');                      // ⬅️ penting
        return new SiswaResource($student);
    }

    public function store(Request $request) {
        $user = $request->user();

        // Terima format nested "student[...]" ATAU flat body (biar fleksibel)
        $payload = $request->has('siswa') ? $request->input('siswa') : $request->all();

        $validated = validator($payload, [
            'nama_lengkap' => 'required|string',
            'nama_panggilan' => 'required|string',
            'nis' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'kewarganegaraan' => 'required|in:WNI,WNA',
            'status_keluarga' => 'required|in:Kandung,Angkat',
            'status_orangtua' => 'nullable|in:Yatim,Piatu,Yatim piatu,Dhuafa',
            'anak_ke' => 'required|string',
            'tgl_masuk' => 'required|string',
            'kelas' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ])->validate();

        // Foto bisa datang sebagai student[foto] atau 'foto'
        $fotoFile = $request->file('siswa.foto') ?: $request->file('foto');
        $path = null;
        if ($fotoFile) {
            $path = $fotoFile->store('siswa', 'public'); // storage/app/public/students
        }

        $student = Siswa::create($validated + [
            'user_id'   => $user->id,
            'foto' => $path,
        ]);

        // BONUS: If request mengirim "orangtua[...]" dan profil belum ada → upsert
        if ($request->has('orangtua')) {
            $orangtua = $request->input('orangtua', []);
            Orangtua::updateOrCreate(
                ['user_id' => $user->id],
                array_intersect_key($orangtua, array_flip([
                    'nama_ayah','pend_ayah','pekerjaan_ayah',
                    'nama_ibu','pend_ibu','pekerjaan_ibu',
                    'nama_wali','pekerjaan_wali','alamat_wali',
                    'alamat','no_hp'
                ])) + ['user_id' => $user->id]
            );
        }

        return response()->json([
            'message' => 'Pendaftaran anak berhasil.',
            'data' => $student
        ], 201);
    }

    private function authorizeStudent(Request $request, Siswa $student) {
        abort_if($student->user_id !== $request->user()->id, 403, 'Unauthorized');
    }
}
