<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\KamarSiswa;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class AssignRoomsService
{
    /**
     * Create a new class instance.
     */
    public function lockAndDistribute(): void
    {
        DB::transaction(function ()  {
            // 1. Ambil siswa tanpa kamar aktif, dipisah gender
            $siswaL = Siswa::where('jenis_kelamin','Laki-laki')->whereDoesntHave('kamarAktif')->get();
            $siswaP = Siswa::where('jenis_kelamin','Perempuan')->whereDoesntHave('kamarAktif')->get();

            // 2. Hitung sisa slot tiap kamar
            $all = Kamar::withCount(['penghuniAktif as terisi'])->lockForUpdate()->get();
            $all->each(fn($k) => $k->sisa = max(0, $k->kapasitas - $k->terisi));

            $bucketL = $all->where('jenis_kelamin','Laki-laki')->values();
            $bucketP = $all->where('jenis_kelamin','Perempuan')->values();
            $bucketN = $all->whereNull('jenis_kelamin')->values();

            $slotN = $bucketN->sum('sisa');
            $needL = $siswaL->count();
            $needP = $siswaP->count();

            // 3. Bagi slot netral secara proporsional ke L dan P
            $allocN_L = $allocN_P = 0;
            if ($slotN > 0 && ($needL + $needP) > 0) {
                $allocN_L = (int) floor($slotN * ($needL / ($needL + $needP)));
                $allocN_P = $slotN - $allocN_L;
            }

            // 4. Buat daftar slot kamar (id kamar diulang sesuai sisa)
            $slotsL = collect();
            foreach ($bucketL as $k) for ($i=0;$i<$k->sisa;$i++) $slotsL->push($k->id);

            $slotsP = collect();
            foreach ($bucketP as $k) for ($i=0;$i<$k->sisa;$i++) $slotsP->push($k->id);

            foreach ($bucketN as $k) {
                while ($k->sisa > 0 && $allocN_L-- > 0) { $slotsL->push($k->id); $k->sisa--; }
                while ($k->sisa > 0 && $allocN_P-- > 0) { $slotsP->push($k->id); $k->sisa--; }
            }

            // 5. Fungsi untuk assign round-robin
            $assign = function($list, $slots)  {
                $slots = $slots->shuffle()->values(); $si=0; $max=$slots->count();

                foreach ($list as $s) {
                    if ($max===0) break;
                    $loopGuard = 0;

                    while ($loopGuard++ < $max) {
                        $kamarId = $slots[$si];

                        $k = Kamar::where('id',$kamarId)->lockForUpdate()
                            ->withCount(['penghuniAktif as terisi'])->first();

                        if ($k->terisi < $k->kapasitas) {
                            // set gender jika kamar netral
                            if (is_null($k->jenis_kelamin)) {
                                $k->jenis_kelamin = $s->jenis_kelamin;
                                $k->save();
                            }

                            // buat baris pivot baru
                            KamarSiswa::create([
                                'siswa_id'    => $s->id,
                                'kamar_id'    => $k->id,
                                'tgl_masuk'    => now(),
                                
                            ]);
                            break;
                        }

                        $si = ($si + 1) % $max;
                    }

                    $si = ($si + 1) % max(1,$max);
                }
            };

            $assign($siswaL, $slotsL);
            $assign($siswaP, $slotsP);

            // 6. Validasi akhir
            $remain = Siswa::whereDoesntHave('kamarAktif')->count();
            if ($remain > 0) {
                throw new \RuntimeException("Slot kamar tidak cukup. Sisa tanpa kamar: {$remain}");
            }
        });
    }
}
