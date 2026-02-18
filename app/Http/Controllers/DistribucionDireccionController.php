<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistribucionDireccionRequest;
use App\Http\Requests\UpdateDistribucionDireccionRequest;
use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\Departamento;
use App\Models\DistribucionDireccion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistribucionDireccionController extends Controller
{
    /**
     * Muestra el listado de distribuciones de dirección con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = DistribucionDireccion::query()->with('procedencia');

        // Búsqueda por texto
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_bien', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%")
                  ->orWhere('serial', 'like', "%{$buscar}%")
                  ->orWhere('marca', 'like', "%{$buscar}%");
            });
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

        $distribuciones = $query->latest()->paginate(10)->withQueryString();
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('distribuciones-direccion.index', compact('distribuciones', 'departamentos'));
    }

    /**
     * Muestra el formulario para crear una nueva distribución.
     */
    public function create(): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('distribuciones-direccion.create', compact('departamentos'));
    }

    /**
     * Almacena una nueva distribución de dirección.
     */
    public function store(StoreDistribucionDireccionRequest $request): RedirectResponse
    {
        $distribucion = DistribucionDireccion::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        // Actualizar ubicación del bien distribuido
        $this->actualizarUbicacionBien($distribucion);

        return redirect()->route('distribuciones-direccion.index')
            ->with('success', 'Distribución de dirección creada exitosamente.');
    }

    /**
     * Muestra el detalle de una distribución de dirección.
     */
    public function show(DistribucionDireccion $distribuciones_direccion): View
    {
        $distribuciones_direccion->load(['procedencia', 'bien', 'bienExterno', 'user']);
        return view('distribuciones-direccion.show', ['distribucion' => $distribuciones_direccion]);
    }

    /**
     * Muestra el formulario para editar una distribución de dirección.
     */
    public function edit(DistribucionDireccion $distribuciones_direccion): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('distribuciones-direccion.edit', [
            'distribucion' => $distribuciones_direccion,
            'departamentos' => $departamentos,
        ]);
    }

    /**
     * Actualiza una distribución de dirección.
     */
    public function update(UpdateDistribucionDireccionRequest $request, DistribucionDireccion $distribuciones_direccion): RedirectResponse
    {
        $distribuciones_direccion->update($request->validated());

        // Actualizar ubicación del bien distribuido
        $this->actualizarUbicacionBien($distribuciones_direccion);

        return redirect()->route('distribuciones-direccion.index')
            ->with('success', 'Distribución de dirección actualizada exitosamente.');
    }

    /**
     * Elimina una distribución de dirección.
     */
    public function destroy(DistribucionDireccion $distribuciones_direccion): RedirectResponse
    {
        $distribuciones_direccion->forceDelete();

        return redirect()->route('distribuciones-direccion.index')
            ->with('success', 'Distribución de dirección eliminada exitosamente.');
    }

    /**
     * Actualiza la ubicación (departamento) del bien vinculado a la distribución.
     */
    private function actualizarUbicacionBien(DistribucionDireccion $distribucion): void
    {
        if ($distribucion->bien_externo_id && $distribucion->procedencia_id) {
            BienExterno::where('id', $distribucion->bien_externo_id)
                ->update(['departamento_id' => $distribucion->procedencia_id]);
        }
    }
}
