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

class DesincorporacionController extends Controller
{
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

        $desincorporaciones = $query->paginate(10)->withQueryString();
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();

        return view('desincorporaciones.index', compact('desincorporaciones', 'departamentos', 'estatuses'));
    }

    /**
     * Muestra el formulario para crear una nueva desincorporación.
     */
    public function create(): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();

        return view('desincorporaciones.create', compact('departamentos', 'estatuses'));
    }

    /**
     * Almacena una nueva desincorporación.
     */
    public function store(StoreDesincorporacionRequest $request): RedirectResponse
    {
        $desincorporacion = Desincorporacion::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        // Marcar el bien como desincorporado
        $this->marcarBienDesincorporado($desincorporacion);

        return redirect()->route('desincorporaciones.index')
            ->with('success', 'Desincorporación creada exitosamente.');
    }

    /**
     * Muestra el detalle de una desincorporación.
     */
    public function show(Desincorporacion $desincorporacione): View
    {
        $desincorporacione->load(['procedencia', 'bien', 'bienExterno', 'user', 'estatusActa']);
        return view('desincorporaciones.show', ['desincorporacion' => $desincorporacione]);
    }

    /**
     * Muestra el formulario para editar una desincorporación.
     */
    public function edit(Desincorporacion $desincorporacione): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();

        return view('desincorporaciones.edit', [
            'desincorporacion' => $desincorporacione,
            'departamentos' => $departamentos,
            'estatuses' => $estatuses,
        ]);
    }

    /**
     * Actualiza una desincorporación existente.
     */
    public function update(UpdateDesincorporacionRequest $request, Desincorporacion $desincorporacione): RedirectResponse
    {
        $desincorporacione->update($request->validated());

        return redirect()->route('desincorporaciones.index')
            ->with('success', 'Desincorporación actualizada exitosamente.');
    }

    /**
     * Elimina una desincorporación.
     */
    public function destroy(Desincorporacion $desincorporacione): RedirectResponse
    {
        $desincorporacione->forceDelete();

        return redirect()->route('desincorporaciones.index')
            ->with('success', 'Desincorporación eliminada exitosamente.');
    }

    /**
     * Marca el bien vinculado con el estado "Desincorporado".
     */
    private function marcarBienDesincorporado(Desincorporacion $desincorporacion): void
    {
        $estadoDesincorporado = Estado::where('nombre', 'Desincorporado')->first();
        if (!$estadoDesincorporado) return;

        if ($desincorporacion->bien_id) {
            Bien::where('id', $desincorporacion->bien_id)
                ->update(['estado_id' => $estadoDesincorporado->id]);
        } elseif ($desincorporacion->bien_externo_id) {
            BienExterno::where('id', $desincorporacion->bien_externo_id)
                ->update(['estado_id' => $estadoDesincorporado->id]);
        }
    }
}
