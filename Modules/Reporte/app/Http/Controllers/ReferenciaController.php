<?php

namespace Modules\Reporte\Http\Controllers;
 
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 

class ReferenciaController extends Controller
{ 
    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Reporte/Referencia/IndexReferencia');
    } 
}
