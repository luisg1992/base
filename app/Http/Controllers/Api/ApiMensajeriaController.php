<?php

namespace App\Http\Controllers\Api;

use ErrorException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class ApiMensajeriaController extends Controller
{
    private $ApiSigesa;
    public function __construct()
    { 
        $this->ApiSigesa = DB::select('EXEC WebS_ParametrosApiMostrarUrlToken');
    }

    // public function firebaseSendAppSMS(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:20',
    //         'body' => 'required|string|max:150',
    //         'phone' => 'required|string|min:9|max:9',
    //     ]);
    //     try {
    //         $url = $this->ApiSigesa[0]->url . '/api/firebase/send';
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'Authorization' => 'Bearer ' . $this->ApiSigesa[0]->token
    //         ])->post($url, [
    //                     'title' => $request->title,
    //                     'body' => $request->body,
    //                     'phone' => $request->phone,
    //                 ]);

    //         // Obtener los datos JSON de la respuesta
    //         $responseData = $response->json();
    //         if ($response->successful() && isset($responseData['success']) && $responseData['success'] === true) {
    //             // La notificación fue enviada exitosamente

    //             return response()->json([
    //                 'success' => true,
    //                 'data' => $responseData['data'], // Puedes devolver la información que venga en 'data'
    //             ]);
    //         } else {
    //             // Hubo un error, devolver el mensaje de error
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => $responseData['message'] ?? 'Error desconocido al enviar la notificación.',
    //             ], $response->status());
    //         }
    //     } catch (\Exception $e) {
    //         // Manejo de excepciones
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function api_sms_simple(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string',
            'nombre' => 'required|string',
            'numero_destino' => 'required|string',
            'mensaje' => 'required|string',
            'fecha_proxima_cita' => 'required',
            'envio_directo' => 'required|boolean',
        ]);
        try {
            $url = "{$this->ApiSigesa[0]->url}/api/sms";
            $response = Http::withToken($this->ApiSigesa[0]->token)
                ->post($url, $validated);

            if ($response->ok()) {
                return response()->json([
                    'data' => $response->json(),
                    'mensaje' => 'DETALLES ENCONTRADOS DE FORMA CORRECTA.',
                ]);
            }

            $response->throw();

        } catch (ConnectionException | RequestException | ErrorException $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function api_sms_simple_2($data)
    {
        try {
            $url = "{$this->ApiSigesa[0]->url}/api/sms_simple";
            $response = Http::withToken($this->ApiSigesa[0]->token)
                ->post($url, $data);

            if ($response->ok()) {
                return response()->json([
                    'data' => $response->json(),
                    'message' => 'Mensaje enviado correctamente.',
                ]);
            }

            $response->throw();

        } catch (ConnectionException | RequestException | ErrorException $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function api_mail_simple_2($data)
    {
        try {
            $url = "{$this->ApiSigesa[0]->url}/api/mail_simple";
            $response = Http::withToken($this->ApiSigesa[0]->token)
                ->post($url, $data);

            if ($response->ok()) {
                return response()->json([
                    'data' => $response->json(),
                    'message' => 'Mensaje enviado correctamente.',
                ]);
            }

            $response->throw();

        } catch (ConnectionException | RequestException | ErrorException $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    // public function api_ws(Request $request)
    // {
    //     try {
    //         $form = [
    //             "numero" => $request->numero,
    //             "nombre" => $request->nombre,
    //             "tipodocumento" => $request->tipodocumento,
    //             "numero_destino" => $request->numero_destino,
    //             "mensaje" => $request->mensaje,
    //             "fecha_proxima_cita" => $request->fecha_proxima_cita,
    //             "archivo_nombre" => $request->archivo_nombre,
    //             "archivo_base64" => $request->archivo_base64,
    //             "envio_directo" => $request->envio_directo,
    //             "id_cuenta_atencion" => '',
    //             "especialidad" => '',
    //             "medico" => '',
    //             "consultorio" => '',
    //             "tipo" => '',
    //             "usuario" => Auth::user()->name
    //         ];
    //         $url = $this->ApiSigesa[0]->url . '/api/ws';
    //         $response = Http::withToken($this->ApiSigesa[0]->Token)
    //             ->post($url, $form);
    //         if ($response->ok()) {
    //             $res = $response->json();
    //         } else {
    //             $data['message'] = $response;
    //             $data['codigo'] = -1;
    //             return ($data);
    //         }
    //         $response->throw();
    //     } catch (ConnectionException $e) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $e->getMessage(),
    //         ];
    //     } catch (RequestException $re) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $re->getMessage(),
    //         ];
    //     } catch (ErrorException $ee) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $ee->getMessage(),
    //         ];
    //     }
    //     return $res;
    // }

    // public function api_ws_mail(Request $request)
    // {
    //     try {
    //         $form = [
    //             "numero" => $request->numero,
    //             "nombre" => $request->nombre,
    //             "numero_destino" => $request->numero_destino,
    //             "correo_destino" => $request->correo_destino,
    //             "asunto" => $request->asunto,
    //             "mensaje" => $request->mensaje,
    //             "fecha_proxima_cita" => $request->fecha_proxima_cita,
    //             "archivo_nombre" => $request->archivo_nombre,
    //             "archivo_base64" => $request->archivo_base64,
    //             "envio_directo" => $request->envio_directo,
    //             "id_cuenta_atencion" => '',
    //             "especialidad" => '',
    //             "medico" => '',
    //             "consultorio" => '',
    //             "tipo" => '',
    //             "usuario" => Auth::user()->name
    //         ];

    //         $url = $this->ApiSigesa[0]->url . '/api/ws_mail';
    //         $response = Http::withToken($this->ApiSigesa[0]->Token)
    //             ->post($url, $form);
    //         if ($response->ok()) {
    //             $res = $response->json();
    //         } else {
    //             $data['message'] = $response;
    //             $data['codigo'] = -1;
    //             return ($data);
    //         }
    //         $response->throw();
    //     } catch (ConnectionException $e) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $e->getMessage(),
    //         ];
    //     } catch (RequestException $re) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $re->getMessage(),
    //         ];
    //     } catch (ErrorException $ee) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $ee->getMessage(),
    //         ];
    //     }
    //     return $res;
    // }

    // public function api_ws_sms_mail(Request $request)
    // {
    //     try {
    //         $form = [
    //             "numero" => $request->numero,
    //             "nombre" => $request->nombre,
    //             "numero_destino" => $request->numero_destino,
    //             "correo_destino" => $request->correo_destino,
    //             "asunto" => $request->asunto,
    //             "mensaje" => $request->mensaje,
    //             "mensaje_ws" => $request->has('mensaje_ws') ? $request->mensaje_ws : $request->mensaje,
    //             "fecha_proxima_cita" => $request->fecha_proxima_cita,
    //             "archivo_nombre" => $request->archivo_nombre,
    //             "archivo_base64" => $request->archivo_base64,
    //             "envio_directo" => $request->envio_directo,
    //             "id_cuenta_atencion" => $request->id_cuenta_atencion,
    //             "especialidad" => $request->especialidad,
    //             "medico" => $request->medico,
    //             "consultorio" => $request->consultorio,
    //             "tipo" => $request->tipo,
    //             "usuario" => Auth::user()->name
    //         ];

    //         $url = $this->ApiSigesa[0]->url . '/api/ws_sms_mail';
    //         $response = Http::withToken($this->ApiSigesa[0]->Token)
    //             ->post($url, $form);
    //         if ($response->ok()) {
    //             $res = $response->json();
    //         } else {
    //             $res = [
    //                 'success' => false,
    //                 'codRespuesta' => -1,
    //                 'message' => $response->json()['message'],
    //             ];
    //             return ($res);
    //         }
    //     } catch (ConnectionException $e) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $e->getMessage(),
    //         ];
    //     } catch (RequestException $re) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $re->getMessage(),
    //         ];
    //     } catch (ErrorException $ee) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $ee->getMessage(),
    //         ];
    //     }
    //     return $res;
    // }

    // public function api_ws_simple($numero, $nombre, $numero_celular, $mensaje, $envio_directo)
    // {
    //     try {
    //         $form = [
    //             "numero" => $numero,
    //             "nombre" => $nombre,
    //             "numero_destino" => $numero_celular,
    //             "mensaje" => $mensaje,
    //             "envio_directo" => $envio_directo,
    //             "usuario" => Auth::user()->name
    //         ];

    //         $url = $this->ApiSigesa[0]->url . '/api/ws';
    //         $response = Http::withToken($this->ApiSigesa[0]->Token)
    //             ->post($url, $form);
    //         if ($response->ok()) {
    //             $res = $response->json();
    //         }
    //         $response->throw();
    //     } catch (ConnectionException $e) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $e->getMessage(),
    //         ];
    //     } catch (RequestException $re) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $re->getMessage(),
    //         ];
    //     } catch (ErrorException $ee) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $ee->getMessage(),
    //         ];
    //     }
    //     return $res;
    // }


    // public function api_mail_simple($numero, $nombre, $correo_electronico, $asunto, $mensaje, $envio_directo)
    // {
    //     try {
    //         $form = [
    //             "numero" => $numero,
    //             "nombre" => $nombre,
    //             "correo_destino" => $correo_electronico,
    //             "asunto" => $asunto,
    //             "mensaje" => $mensaje,
    //             "envio_directo" => $envio_directo
    //         ];

    //         $url = $this->ApiSigesa[0]->url . '/api/mail';
    //         $response = Http::withToken($this->ApiSigesa[0]->Token)
    //             ->post($url, $form);
    //         if ($response->ok()) {
    //             $res = $response->json();
    //         }
    //         $response->throw();
    //     } catch (ConnectionException $e) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $e->getMessage(),
    //         ];
    //     } catch (RequestException $re) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $re->getMessage(),
    //         ];
    //     } catch (ErrorException $ee) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $ee->getMessage(),
    //         ];
    //     }
    //     return $res;
    // }


    // public function api_sms_consultar_credito()
    // {
    //     try {
    //         $url = $this->ApiSigesa[0]->url . '/api/sms/consultar_credito';
    //         $response = Http::withToken($this->ApiSigesa[0]->Token)
    //             ->post($url);
    //         if ($response->ok()) {
    //             $res = $response->json();
    //         }
    //         $response->throw();
    //     } catch (ConnectionException $e) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $e->getMessage(),
    //         ];
    //     } catch (RequestException $re) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $re->getMessage(),
    //         ];
    //     } catch (ErrorException $ee) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $ee->getMessage(),
    //         ];
    //     }
    //     return $res;
    // }


    // public function listar_mensajes_por_dni(Request $request)
    // {
    //     try {
    //         $form = [
    //             "dni" => $request->dni
    //         ];

    //         $url = $this->ApiSigesa[0]->url . '/api/listar_mensajes_por_dni';
    //         $response = Http::withToken($this->ApiSigesa[0]->Token)
    //             ->post($url, $form);
    //         if ($response->ok()) {
    //             $res = $response->json();
    //         }
    //         $response->throw();
    //     } catch (ConnectionException $e) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $e->getMessage(),
    //         ];
    //     } catch (RequestException $re) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $re->getMessage(),
    //         ];
    //     } catch (ErrorException $ee) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'message' => $ee->getMessage(),
    //         ];
    //     }
    //     return $res;
    // }
}
