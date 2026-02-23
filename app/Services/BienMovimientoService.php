<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\Departamento;
use App\Models\Desincorporacion;
use App\Models\DistribucionDireccion;
use App\Models\Mantenimiento;
use App\Models\MovimientoBien;
use App\Models\TransferenciaInterna;
use Illuminate\Support\Facades\Log;

class BienMovimientoService
{
    /**
     * Registra un movimiento en el historial centralizado.
     */
    public function registrarMovimiento(
        string $bienType,
        int|string $bienId,
        string $numeroBien,
        string $tipoMovimiento,
        ?string $operacionType = null,
        int|string|null $operacionId = null,
        int|string|null $departamentoOrigenId = null,
        int|string|null $departamentoDestinoId = null,
        int|string|null $areaOrigenId = null,
        int|string|null $areaDestinoId = null,
        ?string $descripcion = null,
        ?string $fecha = null,
    ): MovimientoBien {
        $bienIdInt = (int) $bienId;

        // Auto-resolver bien_id si es inválido, buscando por numero_bien
        if ($bienIdInt <= 0) {
            Log::info("Auto-resolviendo bien_id para numero_bien: {$numeroBien} (tipo indicado: {$bienType})");

            $bien = Bien::where('numero_bien', $numeroBien)->first();
            if ($bien) {
                $bienType = Bien::class;
                $bienIdInt = $bien->id;
            } else {
                $bienExterno = BienExterno::where('numero_bien', $numeroBien)->first();
                if ($bienExterno) {
                    $bienType = BienExterno::class;
                    $bienIdInt = $bienExterno->id;
                } else {
                    Log::warning("No se pudo resolver bien_id para numero_bien: {$numeroBien}. No existe en ninguna tabla.");
                }
            }
        }

        return MovimientoBien::create([
            'bien_type' => $bienType,
            'bien_id' => $bienIdInt,
            'numero_bien' => $numeroBien,
            'tipo_movimiento' => $tipoMovimiento,
            'operacion_type' => $operacionType,
            'operacion_id' => $operacionId ? (int) $operacionId : null,
            'departamento_origen_id' => $departamentoOrigenId ? (int) $departamentoOrigenId : null,
            'departamento_destino_id' => $departamentoDestinoId ? (int) $departamentoDestinoId : null,
            'area_origen_id' => $areaOrigenId ? (int) $areaOrigenId : null,
            'area_destino_id' => $areaDestinoId ? (int) $areaDestinoId : null,
            'descripcion' => $descripcion,
            'fecha' => $fecha ?? now()->toDateString(),
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Actualiza la ubicación y la tabla del bien (DTIC <-> Externo) basado en una transferencia.
     * También registra el movimiento en el historial.
     */
    public function actualizarUbicacionBien(TransferenciaInterna|Mantenimiento $transferencia, int|string|null $areaId = null): void
    {
        $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;

        $esProcedenciaDtic = $transferencia->procedencia_id == $dticId;
        $esDestinoDtic = $transferencia->destino_id == $dticId;

        $tipoMovimiento = $transferencia instanceof TransferenciaInterna
            ? MovimientoBien::TIPO_TRANSFERENCIA
            : MovimientoBien::TIPO_MANTENIMIENTO;

        // Escenario 1: DTIC a Externo (Movimiento de tabla)
        // Origen: DTIC -> Destino: Externo
        if ($esProcedenciaDtic && !$esDestinoDtic) {
            if ($transferencia->bien_id) {
                $bienOriginal = Bien::find($transferencia->bien_id);

                if ($bienOriginal) {
                    // 1. Capturar datos antes de eliminar
                    $datosOriginal = $bienOriginal->toArray();

                    // 2. Eliminar Bien Original primero (libera numero_bien único)
                    $bienOriginal->delete();

                    // 3. Crear Bien Externo con trazabilidad de origen
                    $bienExterno = BienExterno::create([
                        'equipo' => $datosOriginal['equipo'],
                        'marca' => $datosOriginal['marca'],
                        'modelo' => $datosOriginal['modelo'],
                        'serial' => $datosOriginal['serial'],
                        'color' => $datosOriginal['color'],
                        'numero_bien' => $datosOriginal['numero_bien'],
                        'categoria_bien_id' => $datosOriginal['categoria_bien_id'],
                        'estado_id' => $datosOriginal['estado_id'],
                        'observaciones' => $datosOriginal['observaciones'],
                        'departamento_id' => $transferencia->destino_id,
                        'departamento_origen_id' => $dticId, // Trazabilidad DTIC
                        'user_id' => auth()->id(),
                    ]);

                    // 4. Registrar movimiento en el historial
                    $this->registrarMovimiento(
                        bienType: BienExterno::class,
                        bienId: $bienExterno->id,
                        numeroBien: $datosOriginal['numero_bien'],
                        tipoMovimiento: $tipoMovimiento,
                        operacionType: get_class($transferencia),
                        operacionId: $transferencia->id,
                        departamentoOrigenId: $dticId,
                        departamentoDestinoId: $transferencia->destino_id,
                        areaOrigenId: $datosOriginal['area_id'],
                        descripcion: "Transferido de DTIC a " . ($transferencia->destino?->nombre ?? 'departamento externo'),
                        fecha: $transferencia->fecha?->toDateString(),
                    );

                    // 5. Actualizar Transferencia
                    $transferencia->update([
                        'bien_externo_id' => $bienExterno->id,
                        'bien_id' => null
                    ]);
                }
            }
            // Si era un bien externo recuperado que se vuelve a enviar fuera, solo actualizamos departamento
            elseif ($transferencia->bien_externo_id) {
                $bienExterno = BienExterno::find($transferencia->bien_externo_id);
                $depOrigenId = $bienExterno?->departamento_id;

                BienExterno::where('id', $transferencia->bien_externo_id)
                    ->update(['departamento_id' => $transferencia->destino_id]);

                // Registrar movimiento
                if ($bienExterno) {
                    $this->registrarMovimiento(
                        bienType: BienExterno::class,
                        bienId: $bienExterno->id,
                        numeroBien: $bienExterno->numero_bien,
                        tipoMovimiento: $tipoMovimiento,
                        operacionType: get_class($transferencia),
                        operacionId: $transferencia->id,
                        departamentoOrigenId: $depOrigenId,
                        departamentoDestinoId: $transferencia->destino_id,
                        descripcion: "Transferido de DTIC a " . ($transferencia->destino?->nombre ?? 'departamento externo'),
                        fecha: $transferencia->fecha?->toDateString(),
                    );
                }
            }
        }

        // Escenario 2: Externo a DTIC (Movimiento de tabla)
        // Origen: Externo -> Destino: DTIC
        elseif (!$esProcedenciaDtic && $esDestinoDtic) {
            if ($transferencia->bien_externo_id) {
                $bienExternoOriginal = BienExterno::find($transferencia->bien_externo_id);

                if ($bienExternoOriginal) {
                    // 1. Capturar datos antes de eliminar
                    $datosOriginal = $bienExternoOriginal->toArray();

                    // 2. Eliminar Bien Externo Original primero (libera numero_bien único)
                    $bienExternoOriginal->delete();

                    // 3. Crear Bien Interno (DTIC)
                    $bienInterno = Bien::create([
                        'equipo' => $datosOriginal['equipo'],
                        'marca' => $datosOriginal['marca'],
                        'modelo' => $datosOriginal['modelo'],
                        'serial' => $datosOriginal['serial'],
                        'color' => $datosOriginal['color'],
                        'numero_bien' => $datosOriginal['numero_bien'],
                        'categoria_bien_id' => $datosOriginal['categoria_bien_id'],
                        'estado_id' => $datosOriginal['estado_id'],
                        'observaciones' => $datosOriginal['observaciones'],
                        'area_id' => $areaId,
                        'user_id' => auth()->id(),
                    ]);

                    // 4. Registrar movimiento
                    $this->registrarMovimiento(
                        bienType: Bien::class,
                        bienId: $bienInterno->id,
                        numeroBien: $datosOriginal['numero_bien'],
                        tipoMovimiento: $tipoMovimiento,
                        operacionType: get_class($transferencia),
                        operacionId: $transferencia->id,
                        departamentoOrigenId: $transferencia->procedencia_id,
                        departamentoDestinoId: $dticId,
                        areaDestinoId: $areaId ? (int) $areaId : null,
                        descripcion: "Transferido de " . ($transferencia->procedencia?->nombre ?? 'departamento externo') . " a DTIC",
                        fecha: $transferencia->fecha?->toDateString(),
                    );

                    // 5. Actualizar Transferencia
                    $transferencia->update([
                        'bien_id' => $bienInterno->id,
                        'bien_externo_id' => null
                    ]);
                }
            }
        }

        // Escenario 3: Externo a Externo
        // Solo actualizamos el departamento del bien externo
        elseif (!$esProcedenciaDtic && !$esDestinoDtic) {
            if ($transferencia->bien_externo_id) {
                $bienExterno = BienExterno::find($transferencia->bien_externo_id);
                $depOrigenId = $bienExterno?->departamento_id;

                BienExterno::where('id', $transferencia->bien_externo_id)
                    ->update(['departamento_id' => $transferencia->destino_id]);

                // Registrar movimiento
                if ($bienExterno) {
                    $this->registrarMovimiento(
                        bienType: BienExterno::class,
                        bienId: $bienExterno->id,
                        numeroBien: $bienExterno->numero_bien,
                        tipoMovimiento: $tipoMovimiento,
                        operacionType: get_class($transferencia),
                        operacionId: $transferencia->id,
                        departamentoOrigenId: $depOrigenId,
                        departamentoDestinoId: $transferencia->destino_id,
                        descripcion: "Transferido de " . ($transferencia->procedencia?->nombre ?? '—') . " a " . ($transferencia->destino?->nombre ?? '—'),
                        fecha: $transferencia->fecha?->toDateString(),
                    );
                }
            }
        }

        // Escenario 4: DTIC a DTIC (Movimiento interno)
        // Solo actualizamos el área del bien interno
        elseif ($esProcedenciaDtic && $esDestinoDtic) {
            if ($transferencia->bien_id && $areaId) {
                $bien = Bien::find($transferencia->bien_id);
                $areaOrigenId = $bien?->area_id;

                Bien::where('id', $transferencia->bien_id)
                    ->update(['area_id' => $areaId]);

                // Registrar movimiento
                if ($bien) {
                    $this->registrarMovimiento(
                        bienType: Bien::class,
                        bienId: $bien->id,
                        numeroBien: $bien->numero_bien,
                        tipoMovimiento: $tipoMovimiento,
                        operacionType: get_class($transferencia),
                        operacionId: $transferencia->id,
                        departamentoOrigenId: $dticId,
                        departamentoDestinoId: $dticId,
                        areaOrigenId: $areaOrigenId,
                        areaDestinoId: (int) $areaId,
                        descripcion: "Movimiento interno en DTIC",
                        fecha: $transferencia->fecha?->toDateString(),
                    );
                }
            }
        }
    }

    /**
     * Registra el movimiento de desincorporación, actualiza el estatus a "Desincorporado" y elimina lógicamente el bien.
     */
    public function marcarBienDesincorporado(Desincorporacion $desincorporacion): void
    {
        $estadoDesincorporadoId = \App\Models\Estado::where('nombre', 'Desincorporado')->first()?->id;

        // Registrar movimiento antes de eliminar
        if ($desincorporacion->bien_id) {
            $bien = Bien::find($desincorporacion->bien_id);
            if ($bien) {
                $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;
                $this->registrarMovimiento(
                    bienType: Bien::class,
                    bienId: $bien->id,
                    numeroBien: $bien->numero_bien,
                    tipoMovimiento: MovimientoBien::TIPO_DESINCORPORACION,
                    operacionType: Desincorporacion::class,
                    operacionId: $desincorporacion->id,
                    departamentoOrigenId: $dticId,
                    areaOrigenId: $bien->area_id,
                    descripcion: "Bien desincorporado: " . ($desincorporacion->observaciones ?? ''),
                    fecha: $desincorporacion->fecha?->toDateString(),
                );

                if ($estadoDesincorporadoId) {
                    $bien->update(['estado_id' => $estadoDesincorporadoId]);
                }
            }
        } elseif ($desincorporacion->bien_externo_id) {
            $bienExterno = BienExterno::find($desincorporacion->bien_externo_id);
            if ($bienExterno) {
                $this->registrarMovimiento(
                    bienType: BienExterno::class,
                    bienId: $bienExterno->id,
                    numeroBien: $bienExterno->numero_bien,
                    tipoMovimiento: MovimientoBien::TIPO_DESINCORPORACION,
                    operacionType: Desincorporacion::class,
                    operacionId: $desincorporacion->id,
                    departamentoOrigenId: $bienExterno->departamento_id,
                    descripcion: "Bien desincorporado: " . ($desincorporacion->observaciones ?? ''),
                    fecha: $desincorporacion->fecha?->toDateString(),
                );

                if ($estadoDesincorporadoId) {
                    $bienExterno->update(['estado_id' => $estadoDesincorporadoId]);
                }
            }
        }
    }

    /**
     * Registra el movimiento de una distribución.
     */
    public function registrarDistribucion(DistribucionDireccion $distribucion): void
    {
        $bienType = $distribucion->bien_id ? Bien::class : BienExterno::class;
        $bienId = $distribucion->bien_id ?? $distribucion->bien_externo_id;

        if (!$bienId) {
            return;
        }

        $bien = $bienType === Bien::class ? Bien::find($bienId) : BienExterno::find($bienId);
        if (!$bien) {
            return;
        }

        $this->registrarMovimiento(
            bienType: $bienType,
            bienId: $bienId,
            numeroBien: $bien->numero_bien,
            tipoMovimiento: MovimientoBien::TIPO_DISTRIBUCION,
            operacionType: DistribucionDireccion::class,
            operacionId: $distribucion->id,
            departamentoOrigenId: $distribucion->procedencia_id,
            descripcion: "Distribución desde dirección",
            fecha: $distribucion->fecha?->toDateString(),
        );
    }
}
