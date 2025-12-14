<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiswaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parent = $this->orangtua; // sudah diload dari controller

        return [
            'id'              => (int) $this->id,
            'nama_lengkap'    => $this->nama_lengkap ?? '',
            'nama_panggilan'  => $this->nama_panggilan ?? '',
            'nis'             => $this->nis ?? '',
            'tempat_lahir'    => $this->tempat_lahir ?? '',
            'tanggal_lahir'   => (string) ($this->tanggal_lahir ?? ''),
            'kelas'           => $this->kelas ?? '',
            'kamar'           => $this->kamar ?? null,
            'jenis_kelamin'   => $this->jenis_kelamin ?? '',
            'status_keluarga' => $this->status_keluarga ?? '',
            'kewarganegaraan' => $this->kewarganegaraan ?? '',
            'tgl_masuk'       => (string) ($this->tgl_masuk ?? ''),
            'foto_url'        => $this->foto_url,

            // ⬇️ blok orang tua (aman walau null)
            'orang_tua' => [
                'nama_ayah' => optional($parent)->nama_ayah ?? '',
                'nama_ibu'  => optional($parent)->nama_ibu ?? '',
                'no_hp'     => optional($parent)->no_hp ?? '',
                'alamat'    => optional($parent)->alamat ?? '',
            ],
        ];
    }
}
