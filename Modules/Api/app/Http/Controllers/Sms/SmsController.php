<?php

namespace Modules\Api\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use ErrorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\Core\Models\SmsCelular;

class SmsController extends Controller
{
    public function enviar(Request $request)
    {
        $params = [
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'phone' => $request->input('phone'),
        ];

        return $this->sendByFormParams($params);
    }

    public function sendByFormParams(array $form_params, $id = null)
    {
        if($id) {
            $smsCelular = SmsCelular::query()->find($id);
        } else {
            $smsCelular = SmsCelular::query()
                ->where('Estado', true)
                ->inRandomOrder()
                ->first();
        }

        $start = microtime(true);
        $url = $smsCelular->Url;

        try {
            $response = Http::timeout(10)
                ->withToken($smsCelular->Token)
                ->post($url, $form_params);

            $response->throw();

            if (!$response->ok()) {
                return [
                    'success' => false,
                    'message' => 'Error en el envío del SMS',
                    'time' => microtime(true) - $start
                ];
            } 
            return [
                'success' => true,
                'message' => 'SMS enviado correctamente.',
                'time' => microtime(true) - $start
            ];
        } catch (ConnectionException $e) {
            return [
                'success' => false,
                'message'=> $e->getMessage(),
            ];
        } catch (RequestException|ErrorException|\Throwable $e) {
            return [
                'success' => false,
                'message'=> $e->getMessage(),
            ];
        }
    }
}
