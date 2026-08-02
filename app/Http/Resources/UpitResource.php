<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'poruka' => $this->poruka,
            'status_upita' => $this->status_upita,
            'datum_slanja' => $this->created_at->format('d.m.Y. H:i'),
            'nekretnina' => new NekretninaResource($this->whenLoaded('nekretnina')),
            'korisnik' => [
                'id' => $this->korisnik->id ?? null,
                'ime' => $this->korisnik->name ?? null,
                'email' => $this->korisnik->email ?? null,
            ],
        ];
    }
}
