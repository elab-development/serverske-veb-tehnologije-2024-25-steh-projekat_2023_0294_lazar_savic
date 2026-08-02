<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNekretninaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'naslov' => 'required|string|max:255',
            'opis' => 'required|string',
            'cena' => 'required|numeric|min:0',
            'popust' => 'nullable|numeric|min:0|max:100',
            'kvadratura' => 'required|numeric|min:1',
            'adresa' => 'required|string|max:255',
            'tip' => 'required|in:stan,kuca,poslovni_prostor,zemljiste',
            'status' => 'required|in:prodaja,izdavanje',
            'is_istaknuto' => 'boolean',
            'grad_id' => 'required|exists:gradovi,id',
            'slika' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }
}