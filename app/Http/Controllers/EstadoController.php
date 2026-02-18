<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::orderBy('nombre')->paginate(10);
        return view('estados.index', compact('estados'));
    }

    public function create()
    {
        return view('estados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados',
            'descripcion' => 'nullable|string',
        ]);

        Estado::create($request->all());

        return redirect()->route('estados.index')->with('success', 'Estado creado exitosamente.');
    }

    public function show(Estado $estado)
    {
        return view('estados.show', compact('estado'));
    }

    public function edit(Estado $estado)
    {
        return view('estados.edit', compact('estado'));
    }

    public function update(Request $request, Estado $estado)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre,' . $estado->id,
            'descripcion' => 'nullable|string',
        ]);

        $estado->update($request->all());

        return redirect()->route('estados.index')->with('success', 'Estado actualizado exitosamente.');
    }

    public function destroy(Estado $estado)
    {
        $estado->forceDelete();
        return redirect()->route('estados.index')->with('success', 'Estado eliminado exitosamente.');
    }
}
