<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBienRequest;
use App\Http\Requests\UpdateBienRequest;
use App\Models\Area;
use App\Models\Bien;
use App\Models\CategoriaBien;
use App\Models\Estado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BienController extends Controller
{
    /**
     * Muestra el listado de bienes con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = Bien::query()->with('categoria');

        // Búsqueda por texto
        // Comparación por área (ubicación) - se busca en la relación
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('equipo', 'like', "%{$buscar}%")
                  ->orWhere('numero_bien', 'like', "%{$buscar}%")
                  ->orWhere('marca', 'like', "%{$buscar}%")
                  ->orWhere('modelo', 'like', "%{$buscar}%")
                  ->orWhere('serial', 'like', "%{$buscar}%")
                  ->orWhereHas('area', function ($qArea) use ($buscar) {
                      $qArea->where('nombre', 'like', "%{$buscar}%");
                  });
            });
        }

        // Filtro por estado
        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->input('estado_id'));
        }

        // Filtro por categoría
        if ($request->filled('categoria_bien_id')) {
            $query->where('categoria_bien_id', $request->input('categoria_bien_id'));
        }

        // Filtro por área
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->input('area_id'));
        }

        $bienes = $query->with(['estado', 'area'])->latest()->paginate(10)->withQueryString();
        $estados = Estado::orderBy('nombre')->get();
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $areas = Area::orderBy('nombre')->get();

        return view('bienes.index', compact('bienes', 'estados', 'categorias', 'areas'));
    }

    /**
     * Mostrar el formulario para crear un nuevo recurso.
     */
    public function create(): View
    {
        $estados = Estado::orderBy('nombre')->get();
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $areas = Area::orderBy('nombre')->get();

        return view('bienes.create', compact('estados', 'categorias', 'areas'));
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     */
    public function store(StoreBienRequest $request): RedirectResponse
    {
        Bien::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('bienes.index')
            ->with('success', 'Bien creado exitosamente.');
    }

    /**
     * Muestra el detalle de un bien específico.
     */
    public function show(Bien $bien): View
    {
        $bien->load(['user', 'categoria', 'area', 'estado']);
        return view('bienes.show', compact('bien'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Bien $bien): View
    {
        $estados = Estado::orderBy('nombre')->get();
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $areas = Area::orderBy('nombre')->get();

        return view('bienes.edit', compact('bien', 'estados', 'categorias', 'areas'));
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(UpdateBienRequest $request, Bien $bien): RedirectResponse
    {
        $bien->update($request->validated());

        return redirect()->route('bienes.index')
            ->with('success', 'Bien actualizado exitosamente.');
    }

    /**
     * Elimina el recurso especificado del almacenamiento (soft delete).
     */
    public function destroy(Bien $bien): RedirectResponse
    {
        $bien->forceDelete();

        return redirect()->route('bienes.index')
            ->with('success', 'Bien eliminado exitosamente.');
    }
}
