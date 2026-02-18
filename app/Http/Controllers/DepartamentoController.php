<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::orderBy('nombre')->paginate(10);
        return view('departamentos.index', compact('departamentos'));
    }

    public function create()
    {
        return view('departamentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:departamentos',
            'descripcion' => 'nullable|string',
        ]);

        Departamento::create($request->all());

        return redirect()->route('departamentos.index')->with('success', 'Departamento/Servicio creado exitosamente.');
    }

    public function show(Departamento $departamento)
    {
        return view('departamentos.show', compact('departamento'));
    }

    public function edit(Departamento $departamento)
    {
        return view('departamentos.edit', compact('departamento'));
    }

    public function update(Request $request, Departamento $departamento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:departamentos,nombre,' . $departamento->id,
            'descripcion' => 'nullable|string',
        ]);

        $departamento->update($request->all());

        return redirect()->route('departamentos.index')->with('success', 'Departamento/Servicio actualizado exitosamente.');
    }

    public function destroy(Departamento $departamento)
    {
        $departamento->delete();
        return redirect()->route('departamentos.index')->with('success', 'Departamento/Servicio eliminado exitosamente.');
    }
}
