<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul_laporan' => $this->judul_laporan,
            'lokasi_fasilitas' => $this->lokasi_fasilitas,
            'deskripsi_kerusakan' => $this->deskripsi_kerusakan,
            'foto_bukti' => $this->foto_bukti ? asset('storage/' . $this->foto_bukti) : null,
            'status' => $this->status,
            'user' => [
                'id' => $this->user->id,
                'nim' => $this->user->nim,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
