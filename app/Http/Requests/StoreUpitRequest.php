<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'poruka' => 'required|string|min:10',
            'nekretnina_id' => 'required|exists:nekretnine,id',
        ];
    }
}