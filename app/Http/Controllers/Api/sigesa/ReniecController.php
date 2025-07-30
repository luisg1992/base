<?php

namespace App\Http\Controllers\Api\sigesa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReniecController extends Controller
{
    public function send(Request $request)
    {
        $number = $request->input('dni');
        $advanced = $request->input('avanzado');

        $app = config('services.sigesa_reniec.app');
        $user = config('services.sigesa_reniec.usuario');
        $clave = config('services.sigesa_reniec.clave');
        $url = config('services.sigesa_reniec.url');
        $query_basic =  config('services.sigesa_reniec.result_basico');
        $query_advanced = config('services.sigesa_reniec.result_completo');

        if ($advanced) {
            $res = $this->getDataAdvanced($app, $user, $clave, $url, $query_advanced, $number);
        } else {
            $res = $this->getDataBasic($app, $user, $clave, $url, $query_basic, $number);
        }

        return $res;
    }

    private function getDataAdvanced($app, $user, $clave, $url, $query, $number)
    {
        $time_start = microtime(true);
        $xmlRequest = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
        <soapenv:Header>
            <tem:Credencialmq>
                <tem:app>$app</tem:app>
                <tem:usuario>$user</tem:usuario>
                <tem:clave>$clave</tem:clave>
            </tem:Credencialmq>
        </soapenv:Header>
        <soapenv:Body>
            <tem:obtenerDatosCompletos>
                <tem:nrodoc>$number</tem:nrodoc>
            </tem:obtenerDatosCompletos>
        </soapenv:Body>
        </soapenv:Envelope>
        XML;

        $options = [
            'location' => $url,
            'uri' => 'http://tempuri.org/',
            'trace' => 1,
        ];

        $soapClient = new \SoapClient(null, $options);
        try {
            $data_reniec = $soapClient->__doRequest($xmlRequest, $url, $query, SOAP_1_1);
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($data_reniec);
            if ($xml === false) {
                $errors = [];
                foreach (libxml_get_errors() as $error) {
                    $error[] = $error->message;
                }
                libxml_clear_errors();
                $res = [
                    'success' => false,
                    'code' => 0,
                    'message' => implode(', ', $errors)
                ];
            } else {
                $response = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body
                    ->children('http://tempuri.org/')->obtenerDatosCompletosResponse->obtenerDatosCompletosResult;

                if ($response) {
                    $r = $response->string;
                    $code = (string)$r[0];
                    if ($code === '0000') {
                        $data = [
                            'CodigoRespuesta' => (string)$r[0],
                            'MensajeRespuesta' => (string)$r[1],
                            'NumerodeDNI' => (string)$r[2],
                            'DigitoVerificacion' => (string)$r[3],
                            'ApellidoPaterno' => (string)$r[4],
                            'ApellidoMaterno' => (string)$r[5],
                            'ApellidoDeCasada' => (string)$r[6],
                            'Nombres' => (string)$r[7],
                            'UbigeoContinDomicilio' => (string)$r[8],
                            'UbigeoPaisDomicilio' => (string)$r[9],
                            'UbigeoDepartDomicilio' => (string)$r[10],
                            'UbigeoProvinDomicilio' => (string)$r[11],
                            'UbigeoDistritDomicilio' => (string)$r[12],
                            'UbigeoLocaliDomicilio' => (string)$r[13],
                            'ContinenteDomicilio' => (string)$r[14],
                            'PaisDomicilio' => (string)$r[15],
                            'DepartDomicilio' => (string)$r[16],
                            'ProvinDomicilio' => (string)$r[17],
                            'DistritoDomicilio' => (string)$r[18],
                            'LocalidaDomicilio' => (string)$r[19],
                            'EstadoCivil' => (string)$r[20],
                            'CodigodGradoInstruccion' => (string)$r[21],
                            'Sexo' => (string)$r[22],
                            'UbigeoDepartNacimiento' => (string)$r[23],
                            'UbigeoProvinNacimiento' => (string)$r[24],
                            'UbigeoDistritNacimiento' => (string)$r[25],
                            'DepartamentoNacimiento' => (string)$r[26],
                            'ProvinciaNacimiento' => (string)$r[27],
                            'DistritoNacimiento' => (string)$r[28],
                            'FechaNacimiento' => (string)$r[29],
                            'NombrePadre' => (string)$r[30],
                            'NombreMadre' => (string)$r[31],
                            'FechaInscripcion' => (string)$r[32],
                            'FechaExpedicion' => (string)$r[33],
                            'Restricciones' => (string)$r[34],
                            'Prefijodireccion' => (string)$r[35],
                            'Direccion' => (string)$r[36],
                            'NumeroDireccion' => (string)$r[37],
                            'BlockChalet' => (string)$r[38],
                            'PrefijoBlockChalet' => (string)$r[39],
                            'Interior' => (string)$r[40],
                            'Urbanizacion' => (string)$r[41],
                            'Etapa' => (string)$r[42],
                            'Manzana' => (string)$r[43],
                            'Lote' => (string)$r[44],
                            'PrefijoDptoPisoInterior' => (string)$r[45],
                            'PrefijoUrbCondResid' => (string)$r[46],
                            'ImagenFoto' => (string)$r[47],
                            'ImagenFirma' => (string)$r[48],
                        ];

                        $res = [
                            'success' => true,
                            'code' => $code,
                            'message' => 'Consulta satisfactoria',
                            'data' => $data,
                        ];
                    } else {
                        $res = [
                            'success' => false,
                            'code' => $code,
                            'message' => 'Error código: ' . $code,
                        ];
                    }
                } else {
                    $res = [
                        'success' => false,
                        'code' => '-',
                        'message' => 'Elemento obtenerDatosBasicosResult no encontrado en el XML.',
                    ];
                }
            }
        } catch (\SoapFault $sf) {
            $res = [
                'success' => false,
                'code' => $sf->getCode(),
                'message' => $sf->getMessage(),
            ];
        } catch (\Exception $e) {
            $res = [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
        $time_end = microtime(true);
        $res['time'] = $time_end - $time_start;

        return $res;
    }

    private function getDataBasic($app, $user, $clave, $url, $query, $number)
    {
        $xmlRequest = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
        <soapenv:Header>
            <tem:Credencialmq>
                <tem:app>$app</tem:app>
                <tem:usuario>$user</tem:usuario>
                <tem:clave>$clave</tem:clave>
            </tem:Credencialmq>
        </soapenv:Header>
        <soapenv:Body>
            <tem:obtenerDatosBasicos>
                <tem:nrodoc>$number</tem:nrodoc>
            </tem:obtenerDatosBasicos>
        </soapenv:Body>
        </soapenv:Envelope>
        XML;

        $options = [
            'location' => $url,
            'uri' => 'http://tempuri.org/',
            'trace' => 1,
        ];

        $time_start = microtime(true);
        $soapClient = new \SoapClient(null, $options);
        try {
            $data_reniec = $soapClient->__doRequest($xmlRequest, $url, $query, SOAP_1_1);
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($data_reniec);
            if ($xml === false) {
                $errors = [];
                foreach (libxml_get_errors() as $error) {
                    $error[] = $error->message;
                }
                libxml_clear_errors();
                $res = [
                    'success' => false,
                    'code' => 0,
                    'message' => implode(', ', $errors)
                ];
            } else {
                $response = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body
                    ->children('http://tempuri.org/')->obtenerDatosBasicosResponse->obtenerDatosBasicosResult;

                if ($response) {
                    $r = $response->string;
                    $code = (string)$r[0];
                    if ($code === '0000') {
                        $data = [
                            'CodigoRespuesta' => (string)$r[0],
                            'MensajeRespuesta' => (string)$r[1],
                            'ApellidoPaterno' => (string)$r[2],
                            'ApellidoMaterno' => (string)$r[3],
                            'ApellidoDeCasada' => (string)$r[4],
                            'Nombres' => (string)$r[5],
                            'UbigeoContinDomicilio' => (string)$r[6],
                            'UbigeoPaisDomicilio' => (string)$r[7],
                            'UbigeoDepartDomicilio' => (string)$r[8],
                            'UbigeoProvinDomicilio' => (string)$r[9],
                            'UbigeoDistritDomicilio' => (string)$r[10],
                            'UbigeoDistritoLocalidad' => (string)$r[11],
                            'ContinenteDomicilio' => (string)$r[12],
                            'PaisDomicilio' => (string)$r[13],
                            'DepartDomicilio' => (string)$r[14],
                            'ProvinDomicilio' => (string)$r[15],
                            'DistritoDomicilio' => (string)$r[16],
                            'LocalidaDomicilio' => (string)$r[17],
                            'Direccion' => (string)$r[18],
                            'Sexo' => (string)$r[19],
                            'FechaNacimiento' => (string)$r[20],
                            'FechaExpedicion' => (string)$r[21],
                            'NumeroDocumento' => (string)$r[22],
                            'TipoDocumento' => 'DNI',
                        ];

                        $res = [
                            'success' => true,
                            'code' => $code,
                            'message' => 'Consulta satisfactoria',
                            'data' => $data,
                        ];
                    } else {
                        $res = [
                            'success' => false,
                            'code' => $code,
                            'message' => 'Error código: ' . $code,
                        ];
                    }
                } else {
                    $res = [
                        'success' => false,
                        'code' => '-',
                        'message' => 'Elemento obtenerDatosBasicosResult no encontrado en el XML.',
                    ];
                }
            }
        } catch (\SoapFault $sf) {
            $res = [
                'success' => false,
                'code' => $sf->getCode(),
                'message' => $sf->getMessage(),
            ];
        } catch (\Exception $e) {
            $res = [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }

        $time_end = microtime(true);
        $res['time'] = $time_end - $time_start;

        return $res;
    }
}
