<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class PerizinanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private function toFront(?string $s): string {
        return match($s) {
            'Diterima' => 'disetujui',
            'Ditolak'  => 'ditolak',
            default    => 'pending',
        };
    }

    public function toArray($request): array
    {
        $s = $this->siswa;

        return [
            'id'         => (int) $this->id,
            'siswa_id'   => (int) $this->siswa_id,
            'siswa' => [
                'nama_lengkap'    => $s->nama_lengkap ?? '',
                'no_induk_santri' => $s->nis ?? '',
                'kelas_masuk'     => $s->kelas ?? '',
                'foto'            => $s->foto_url ?? null,
            ],
            'alasan'         => $this->alasan ?? '',
            'tanggal_keluar' => $this->tanggal_keluar ?? '',
            'tanggal_masuk'  => $this->tanggal_masuk ?? '',
            'jam_keluar'     => $this->waktu_keluar ?? '',
            'jam_datang'     => $this->waktu_masuk ?? '',
            'status'         => $this->toFront($this->status),
            'keterangan'     => $this->keterangan ?? '',
            'dibuat_oleh'    => (int) $this->user_id,
            'created_at'     => optional($this->created_at)->toISOString(),
            'updated_at'     => optional($this->updated_at)->toISOString(),
        ];
    }
}
