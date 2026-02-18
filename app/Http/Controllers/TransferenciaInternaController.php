<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EstatusActa;
use App\Http\Requests\StoreTransferenciaInternaRequest;
use App\Http\Requests\UpdateTransferenciaInternaRequest;
use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\Departamento;
use App\Models\TransferenciaInterna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransferenciaInternaController extends Controller
{
    /**
     * Muestra el listado de transferencias internas con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = TransferenciaInterna::query()->with(['procedencia', 'destino', 'estatusActa']);

        // Búsqueda por texto
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_bien', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%")
                  ->orWhere('serial', 'like', "%{$buscar}%");
            });
        }

        // Filtro por estatus
        if ($request->filled('estatus_acta_id')) {
            $query->where('estatus_acta_id', $request->input('estatus_acta_id'));
        }

        if ($request->filled('procedencia_id')) {
            if ($request->input('procedencia_id') === 'dtic') {
                $query->whereNull('procedencia_id');
            } else {
                $query->where('procedencia_id', $request->input('procedencia_id'));
            }
        }

        // Filtro por destino
        if ($request->filled('destino_id')) {
            $query->where('destino_id', $request->input('destino_id'));
        }

        // Filtro por fecha (Rango)
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $transferencias = $query->latest()->paginate(10)->withQueryString();
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();

        return view('transferencias-internas.index', compact('transferencias', 'departamentos', 'estatuses'));
    }

    /**
     * Muestra el formulario para crear una nueva transferencia.
     */
    public function create(): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $areas = \App\Models\Area::orderBy('nombre')->get();

        return view('transferencias-internas.create', compact('departamentos', 'estatuses', 'areas'));
    }

    /**
     * Almacena una nueva transferencia interna.
     */
    public function store(StoreTransferenciaInternaRequest $request): RedirectResponse
    {
        $transferencia = TransferenciaInterna::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        // Actualizar ubicación del bien transferido
        $this->actualizarUbicacionBien($transferencia, $request->input('area_id'));

        return redirect()->route('transferencias-internas.index')
            ->with('success', 'Transferencia interna creada exitosamente.');
    }

    /**
     * Muestra el detalle de una transferencia interna.
     */
    public function show(TransferenciaInterna $transferencias_interna): View
    {
        $transferencias_interna->load(['procedencia', 'destino', 'bien', 'bienExterno', 'user', 'estatusActa']);
        return view('transferencias-internas.show', ['transferencia' => $transferencias_interna]);
    }

    /**
     * Muestra el formulario para editar una transferencia interna.
     */
    public function edit(TransferenciaInterna $transferencias_interna): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $areas = \App\Models\Area::orderBy('nombre')->get();

        return view('transferencias-internas.edit', [
            'transferencia' => $transferencias_interna,
            'departamentos' => $departamentos,
            'estatuses' => $estatuses,
            'areas' => $areas,
        ]);
    }

    /**
     * Actualiza una transferencia interna existente.
     */
    public function update(UpdateTransferenciaInternaRequest $request, TransferenciaInterna $transferencias_interna): RedirectResponse
    {
        $transferencias_interna->update($request->validated());

        // Actualizar ubicación del bien transferido
        $this->actualizarUbicacionBien($transferencias_interna, $request->input('area_id'));

        return redirect()->route('transferencias-internas.index')
            ->with('success', 'Transferencia interna actualizada exitosamente.');
    }

    /**
     * Elimina una transferencia interna.
     */
    public function destroy(TransferenciaInterna $transferencias_interna): RedirectResponse
    {
        $transferencias_interna->forceDelete();

        return redirect()->route('transferencias-internas.index')
            ->with('success', 'Transferencia interna eliminada exitosamente.');
    }

    /**
     * Actualiza la ubicación y la tabla del bien (DTIC <-> Externo).
     */
    private function actualizarUbicacionBien(TransferenciaInterna $transferencia, ?string $areaId = null): void
    {
        // Escenario 1: DTIC a Externo (Movimiento de tabla)
        // Origen: DTIC (procedencia_id IS NULL) -> Destino: Externo (destino_id IS NOT NULL)
        if (is_null($transferencia->procedencia_id) && !is_null($transferencia->destino_id)) {
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
        // Origen: Externo (procedencia_id IS NOT NULL) -> Destino: DTIC (destino_id IS NULL)
        elseif (!is_null($transferencia->procedencia_id) && is_null($transferencia->destino_id)) {
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
        elseif (!is_null($transferencia->procedencia_id) && !is_null($transferencia->destino_id)) {
            if ($transferencia->bien_externo_id) {
                BienExterno::where('id', $transferencia->bien_externo_id)
                    ->update(['departamento_id' => $transferencia->destino_id]);
            }
        }

        // Escenario 4: DTIC a DTIC (Movimiento interno)
        // Solo actualizamos el área del bien interno
        elseif (is_null($transferencia->procedencia_id) && is_null($transferencia->destino_id)) {
            if ($transferencia->bien_id && $areaId) {
                Bien::where('id', $transferencia->bien_id)
                    ->update(['area_id' => $areaId]);
            }
        }
    }
}
