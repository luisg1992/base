<?php

namespace Modules\Api\Http\Controllers\Colegio;

use App\Http\Controllers\Controller;
use DiDom\Document;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TecnologoController extends Controller
{
    public function consultarPorCtm(Request $request)
    {
        $ctm = $request->input('ctm');
        $start = microtime(true);

        try {
            $url = 'https://ctmperu.org.pe/busqueda-de-colegiado/?query=' . urlencode($ctm);
            $response = Http::timeout(5)->get($url);

            $response->throw();

            $doc = new Document($response->body());
            $uls = $doc->find('.documentos-lista');

            if (!isset($uls[0])) {
                throw new Exception('No se encontró la lista de documentos');
            }

            $lis = $uls[0]->find('li');
            if (count($lis) !== 3) {
                return [
                    'success' => false,
                    'message' => 'Formato inesperado de respuesta',
                    'time' => microtime(true) - $start,
                ];
            }

            $div1 = $lis[1]->find('div');
            $div2 = $lis[2]->find('div');

            $data = [
                'ctm' => $ctm,
                'nombres' => trim($div1[2]->text() ?? ''),
                'apellidos' => trim($div1[5]->text() ?? ''),
                'especialidad' => trim($div2[2]->text() ?? ''),
                'consejo' => str_to_upper_utf8(trim($div2[5]->text() ?? '')),
            ];

            return [
                'success' => true,
                'data' => $data,
                'time' => microtime(true) - $start,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'time' => microtime(true) - $start,
            ];
        }
    }
}
