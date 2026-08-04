<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KreditnaKalkulacijaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'iznos_kredita' => $this->iznos_kredita,
            'ucesce' => $this->ucesce,
            'neto_iznos_kredita' => $this->iznos_kredita - $this->ucesce,
            'godisnja_kamata' => $this->godisnja_kamata . '%',
            'period_otplate_kredita' => $this->period_otplate_kredita,
            'mesecna_rata' => $this->mesecna_rata,
            'nekretnina' => new NekretninaResource($this->whenLoaded('nekretnina')),
            'datum_kalkulacije' => $this->created_at->format('d.m.Y. H:i'),
        ];
    }
}