<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NekretninaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cenaSaPopustom = $this->popust > 0
            ? round($this->cena * (1 - ($this->popust / 100)), 2)
            : $this->cena;

        return [
            'id' => $this->id,
            'naslov' => $this->naslov,
            'opis' => $this->opis,
            'osnovna_cena' => $this->cena,
            'popust_procenat' => $this->popust . '%',
            'cena_sa_popustom' => $cenaSaPopustom,
            'kvadratura' => $this->kvadratura,
            'cena_po_m2' => round($cenaSaPopustom / $this->kvadratura, 2),
            'adresa' => $this->adresa,
            'tip' => $this->tip,
            'status' => $this->status,
            'istaknuto' => (bool) $this->is_istaknuto,
            'grad' => new GradResource($this->whenLoaded('grad')),
        ];
    }
}
