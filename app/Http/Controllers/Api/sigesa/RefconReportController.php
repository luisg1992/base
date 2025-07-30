<?php

namespace App\Http\Controllers\Api\sigesa;

use Exception;
use ErrorException; 
use Illuminate\Http\Request;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class RefconReportController extends Controller
{
    public function login($data)
    {
        $res = [];
        try {
            if (key_exists('refconUsuario', $data) && key_exists('refconPassword', $data)) {
                $username = $data['refconUsuario'];
                $password = $data['refconPassword'];
            } else {
                $username = config('services.sigesa_refcon.userweb');
                $password = config('services.sigesa_refcon.passwordweb');
            }

            $login_params = [
                'C' => 'LOGIN',
                'S' => 'INIT',
                '_dcp' => '',
                'mlkuser' => $username,
                'mlkpass' => $password,
            ];

            $login_form = Http::asForm()->post('https://refcon.minsa.gob.pe/refconv02/desktop', $login_params);

            if ($login_form->ok()) {
                $cookieJar = new CookieJar();
                $cookies = $login_form->cookies();
                if ($cookies->count() > 0) {
                    foreach ($cookies as $cookie) {
                        $cookieJar->setCookie($cookie);
                    }
                    $res = [
                        'success' => true,
                        'cookies' => $cookieJar
                    ];
                } else {
                    throw new Exception('No fue posible iniciar sesión');
                }
            }
            $login_form->throw();
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'code' => $re->getCode(),
                'message' => $re->getMessage(),
            ];
        } catch (Exception $ee) {
            $res = [
                'success' => false,
                'code' => $ee->getCode(),
                'message' => $ee->getMessage(),
            ];
        }

        return $res;
    }

    public function validationUser(Request $request)
    {
        $res_login = $this->login($request->all());
        if ($res_login['success']) {
            return [
                'success' => true,
                'message' => 'Credenciales correctas'
            ];
        }
        return $res_login;
    } 

    public function  str_to_lower_utf8($text)
    {
        if (is_null($text)) {
            return null;
        }
        return mb_strtolower($text, 'utf-8');
    }
    public function preview(Request $request)
    {
        $res_login = $this->login($request->all());
        if ($res_login['success']) {
            $res = [];
            try {
                $idestablecimiento = $request->input('idestablecimiento');
                $idreferencia = $request->input('idreferencia');
                $estadoreferencia = $this->str_to_lower_utf8($request->input('estadoreferencia'));

                if ($estadoreferencia === 'rechazado') {
                    $reportname  = '../his/reports/referencia-rechazado';
                } elseif ($estadoreferencia === 'pendiente') {
                    $reportname  = '../his/reports/referencia-pendiente';
                } else {
                    $reportname  = '../his/reports/referencia';
                }

                $form_params = [
                    'outputtype' => 'PDF',
                    'reportname' => $reportname,
                    'paramstoreport' => '{"idreferencia":' . $idreferencia . ',"idestablecimiento":' . $idestablecimiento . '}',
                ];
                $response = Http::withOptions([
                    'cookies' => $res_login['cookies'],
                ])
                    ->asForm()
                    ->post('https://refcon.minsa.gob.pe/refconv02/reports', $form_params);

                if ($response->ok()) {
                    $header = json_decode($response->header('X-JSON'), true);
                    $urlFile = $header['urlFile'];
                    $url_file_array = explode('-', $urlFile);
                    if (count($url_file_array) > 2 && $url_file_array[count($url_file_array) - 2] != '0') {
                        return  [
                            'success' => true,
                            'urlFile' => $urlFile
                        ];
                    }
                    return  [
                        'success' => false,
                        'message' => 'No fue posible obtener la url del reporte'
                    ];
                }
                $response->throw();
            } catch (ConnectionException $e) {
                $res = [
                    'success' => false,
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ];
            } catch (RequestException $re) {
                $res = [
                    'success' => false,
                    'code' => $re->getCode(),
                    'message' => $re->getMessage(),
                ];
            } catch (ErrorException $ee) {
                $res = [
                    'success' => false,
                    'code' => $ee->getCode(),
                    'message' => $ee->getMessage(),
                ];
            }
            return $res;
        }
        return $res_login;
    }
}
