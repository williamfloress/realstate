<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunAmcRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() || (auth()->check() && auth()->user()->isAgent());
    }

    public function rules(): array
    {
        $rules = [
            'sector_id' => ['required', 'integer', 'exists:sectores,id'],
            'area_m2' => ['required', 'numeric', 'min:0.01'],
            'habitaciones' => ['required', 'integer', 'min:0'],
            'banos' => ['required', 'integer', 'min:0'],
            'parqueos' => ['required', 'integer', 'min:0'],
            'anio_construccion' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'finish_piso_id' => ['nullable', 'integer', 'exists:acabados,id'],
            'finish_cocina_id' => ['nullable', 'integer', 'exists:acabados,id'],
            'finish_bano_id' => ['nullable', 'integer', 'exists:acabados,id'],
        ];

        $piso = $this->input('finish_piso_id');
        $cocina = $this->input('finish_cocina_id');
        $bano = $this->input('finish_bano_id');

        if ($piso || $cocina || $bano) {
            $rules['finish_piso_id'] = ['required', 'integer', 'exists:acabados,id'];
            $rules['finish_cocina_id'] = ['required', 'integer', 'exists:acabados,id'];
            $rules['finish_bano_id'] = ['required', 'integer', 'exists:acabados,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'sector_id.required' => 'El sector es obligatorio.',
            'sector_id.exists' => 'El sector seleccionado no existe.',
            'area_m2.required' => 'El área en m² es obligatoria.',
            'area_m2.min' => 'El área debe ser al menos 0.01 m².',
        ];
    }
}
