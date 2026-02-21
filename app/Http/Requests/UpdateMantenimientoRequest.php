<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMantenimientoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('editar transferencias');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'numero_bien' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'serial'      => ['nullable', 'string', 'max:255'],
            'procedencia_id' => ['required', 'exists:departamentos,id'],
            'destino_id'     => ['required', 'exists:departamentos,id'],
            'area_id'        => ['nullable', 'exists:areas,id'],
            'area_procedencia_id' => ['nullable', 'exists:areas,id'],
            'fecha'          => ['required', 'date'],
            'estatus_acta_id' => ['required', 'exists:estatus_actas,id'],
            'fecha_firma'    => ['nullable', 'date'],
            'n_orden_acta'   => ['nullable', 'string', 'digits:4'],
            'fecha_acta'     => ['nullable', 'date'],
        ];
    }

    /**
     * Mensajes de error en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'numero_bien.required' => 'El número de bien es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'procedencia_id.required' => 'La procedencia es obligatoria.',
            'procedencia_id.exists'   => 'La procedencia seleccionada no es válida.',
            'destino_id.required'     => 'El destino es obligatorio.',
            'destino_id.exists'       => 'El destino seleccionado no es válido.',
            'fecha.required'          => 'La fecha es obligatoria.',
            'estatus_acta_id.required' => 'El estatus del acta es obligatorio.',
            'estatus_acta_id.exists'  => 'El estatus seleccionado no es válido.',
        ];
    }
}
