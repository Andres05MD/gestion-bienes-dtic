<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBienExternoRequest;
use App\Http\Requests\UpdateBienExternoRequest;
use App\Models\Departamento;
use App\Models\BienExterno;
use App\Models\CategoriaBien;
use App\Models\Estado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BienExternoController extends Controller
{
    /**
     * Muestra el listado de bienes externos con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = BienExterno::query()->with(['categoria', 'estado', 'departamento']);

        // Búsqueda por texto
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('equipo', 'like', "%{$buscar}%")
                  ->orWhere('numero_bien', 'like', "%{$buscar}%")
                  ->orWhere('marca', 'like', "%{$buscar}%")
                  ->orWhere('modelo', 'like', "%{$buscar}%")
                  ->orWhere('serial', 'like', "%{$buscar}%")
                  ->orWhereHas('departamento', function ($qDep) use ($buscar) {
                      $qDep->where('nombre', 'like', "%{$buscar}%");
                  });
            });
        }

        // Filtros
        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->input('estado_id'));
        }
        if ($request->filled('categoria_bien_id')) {
            $query->where('categoria_bien_id', $request->input('categoria_bien_id'));
        }
        if ($request->filled('departamento_id')) {
            $query->where('departamento_id', $request->input('departamento_id'));
        }

        $bienes = $query->latest()->paginate(10)->withQueryString();
        $estados = Estado::orderBy('nombre')->get();
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $departamentos = Departamento::orderBy('nombre')->get(); // Departamentos en lugar de Áreas

        return view('bienes-externos.index', compact('bienes', 'estados', 'categorias', 'departamentos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo recurso.
     */
    public function create(): View
    {
        $estados = Estado::orderBy('nombre')->get();
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('bienes-externos.create', compact('estados', 'categorias', 'departamentos'));
    }

    /**
     * Almacenar un recurso recién creado.
     */
    public function store(StoreBienExternoRequest $request): RedirectResponse
    {
        BienExterno::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('bienes-externos.index')
            ->with('success', 'Bien Externo creado exitosamente.');
    }

    /**
     * Muestra el detalle de un bien específico.
     */
    public function show(BienExterno $bienExterno): View
    {
        $bienExterno->load(['user', 'categoria', 'departamento', 'estado']);
        return view('bienes-externos.show', compact('bienExterno'));
    }

    /**
     * Muestra el formulario para editar el recurso.
     */
    public function edit(BienExterno $bienExterno): View
    {
        $estados = Estado::orderBy('nombre')->get();
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('bienes-externos.edit', compact('bienExterno', 'estados', 'categorias', 'departamentos'));
    }

    /**
     * Actualiza el recurso especificado.
     */
    public function update(UpdateBienExternoRequest $request, BienExterno $bienExterno): RedirectResponse
    {
        $bienExterno->update($request->validated());

        return redirect()->route('bienes-externos.index')
            ->with('success', 'Bien Externo actualizado exitosamente.');
    }

    /**
     * Elimina el recurso especificado.
     */
    public function destroy(BienExterno $bienExterno): RedirectResponse
    {
        $bienExterno->delete(); // O forceDelete si no hay softDeletes (en migracion no puse softDeletes)

        return redirect()->route('bienes-externos.index')
            ->with('success', 'Bien Externo eliminado exitosamente.');
    }
}
