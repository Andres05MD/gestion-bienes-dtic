<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EstatusActa;
use App\Models\Estado;
use App\Http\Requests\StoreDesincorporacionRequest;
use App\Http\Requests\UpdateDesincorporacionRequest;
use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\Departamento;
use App\Models\Desincorporacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Services\BienMovimientoService;

class DesincorporacionController extends Controller
{
    public function __construct(private BienMovimientoService $movimientoService) {}

    /**
     * Muestra el listado de desincorporaciones con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = Desincorporacion::query()->with(['procedencia', 'estatusActa']);

        // Búsqueda por texto
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_bien', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('serial', 'like', "%{$buscar}%")
                    ->orWhere('numero_informe', 'like', "%{$buscar}%");
            });
        }

        // Filtro por estatus
        if ($request->filled('estatus_acta_id')) {
            $query->where('estatus_acta_id', $request->input('estatus_acta_id'));
        }

        // Filtro por procedencia
        if ($request->filled('procedencia_id')) {
            $query->where('procedencia_id', $request->input('procedencia_id'));
        }

        // Filtro por fecha (Rango)
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Agrupación por codigo_acta para visualización
        $agrupadores = $query->clone()
            ->selectRaw('COALESCE(codigo_acta, CONCAT("legacy_", id)) as group_key, MAX(id) as max_id')
            ->groupBy('group_key')
            ->orderByDesc('max_id');

        $gruposPaginados = DB::query()->select('group_key')
            ->fromSub($agrupadores, 'agrupados')
            ->paginate(10)
            ->withQueryString();

        $clavesPagina = collect($gruposPaginados->items())->pluck('group_key');

        $desincorporacionesFinales = Desincorporacion::with(['procedencia', 'estatusActa', 'bien', 'bienExterno'])
            ->whereIn(DB::raw('COALESCE(codigo_acta, CONCAT("legacy_", id))'), $clavesPagina)
            ->latest('id')
            ->get();

        $desincorporacionesAgrupadas = $desincorporacionesFinales->groupBy(function ($item) {
            return $item->codigo_acta ?? 'legacy_' . $item->id;
        });

        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();

        return view('desincorporaciones.index', [
            'desincorporacionesPaginadas' => $gruposPaginados,
            'desincorporacionesAgrupadas' => $desincorporacionesAgrupadas,
            'departamentos' => $departamentos,
            'estatuses' => $estatuses
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva desincorporación.
     */
    public function create(): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $areas = \App\Models\Area::orderBy('nombre')->get();

        // Buscar el destino predeterminado (Insensible a mayúsculas/minúsculas para robustez)
        $destinoPredeterminadoId = Departamento::where('nombre', 'LIKE', 'Administración - Bienes y Materias')->first()?->id;
        $dticId = Departamento::where('nombre', 'LIKE', 'DTIC')->first()?->id;

        return view('desincorporaciones.create', compact('departamentos', 'estatuses', 'destinoPredeterminadoId', 'areas', 'dticId'));
    }

    /**
     * Almacena una nueva desincorporación.
     */
    public function store(StoreDesincorporacionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            $codigoActa = Str::uuid()->toString();

            $numeroInforme = null;
            if ($request->filled('numero_informe')) {
                $informes = collect($request->input('numero_informe'))
                    ->filter()
                    ->unique()
                    ->toArray();
                $numeroInforme = !empty($informes) ? implode(', ', $informes) : null;
            }

            $commonData = [
                'codigo_acta' => $codigoActa,
                'procedencia_id' => $validated['procedencia_id'],
                'destino_id' => $validated['destino_id'],
                'fecha' => $validated['fecha'],
                'numero_informe' => $numeroInforme,
                'estatus_acta_id' => $validated['estatus_acta_id'],
                'observaciones' => $validated['observaciones'] ?? null,
                'user_id' => auth()->id(),
            ];

            foreach ($validated['bienes'] as $bienData) {
                $desincorporacion = Desincorporacion::create([
                    ...$commonData,
                    'numero_bien' => $bienData['numero_bien'],
                    'descripcion' => $bienData['descripcion'],
                    'serial' => $bienData['serial'] ?? null,
                    'bien_id' => $bienData['tipo'] === 'dtic' ? $bienData['id'] : null,
                    'bien_externo_id' => $bienData['tipo'] === 'externo' ? $bienData['id'] : null,
                ]);

                // Marcar el bien como desincorporado usando el servicio
                $this->movimientoService->marcarBienDesincorporado($desincorporacion);
            }
        });

        return redirect()->route('desincorporaciones.index')
            ->with('success', 'Desincorporación creada exitosamente.');
    }

    /**
     * Muestra el detalle de una desincorporación.
     */
    public function show(Desincorporacion $desincorporacione): View
    {
        $desincorporacione->load(['procedencia', 'bien', 'bienExterno', 'user', 'estatusActa']);
        $bienesGrupo = $desincorporacione->codigo_acta
            ? Desincorporacion::where('codigo_acta', $desincorporacione->codigo_acta)->get()
            : collect([$desincorporacione]);

        return view('desincorporaciones.show', [
            'desincorporacion' => $desincorporacione,
            'bienesGrupo' => $bienesGrupo
        ]);
    }

    /**
     * Muestra el formulario para editar una desincorporación.
     */
    public function edit(Desincorporacion $desincorporacione): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $areas = \App\Models\Area::orderBy('nombre')->get();
        $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;

        $bienesGrupo = $desincorporacione->codigo_acta
            ? Desincorporacion::where('codigo_acta', $desincorporacione->codigo_acta)->get()
            : collect([$desincorporacione]);

        return view('desincorporaciones.edit', [
            'desincorporacion' => $desincorporacione,
            'bienesGrupo' => $bienesGrupo,
            'departamentos' => $departamentos,
            'estatuses' => $estatuses,
            'areas' => $areas,
            'dticId' => $dticId,
        ]);
    }

    /**
     * Actualiza una desincorporación existente.
     */
    public function update(UpdateDesincorporacionRequest $request, Desincorporacion $desincorporacione): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $desincorporacione, $request) {
            $numeroInforme = null;
            if ($request->filled('numero_informe')) {
                $informes = collect($request->input('numero_informe'))
                    ->filter()
                    ->unique()
                    ->toArray();
                $numeroInforme = !empty($informes) ? implode(', ', $informes) : null;
            }

            $commonData = [
                'procedencia_id' => $validated['procedencia_id'],
                'destino_id' => $validated['destino_id'],
                'fecha' => $validated['fecha'],
                'numero_informe' => $numeroInforme,
                'estatus_acta_id' => $validated['estatus_acta_id'],
                'observaciones' => $validated['observaciones'] ?? null,
            ];

            if ($desincorporacione->codigo_acta) {
                Desincorporacion::where('codigo_acta', $desincorporacione->codigo_acta)->update($commonData);
            } else {
                $desincorporacione->update($commonData);
            }
        });

        return redirect()->route('desincorporaciones.index')
            ->with('success', 'Desincorporación actualizada exitosamente.');
    }

    /**
     * Elimina una desincorporación.
     */
    public function destroy(Desincorporacion $desincorporacione): RedirectResponse
    {
        DB::transaction(function () use ($desincorporacione) {
            if ($desincorporacione->codigo_acta) {
                Desincorporacion::where('codigo_acta', $desincorporacione->codigo_acta)->forceDelete();
            } else {
                $desincorporacione->forceDelete();
            }
        });

        return redirect()->route('desincorporaciones.index')
            ->with('success', 'Desincorporación eliminada exitosamente.');
    }
}
