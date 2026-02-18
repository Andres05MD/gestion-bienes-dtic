<?php

namespace App\Http\Controllers;

use App\Models\CategoriaBien;
use Illuminate\Http\Request;

class CategoriaBienController extends Controller
{
    public function index()
    {
        $categorias = CategoriaBien::orderBy('nombre')->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categoria_bienes',
            'descripcion' => 'nullable|string',
        ]);

        CategoriaBien::create($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function show(CategoriaBien $categoria)
    {
        return view('categorias.show', compact('categoria'));
    }

    public function edit(CategoriaBien $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, CategoriaBien $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categoria_bienes,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(CategoriaBien $categoria)
    {
        $categoria->forceDelete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}
