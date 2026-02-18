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

        // Filtro por procedencia
        if ($request->filled('procedencia_id')) {
            $query->where('procedencia_id', $request->input('procedencia_id'));
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

        return view('transferencias-internas.create', compact('departamentos', 'estatuses'));
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
        $this->actualizarUbicacionBien($transferencia);

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

        return view('transferencias-internas.edit', [
            'transferencia' => $transferencias_interna,
            'departamentos' => $departamentos,
            'estatuses' => $estatuses,
        ]);
    }

    /**
     * Actualiza una transferencia interna existente.
     */
    public function update(UpdateTransferenciaInternaRequest $request, TransferenciaInterna $transferencias_interna): RedirectResponse
    {
        $transferencias_interna->update($request->validated());

        // Actualizar ubicación del bien transferido
        $this->actualizarUbicacionBien($transferencias_interna);

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
     * Actualiza la ubicación (departamento) del bien vinculado a la transferencia.
     */
    private function actualizarUbicacionBien(TransferenciaInterna $transferencia): void
    {
        if ($transferencia->bien_externo_id && $transferencia->destino_id) {
            BienExterno::where('id', $transferencia->bien_externo_id)
                ->update(['departamento_id' => $transferencia->destino_id]);
        }
    }
}
