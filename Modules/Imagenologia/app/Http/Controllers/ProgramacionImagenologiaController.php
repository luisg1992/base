<?php

namespace Modules\Imagenologia\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Modules\Imagenologia\Http\Requests\ProgramacionImagenologiaRequest;
use Modules\Imagenologia\Models\ProgramacionImagenologia;
use Modules\Imagenologia\Http\Resources\ProgramacionImagenologiaResource;

class ProgramacionImagenologiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Modulos/Imagenologia/ProgramacionImagenologia/IndexProgramacionImagenologia');
    }

    public function FactPuntosCargaFiltrar(Request $request)
    {
        $lcFiltro = $request->LcFiltro  ?? null;
        $data = DB::select(
            'EXEC FactPuntosCargaFiltrar @lcFiltro = ?',
            [$lcFiltro]
        );
        return response()->json($data);
    }

    public function WebS_ProgramacionImagenologia_Lista_BuscarFiltro(Request $request)
    {
        $idPuntoCarga = $request->IdPuntoCarga  ?? null;
        $data = DB::select(
            'EXEC WebS_ProgramacionImagenologia_Lista_BuscarFiltro @IdPuntoCarga = ?',
            [$idPuntoCarga]
        );
        return response()->json($data);
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
    public function store(ProgramacionImagenologiaRequest $request) 
    {
        $data = $request->all();
        if(!empty($request->id)){
            $record = ProgramacionImagenologia::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = ProgramacionImagenologia::max('IdPuntoCarga');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdProgramacion'] = $nuevoId;

            $record = ProgramacionImagenologia::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA');
            

    }
    
    public function WebS_InsertarProgramacionImagenologia(Request $request)
    {
        $resultado = DB::select(
            'EXEC WebS_InsertarProgramacionImagenologia
                    @IdPuntoCarga = ?,
                    @Cupos = ?,
                    @Fecha = ?',
            [
                $request->IdPuntoCarga,
                $request->Cupos,
                $request->Fecha
            ]
        );

        // Manejar el resultado
        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA');
            } else {
                return obtener_respuesta_error('NO SE PUDO GUARDAR LA DATA');
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $record = ProgramacionImagenologia::query()->find($id);
        if(!$record){
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }

        return new ProgramacionImagenologiaResource($record);
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
