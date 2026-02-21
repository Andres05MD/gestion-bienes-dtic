<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDesincorporacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crear desincorporaciones');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'procedencia_id' => ['required', 'exists:departamentos,id'],
            'destino_id' => ['required', 'exists:departamentos,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'area_procedencia_id' => ['nullable', 'exists:areas,id'],
            'fecha' => ['required', 'date'],
            'numero_informe' => ['nullable', 'array'],
            'numero_informe.*' => ['nullable', 'string', 'max:255'],
            'estatus_acta_id' => ['required', 'exists:estatus_actas,id'],
            'observaciones' => ['nullable', 'string'],

            // Validación para múltiples bienes
            'bienes'               => ['required', 'array', 'min:1'],
            'bienes.*.id'          => ['nullable'],
            'bienes.*.tipo'        => ['required', 'in:dtic,externo'],
            'bienes.*.numero_bien' => ['required', 'string', 'max:255'],
            'bienes.*.descripcion' => ['required', 'string', 'max:255'],
            'bienes.*.serial'      => ['nullable', 'string', 'max:255'],
        ];
    }



    public function messages(): array
    {
        return [
            'bienes.required' => 'Debe agregar al menos un bien a la lista de desincorporación.',
            'bienes.min' => 'Debe agregar al menos un bien.',
            'bienes.*.numero_bien.required' => 'El número de bien es obligatorio.',
            'bienes.*.descripcion.required' => 'La descripción es obligatoria.',
            'procedencia_id.required' => 'La procedencia es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
            'numero_informe.regex' => 'El formato del número de informe debe ser 00-00-00.',
            'estatus_acta_id.required' => 'El estatus del acta es obligatorio.',
            'estatus_acta_id.exists' => 'El estatus seleccionado no es válido.',
        ];
    }
}
