<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\sigesa\administration\DentistController;
use Illuminate\Support\Facades\DB;
use ErrorException;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use App\Http\Controllers\Api\sigesa\administration\DoctorController;
use App\Http\Controllers\Api\sigesa\administration\NurseController;
use App\Http\Controllers\Api\sigesa\administration\PsychologistController;

class ApiEmpleadosController extends Controller
{ 

    public function doctor_searchByName(Request $request)
    {
        try {
            $form = [
                'apellido_paterno' => $request->appaterno,
                'apellido_materno' => $request->apmaterno,
                'nombres' => $request->nombres,
            ];

            $api = new DoctorController();
            $responseName = $api->searchByName(request()->merge($form));
            if ($responseName["success"]) {
                $form = ["cmp" => $responseName['data'][0]['cmp']];
                $responseCmp = $this->doctor_searchByCmp(request()->merge($form));
                if ($responseCmp["success"]) {
                    $res = $responseCmp['data'];
                }
            }
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'codRespuesta' => -1,
                'try' => 'ConnectionException',
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'codRespuesta' => -1,
                'try' => 'RequestException',
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'codRespuesta' => -1,
                'try' => 'ErrorException',
                'message' => $ee->getMessage(),
            ];
        }
        return $res;
    }

    public function doctor_searchByCmp(Request $request)
    {
        try {
            $form = [
                'cmp' => $request->cmp,
            ];

            $api = new DoctorController();
            $response = $api->searchByCmp(request()->merge($form));
            if ($response["success"]) {
                $res = $response;
            }
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ConnectionException',
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'RequestException',
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ErrorException',
                'message' => $ee->getMessage(),
            ];
        }
        return $res;
    }

    public function enfermera_search(Request $request)
    {
        try {
            $form = [
                'nombre' => $request->appaterno . " " . $request->apmaterno . " " . $request->nombres,
            ];

            $api = new NurseController();
            $responseName = $api->search(request()->merge($form));
            if ($responseName['success']) {
                $form = ["cep" => $responseName['data'][0]['cep']];
                $responseCep = $this->enfermera_searchByCep(request()->merge($form));
                if ($responseCep["success"]) {
                    $res = $responseCep['data'];
                }
            } else {
                $res = [
                    'success' => false,
                    'codRespuesta' => -1,
                    'message' => $responseName['message'],
                ];
            }
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ConnectionException',
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'RequestException',
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ErrorException',
                'message' => $ee->getMessage(),
            ];
        }
        return $res;
    }

    public function enfermera_searchByCep(Request $request)
    {
        try {
            $form = [
                'cep' => $request->cep,
            ];
            $api = new NurseController();
            $response = $api->searchByCep(request()->merge($form));
            if ($response['success']) {
                $res = $response;
            }
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ConnectionException',
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'RequestException',
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ErrorException',
                'message' => $ee->getMessage(),
            ];
        }
        return $res;
    }

    public function odontologo_search(Request $request)
    {
        $url = '';
        $res = null;
        try {
            $form = [
                'apellidos' => $request->appaterno . " " . $request->apmaterno,
                'nombres' => $request->nombres,
            ];

            $api = new DentistController();
            $responseName = $api->searchByName(request()->merge($form));
            if ($responseName["success"]) {
                $res = $responseName['data'][0];
            } else {
                $res = [
                    'success' => false,
                    'codRespuesta' => -1,
                    'message' => $responseName['message'],
                ];
            }
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ConnectionException',
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'RequestException',
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ErrorException',
                'message' => $ee->getMessage(),
            ];
        }
        return $res;
    }

    public function odontologo_searchByCep(Request $request)
    {
        $url = '';
        try {
            $form = [
                'cop' => $request->cop,
            ];

            $api = new DentistController();
            $response = $api->searchByCop(request()->merge($form));
            if ($response["success"]) {
                $res = $response;
            }
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ConnectionException',
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'RequestException',
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ErrorException',
                'message' => $ee->getMessage(),
            ];
        }
        return $res;
    }

    public function psicologo_search(Request $request)
    {
        $url = '';
        try {
            $form = [
                'nombres' => $request->appaterno . " " . $request->apmaterno . " " . $request->nombres,
            ];

            $api = new PsychologistController();
            $responseName = $api->searchByName(request()->merge($form));
            if ($responseName["success"]) {
                $res = $responseName['data'][0];
            } else {
                $res = [
                    'success' => false,
                    'codRespuesta' => -1,
                    'message' => $responseName['message'],
                ];
            }
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ConnectionException',
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'RequestException',
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ErrorException',
                'message' => $ee->getMessage(),
            ];
        }
        return $res;
    }

    public function psicologo_searchByCep(Request $request)
    {
        $url = '';
        try {
            $form = [
                'cpsp' => $request->cpsp,
            ];

            $api = new PsychologistController();
            $response = $api->searchByCpsp(request()->merge($form));
            if ($response["success"]) {
                $res = $response;
            }
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ConnectionException',
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'RequestException',
                'message' => $re->getMessage(),
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'codRespuesta' => -2,
                'try' => 'ErrorException',
                'message' => $ee->getMessage(),
            ];
        }
        return $res;
    }
}
