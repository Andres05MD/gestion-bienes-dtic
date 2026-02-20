<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferenciaInternaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return $this->user()->can('crear transferencias');
    }

    /**
     * Reglas de validación para crear una transferencia interna.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'procedencia_id' => ['required', 'exists:departamentos,id'],
            'destino_id'     => ['required', 'exists:departamentos,id'],
            'area_id'        => ['nullable', 'exists:areas,id'],
            'fecha'          => ['required', 'date'],
            'estatus_acta_id' => ['required', 'exists:estatus_actas,id'],
            'fecha_firma'    => ['nullable', 'date'],

            // Validación para múltiples bienes
            'bienes'               => ['required', 'array', 'min:1'],
            'bienes.*.id'          => ['required'],
            'bienes.*.tipo'        => ['required', 'in:dtic,externo'],
            'bienes.*.numero_bien' => ['required', 'string', 'max:255'],
            'bienes.*.descripcion' => ['required', 'string', 'max:255'],
            'bienes.*.serial'      => ['nullable', 'string', 'max:255'],
        ];
    }



    /**
     * Mensajes de error personalizados en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bienes.required'            => 'Debe agregar al menos un bien a la lista de transferencia.',
            'bienes.min'                 => 'Debe agregar al menos un bien a la lista de transferencia.',
            'bienes.*.numero_bien.required' => 'El número de bien es obligatorio.',
            'bienes.*.descripcion.required' => 'La descripción es obligatoria.',
            'procedencia_id.required'    => 'La procedencia es obligatoria.',
            'procedencia_id.exists'      => 'La procedencia seleccionada no es válida.',
            'destino_id.required'        => 'El destino es obligatorio.',
            'destino_id.exists'          => 'El destino seleccionado no es válido.',
            'fecha.required'             => 'La fecha es obligatoria.',
            'estatus_acta_id.required'   => 'El estatus del acta es obligatorio.',
            'estatus_acta_id.exists'     => 'El estatus seleccionado no es válido.',
        ];
    }

    /**
     * Acciones posteriores a las reglas básicas. (Prevención de IDOR)
     */
    public function after(): array
    {
        return [
            function (\Illuminate\Validation\Validator $validator) {
                $bienes = $this->input('bienes', []);
                $procedenciaId = $this->input('procedencia_id');

                // Buscar el ID del DTIC para comparar
                $dticId = \App\Models\Departamento::where('nombre', 'DTIC')->first()?->id;

                foreach ($bienes as $key => $bienData) {
                    if (!isset($bienData['tipo']) || !isset($bienData['id'])) {
                        continue;
                    }

                    if ($bienData['tipo'] === 'dtic') {
                        $bienExiste = \App\Models\Bien::where('id', $bienData['id'])->exists();
                        if (!$bienExiste) {
                            $validator->errors()->add("bienes.{$key}.id", "El bien interno (DTIC) no existe.");
                        } elseif ($procedenciaId != $dticId) {
                            $validator->errors()->add("bienes.{$key}.id", "Intento de transferir un bien interno desde una procedencia que no es DTIC.");
                        }
                    } elseif ($bienData['tipo'] === 'externo') {
                        $bienExterno = \App\Models\BienExterno::find($bienData['id']);
                        if (!$bienExterno) {
                            $validator->errors()->add("bienes.{$key}.id", "El bien externo no existe.");
                        } elseif ($bienExterno->departamento_id != $procedenciaId) {
                            $validator->errors()->add("bienes.{$key}.id", "El bien externo no pertenece al departamento de procedencia seleccionado.");
                        }
                    }
                }
            }
        ];
    }
}
