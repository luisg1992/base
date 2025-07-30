<?php

namespace Modules\Imagenologia\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Imagenologia\DataTables\ListaEsperaDataTable;

class ListaEsperaController extends Controller
{
    use ListaEsperaDataTable;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Modulos/Imagenologia/ListaEspera/IndexListaEspera');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('imagenologia::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('imagenologia::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('imagenologia::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
