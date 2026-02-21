<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EstatusActa;
use App\Http\Requests\StoreMantenimientoRequest;
use App\Http\Requests\UpdateMantenimientoRequest;
use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\Departamento;
use App\Models\Area;
use App\Models\Mantenimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Services\BienMovimientoService;

class MantenimientoController extends Controller
{
    public function __construct(private BienMovimientoService $movimientoService) {}

    /**
     * Muestra el listado de mantenimientos con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = Mantenimiento::query()->with([
            'procedencia',
            'destino',
            'estatusActa',
            'bien',
            'bienExterno'
        ]);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_bien', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('serial', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estatus_acta_id')) {
            $query->where('estatus_acta_id', $request->input('estatus_acta_id'));
        }
        if ($request->filled('procedencia_id')) {
            $query->where('procedencia_id', $request->input('procedencia_id'));
        }
        if ($request->filled('destino_id')) {
            $query->where('destino_id', $request->input('destino_id'));
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Obtener la colección de mantenimientos paginada (similar a transferencias pero agrupada por n_orden_acta)
        $agrupadores = $query->clone()
            ->selectRaw('COALESCE(n_orden_acta, CONCAT("legacy_", id)) as group_key, MAX(id) as max_id')
            ->groupBy('group_key')
            ->orderByDesc('max_id');

        $gruposPaginados = \Illuminate\Support\Facades\DB::query()->select('group_key')
            ->fromSub($agrupadores, 'agrupados')
            ->paginate(10)
            ->withQueryString();

        $clavesPagina = collect($gruposPaginados->items())->pluck('group_key');

        $mantenimientosFinales = Mantenimiento::with([
            'procedencia',
            'destino',
            'estatusActa',
            'bien',
            'bienExterno'
        ])
            ->whereIn(\Illuminate\Support\Facades\DB::raw('COALESCE(n_orden_acta, CONCAT("legacy_", id))'), $clavesPagina)
            ->latest('id')
            ->get();

        $mantenimientosAgrupados = $mantenimientosFinales->groupBy(function ($item) {
            return $item->n_orden_acta ?? 'legacy_' . $item->id;
        });

        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $dticAreaMantenimiento = Area::where('nombre', 'Soporte Técnico - Mantenimiento')->first();

        return view('mantenimientos.index', [
            'mantenimientosPaginadas' => $gruposPaginados,
            'mantenimientosAgrupadas' => $mantenimientosAgrupados,
            'departamentos' => $departamentos,
            'estatuses' => $estatuses,
            'dticAreaMantenimiento' => $dticAreaMantenimiento,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo registro de mantenimiento (ENTRADA a DTIC).
     */
    public function create(): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;
        $areaMantenimiento = Area::where('nombre', 'Soporte Técnico - Mantenimiento')->first();

