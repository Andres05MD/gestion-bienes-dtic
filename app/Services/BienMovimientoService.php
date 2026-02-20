<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\Departamento;
use App\Models\Desincorporacion;
use App\Models\TransferenciaInterna;

class BienMovimientoService
{
    /**
     * Actualiza la ubicación y la tabla del bien (DTIC <-> Externo) basado en una transferencia.
     */
    public function actualizarUbicacionBien(TransferenciaInterna $transferencia, ?string $areaId = null): void
    {
        $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;

        $esProcedenciaDtic = $transferencia->procedencia_id == $dticId;
        $esDestinoDtic = $transferencia->destino_id == $dticId;

        // Escenario 1: DTIC a Externo (Movimiento de tabla)
        // Origen: DTIC -> Destino: Externo
        if ($esProcedenciaDtic && !$esDestinoDtic) {
            if ($transferencia->bien_id) {
                $bienOriginal = Bien::find($transferencia->bien_id);

                if ($bienOriginal) {
                    // 1. Crear Bien Externo
                    $bienExterno = BienExterno::create([
                        'equipo' => $bienOriginal->equipo,
                        'marca' => $bienOriginal->marca,
                        'modelo' => $bienOriginal->modelo,
                        'serial' => $bienOriginal->serial,
                        'color' => $bienOriginal->color,
                        'numero_bien' => $bienOriginal->numero_bien,
                        'categoria_bien_id' => $bienOriginal->categoria_bien_id,
                        'estado_id' => $bienOriginal->estado_id,
                        'observaciones' => $bienOriginal->observaciones,
                        'departamento_id' => $transferencia->destino_id,
                        'user_id' => auth()->id(), // O el original si se prefiere conservar
                    ]);

                    // 2. Actualizar Transferencia
                    $transferencia->update([
                        'bien_externo_id' => $bienExterno->id,
                        'bien_id' => null
                    ]);

                    // 3. Eliminar Bien Original
                    $bienOriginal->delete();
                }
            }
            // Si era un bien externo recuperado que se vuelve a enviar fuera, solo actualizamos departamento
            elseif ($transferencia->bien_externo_id) {
                BienExterno::where('id', $transferencia->bien_externo_id)
                    ->update(['departamento_id' => $transferencia->destino_id]);
            }
        }

        // Escenario 2: Externo a DTIC (Movimiento de tabla)
        // Origen: Externo -> Destino: DTIC
        elseif (!$esProcedenciaDtic && $esDestinoDtic) {
            if ($transferencia->bien_externo_id) {
                $bienExternoOriginal = BienExterno::find($transferencia->bien_externo_id);

                if ($bienExternoOriginal) {
                    // 1. Crear Bien Interno (DTIC)
                    $bienInterno = Bien::create([
                        'equipo' => $bienExternoOriginal->equipo,
                        'marca' => $bienExternoOriginal->marca,
                        'modelo' => $bienExternoOriginal->modelo,
                        'serial' => $bienExternoOriginal->serial,
                        'color' => $bienExternoOriginal->color,
                        'numero_bien' => $bienExternoOriginal->numero_bien,
                        'categoria_bien_id' => $bienExternoOriginal->categoria_bien_id,
                        'estado_id' => $bienExternoOriginal->estado_id,
                        'observaciones' => $bienExternoOriginal->observaciones,
                        'area_id' => $areaId, // Área de destino en DTIC
                        'user_id' => auth()->id(),
                    ]);

                    // 2. Actualizar Transferencia
                    $transferencia->update([
                        'bien_id' => $bienInterno->id,
                        'bien_externo_id' => null
                    ]);

                    // 3. Eliminar Bien Externo Original
                    $bienExternoOriginal->delete();
                }
            }
        }

        // Escenario 3: Externo a Externo
        // Solo actualizamos el departamento del bien externo
        elseif (!$esProcedenciaDtic && !$esDestinoDtic) {
            if ($transferencia->bien_externo_id) {
                BienExterno::where('id', $transferencia->bien_externo_id)
                    ->update(['departamento_id' => $transferencia->destino_id]);
            }
        }

        // Escenario 4: DTIC a DTIC (Movimiento interno)
        // Solo actualizamos el área del bien interno
        elseif ($esProcedenciaDtic && $esDestinoDtic) {
            if ($transferencia->bien_id && $areaId) {
                Bien::where('id', $transferencia->bien_id)
                    ->update(['area_id' => $areaId]);
            }
        }
    }

    /**
     * Elimina lógicamente el bien vinculado de la base de datos (Bienes/Bienes Externos).
     */
    public function marcarBienDesincorporado(Desincorporacion $desincorporacion): void
    {
        if ($desincorporacion->bien_id) {
            Bien::where('id', $desincorporacion->bien_id)->delete();
        } elseif ($desincorporacion->bien_externo_id) {
            BienExterno::where('id', $desincorporacion->bien_externo_id)->delete();
        }
    }
}
