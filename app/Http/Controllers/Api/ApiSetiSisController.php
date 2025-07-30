<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\{RequestException, ConnectionException};

class ApiSetiSisController extends Controller
{

    private function sendRequest(string $endpoint, array $data)
    {
        try {
            $url = 'http://172.16.10.136:8181';
            $token = '3f428f3bd69a18bd722d0d29c4764a6f69d4c746e1c73c56989522c8e0e8602b';

            $response = Http::withToken($token)
                ->timeout(20)
                ->post("{$url}/api/{$endpoint}", $data);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => $response->body(),
            ];
        } catch (ConnectionException|RequestException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function api_login()
    {
        return $this->sendRequest('setisis_login', []);
    }

    public function api_enviar_paquete($token, $filename, $base64)
    {
        return $this->sendRequest('setisis_enviar_paquete', [
            'token' => $token,
            'filename' => $filename,
            'base64' => $base64,
        ]);
    }

    public function api_consultar_paquete($token, $paquete)
    {
        return $this->sendRequest('setisis_consultar_paquete', [
            'token' => $token,
            'paquete' => $paquete,
        ]);
    }
}
