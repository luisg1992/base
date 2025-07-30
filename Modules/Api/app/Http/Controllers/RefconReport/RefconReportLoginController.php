<?php

namespace Modules\Api\Http\Controllers\RefconReport;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class RefconReportLoginController extends Controller
{
    public function login(): array
    {
        if (Cache::has('refcon_cookie')) {
            return [
                'success' => true,
                'cookies' => Cache::get('refcon_cookie'),
            ];
        }

        try {
            $usuario = '43623262';
            $clave = 'S0p0rt3@2025**';
            $url = 'https://refcon.minsa.gob.pe/refconv02/desktop';

            $parametros = [
                'C' => 'LOGIN',
                'S' => 'INIT',
                '_dcp' => '',
                'mlkuser' => $usuario,
                'mlkpass' => $clave,
            ];

            $response = Http::asForm()
                ->withoutRedirecting()
                ->connectTimeout(2)
                ->timeout(5)
                ->post($url, $parametros);

            if ($response->successful()) {
                $cookies = $response->cookies();

                if ($cookies->count() > 0) {
                    $cookieArray = [];

                    foreach ($cookies as $cookie) {
                        $cookieArray[] = [
                            'Name'     => $cookie->getName(),
                            'Value'    => $cookie->getValue(),
                            'Domain'   => $cookie->getDomain(),
                            'Path'     => $cookie->getPath(),
                            'Secure'   => $cookie->getSecure(),
                            'HttpOnly' => $cookie->getHttpOnly(),
                            'Expires'  => $cookie->getExpires(),
                        ];
                    }

                    Cache::put('refcon_cookie', $cookieArray, now()->addMinutes(30));

                    //                    $cookieJar = new CookieJar();
                    //                    foreach ($cookies as $cookie) {
                    //                        $cookieJar->setCookie($cookie);
                    //                    }

                    return [
                        'success' => true,
                        'cookies' => $cookieArray,
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'No se recibieron cookies de sesión.',
                    ];
                }
            }

            return [
                'success' => false,
                'code' => $response->status(),
                'message' => 'Error en la solicitud de inicio de sesión.',
            ];
        } catch (ConnectionException | RequestException | Throwable $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => 'Excepción: ' . $e->getMessage(),
            ];
        }
    }

    public function validationUser(): array
    {
        $resLogin = $this->login();

        if (!empty($resLogin['success'])) {
            return [
                'success' => true,
                'message' => 'Credenciales correctas',
            ];
        }

        return $resLogin;
    }
}
