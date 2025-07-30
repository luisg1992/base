<?php

namespace App\Http\Controllers\Api\sigesa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SoapClient;
use Exception;

class SisController extends Controller
{
    public function send(Request $request)
    {
        /*PARA CONSULTA SIS BASADO A NUMERO DE DOCUMENTO*/
        $params = [
            'tipo' => $request->input('tipo'),
            'numero' => $request->input('numero'),

            'intOpcion' => 1,
            'strDisa' => "",
            'strTipoFormato' => "",
            'strNroContrato' => "",
            'strCorrelativo' => "",

            'url' => config('services.sigesa_sis.strURL'),
            'user' => config('services.sigesa_sis.strUsuario'),
            'clave' => config('services.sigesa_sis.strClave'),
            'dni' => config('services.sigesa_sis.strDni'),
        ];
        return $this->getData($params);
        /*PARA CONSULTA SIS BASADO A NUMERO DE DOCUMENTO*/
    }

    private function getData(array $params)
    {
        $timeout = 3;
        $time_start = microtime(true);
        $contextOptions = ['http' => ['timeout' => $timeout]];
        $context = stream_context_create($contextOptions);
        $options = ['stream_context' => $context];

        try {
            $webService = new SoapClient($params['url'], $options);
            $sessionResult = $webService->GetSession([
                'strUsuario' => $params['user'],
                'strClave' => $params['clave']
            ])->GetSessionResult;

            $result = $webService->ConsultarAfiliadoFuaE([
                'intOpcion' => $params['intOpcion'],
                'strAutorizacion' => $sessionResult,
                'strDni' => $params['dni'],
                'strTipoDocumento' => $params['tipo'],
                'strNroDocumento' => $params['numero'],
                'strDisa' => $params['strDisa'],
                'strTipoFormato' => $params['strTipoFormato'],
                'strNroContrato' => $params['strNroContrato'],
                'strCorrelativo' => $params['strCorrelativo'],
            ]);

            $resultData = $result->ConsultarAfiliadoFuaEResult;

            $data = [
                'IdError' => $resultData->IdError,
                'Resultado' => $resultData->Resultado,
                'TipoDocumento' => $resultData->TipoDocumento,
                'NroDocumento' => $resultData->NroDocumento,
                'ApePaterno' => $resultData->ApePaterno,
                'ApeMaterno' => $resultData->ApeMaterno,
                'Nombres' => $resultData->Nombres,
                'FecAfiliacion' => $resultData->FecAfiliacion,
                'EESS' => $resultData->EESS,
                'DescEESS' => $resultData->DescEESS,
                'EESSUbigeo' => $resultData->EESSUbigeo,
                'DescEESSUbigeo' => $resultData->DescEESSUbigeo,
                'Regimen' => $resultData->Regimen,
                'TipoSeguro' => $resultData->TipoSeguro,
                'DescTipoSeguro' => $resultData->DescTipoSeguro,
                'Contrato' => $resultData->Contrato,
                'FecCaducidad' => $resultData->FecCaducidad,
                'Estado' => $resultData->Estado,
                'Tabla' => $resultData->Tabla,
                'IdNumReg' => $resultData->IdNumReg,
                'Genero' => $resultData->Genero,
                'FecNacimiento' => $resultData->FecNacimiento,
                'IdUbigeo' => $resultData->IdUbigeo,
                'Disa' => $resultData->Disa,
                'TipoFormato' => $resultData->TipoFormato,
                'NroContrato' => $resultData->NroContrato,
                'Correlativo' => $resultData->Correlativo,
                'IdPlan' => $resultData->IdPlan,
                'IdGrupoPoblacional' => $resultData->IdGrupoPoblacional,
                'MsgConfidencial' => $resultData->MsgConfidencial,
            ];

            return [
                'success' => $resultData->IdError === '0',
                'code' => $resultData->IdError,
                'message' => $resultData->Resultado,
                'data' => $data,
                'time' => microtime(true) - $time_start
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'code' => '500',
                'message' => $e->getMessage(),
                'data' => [],
                'time' => microtime(true) - $time_start
            ];
        }
    }
}
