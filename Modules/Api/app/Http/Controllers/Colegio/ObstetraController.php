<?php

namespace Modules\Api\Http\Controllers\Colegio;

use App\Http\Controllers\Controller;
use DiDom\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ObstetraController extends Controller
{
    public function consultarPorNombre(Request $request)
    {
        $apellidoPaterno = $request->input('apellido_paterno');
        $apellidoMaterno = $request->input('apellido_materno');
        $nombres = $request->input('nombres');
        $start = microtime(true);

        $form_params = [
            'cop' => '',
            'nombres' => $nombres,
            'pape' => $apellidoPaterno,
            'mape' => $apellidoMaterno,
        ];

        try {
            $url = 'https://consulta.colegiodeobstetras.pe/';
            $response = Http::timeout(5)->get($url, $form_params);
            $response->throw();

            $doc = new Document(utf8_encode($response->body()));
            $rows = $doc->find('.information');
            $data = [];

            foreach ($rows as $row) {
                $href = optional($row->find('a')[0])->attr('href');
                if ($href) {
                    $parsed = $this->parseFichaObstetra($href);
                    if ($parsed) $data[] = $parsed;
                }
            }

            return [
                'success' => true,
                'data' => $data,
                'time' => microtime(true) - $start,
            ];

        } catch (\Throwable $e) {
            return $this->handleException($e, $start);
        }
    }

    public function consultarPorCop(Request $request)
    {
        $cop = $request->input('cop');
        if (empty($cop)) {
            return $this->handleException('El campo cop es requerido');
        }

        $start = microtime(true);

        $form_params = [
            'cop' => $cop,
            'nombres' => '',
            'pape' => '',
            'mape' => '',
        ];

        try {
            $url = 'https://consulta.colegiodeobstetras.pe/';
            $response = Http::timeout(5)->get($url, $form_params);
            $response->throw();

            $doc = new Document(utf8_encode($response->body()));
            $row = $doc->first('.information');
            $href = optional($row?->find('a')[0])->attr('href');
            $parsed = $href ? $this->parseFichaObstetra($href) : null;

            return [
                'success' => (bool)$parsed,
                'data' => $parsed,
                'time' => microtime(true) - $start,
            ];

        } catch (\Throwable $e) {
            return $this->handleException($e, $start);
        }
    }

    /**
     * Parsea la ficha de obstetra desde la URL del detalle.
     */
    private function parseFichaObstetra(string $href): ?array
    {
        try {
            $res = Http::timeout(5)->get($href);
            $res->throw();

            $doc2 = new Document(utf8_encode($res->body()));
            $textInner = $doc2->first('div.et_pb_text_inner');
            if (!$textInner) return null;

            $img = optional($textInner->first('img'))->attr('src');
            $divs = $textInner->find('div.frm6');
            $divs2 = $divs[1]->find('div.frm_grid_container') ?? [];
            $divs3 = $divs2[2]->find('div.frm12') ?? [];

            // COP
            $cop_text = str_replace(["\r", "\n"], '', $divs2[1]->text() ?? '');
            $cop_array = explode(' ', $cop_text);
            $cop = $cop_array[1] ?? null;

            // Nombre
            $nombre_completo = trim(str_replace(["\r", "\n"], ' ', $divs3[2]->text() ?? ''));

            // Colegio regional
            $colegio_regional_array = explode('|', str_replace(["\r", "\n"], '|', $divs3[3]->text() ?? ''));
            $colegio_regional = $colegio_regional_array[1] ?? null;

            // Estado
            $estado_array = explode('|', str_replace(["\r", "\n"], '|', $divs3[5]->text() ?? ''));
            $estado = $estado_array[2] ?? null;

            // Foto
            $photo = ($img && !str_contains($img, "mujerobs.jpeg"))
                ? 'https://consulta.colegiodeobstetras.pe'.$img
                : null;

            return [
                'cop' => $cop,
                'nombres' => $nombre_completo,
                'photo' => $photo ?? null,
                'colegio_regional' => $colegio_regional,
                'estado' => isset($estado) ? str_to_upper_utf8($estado) : null,
            ];
        } catch (\Throwable $e) {
            // Si falla el parseo de una ficha, devuelvo null, no rompe el flujo principal
            return null;
        }
    }

    /**
     * Manejo centralizado de errores para ambos métodos.
     */
    private function handleException($e, $start = null)
    {
        return [
            'success' => false,
            'message' => is_string($e) ? $e : $e->getMessage(),
            'time' => $start ? microtime(true) - $start : null,
        ];
    }
}