        return view('mantenimientos.create', compact('departamentos', 'estatuses', 'dticId', 'areaMantenimiento'));
    }

    /**
     * Almacena un nuevo mantenimiento.
     */
    public function store(StoreMantenimientoRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $tipo = $request->input('devolviendo', false) ? 'salida' : 'entrada';
        $areaMantenimiento = Area::where('nombre', 'Soporte Técnico - Mantenimiento')->first();
        $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request, $tipo, $areaMantenimiento, $dticId) {
            $codigoActa = $validated['n_orden_acta'] ?? Str::uuid()->toString();

            $commonData = [
                'n_orden_acta' => $validated['n_orden_acta'] ?? null,
                'fecha_acta' => $validated['fecha_acta'] ?? null,
                'procedencia_id' => $validated['procedencia_id'],
                'destino_id' => $validated['destino_id'],
                'fecha' => $validated['fecha'],
                'estatus_acta_id' => $validated['estatus_acta_id'],
                'fecha_firma' => $validated['fecha_firma'] ?? null,
                'user_id' => auth()->id(),
                'tipo_movimiento' => $tipo,
            ];

            // Forzar lógicas según el tipo
            if ($tipo === 'entrada') {
                $commonData['destino_id'] = $dticId;
                $commonData['area_id'] = $areaMantenimiento?->id;
            } else {
                // Entrada del formulario de devolución
                $commonData['procedencia_id'] = $dticId;
                $commonData['area_procedencia_id'] = $areaMantenimiento?->id;
            }

            foreach ($validated['bienes'] as $bienData) {
                // Primero buscamos el bien original 
                $bienOriginal = null;
                if ($bienData['tipo'] === 'dtic') {
                    $bienOriginal = Bien::find($bienData['id']);
                } else {
                    $bienOriginal = BienExterno::find($bienData['id']);
                }

                $mantenimiento = Mantenimiento::create([
                    ...$commonData,
                    'numero_bien' => $bienData['numero_bien'],
                    'descripcion' => $bienData['descripcion'],
                    'serial' => $bienData['serial'] ?? null,
                    'bien_id' => $bienData['tipo'] === 'dtic' ? $bienData['id'] : null,
                    'bien_externo_id' => $bienData['tipo'] === 'externo' ? $bienData['id'] : null,
                ]);

                // Actualizar ubicación utilizando el servicio, basado en qué estamos haciendo
                if ($tipo === 'entrada') {
                    // Mover el bien a DTIC - Mantenimiento
                    $this->movimientoService->actualizarUbicacionBien($mantenimiento, $areaMantenimiento?->id);
                } else {
                    // Devolverlo (salida).
                    $destAreaId = ($validated['destino_id'] == $dticId) ? ($validated['area_id'] ?? null) : null;
                    $this->movimientoService->actualizarUbicacionBien($mantenimiento, $destAreaId);
                }
            }
        });

        return redirect()->route('mantenimientos.index')
            ->with('success', 'Mantenimiento registrado exitosamente.');
    }

    /**
     * Muestra el detalle.
     */
    public function show(Mantenimiento $mantenimiento): View
    {
        $mantenimiento->load(['procedencia', 'destino', 'bien', 'bienExterno', 'user', 'estatusActa', 'area']);
        return view('mantenimientos.show', ['mantenimiento' => $mantenimiento]);
    }

    /**
     * Muestra el formulario para editar.
     */
    public function edit(Mantenimiento $mantenimiento): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;

        return view('mantenimientos.edit', compact('mantenimiento', 'departamentos', 'estatuses', 'dticId'));
    }

    /**
     * Actualiza un mantenimiento.
     */
    public function update(UpdateMantenimientoRequest $request, Mantenimiento $mantenimiento): RedirectResponse
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $mantenimiento) {
            $mantenimiento->update($request->validated());

            // Actualizar ubicación del bien en caso de cambio
            $destAreaId = ($request->destino_id == \App\Models\Departamento::where('nombre', 'DTIC')->first()?->id)
                ? $request->input('area_id')
                : null;

            $this->movimientoService->actualizarUbicacionBien($mantenimiento, $destAreaId);
        });

        return redirect()->route('mantenimientos.index')
            ->with('success', 'Mantenimiento actualizado exitosamente.');
    }

    /**
     * Elimina un mantenimiento.
     */
    public function destroy(Mantenimiento $mantenimiento): RedirectResponse
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($mantenimiento) {
            if ($mantenimiento->n_orden_acta) {
                Mantenimiento::where('n_orden_acta', $mantenimiento->n_orden_acta)->forceDelete();
            } else {
                $mantenimiento->forceDelete();
            }
        });

        return redirect()->route('mantenimientos.index')
            ->with('success', 'Mantenimiento eliminado exitosamente.');
    }

    /**
     * Muestra la vista para devolver el bien desde Mantenimiento al origen (SALIDA).
     */
    public function devolver(Mantenimiento $mantenimiento): View
    {
        $mantenimiento->load(['bien', 'bienExterno', 'procedencia']);
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;
        $areaMantenimiento = Area::where('nombre', 'Soporte Técnico - Mantenimiento')->first();

        // El destino por defecto es quien fue su "procedencia" original cuando entró.
        $destinoOriginalId = $mantenimiento->procedencia_id;

        return view('mantenimientos.devolver', compact('mantenimiento', 'departamentos', 'estatuses', 'dticId', 'areaMantenimiento', 'destinoOriginalId'));
    }
}
