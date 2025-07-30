<?php

namespace Modules\Api\Http\Controllers\Sis;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SisController extends Controller
{
    public function consultarAfiliadoFuaE(Request $request)
    {          
        $url = 'http://app.sis.gob.pe/sisWSAFI/Service.asmx?WSDL';
        $dni = '44226779';
        $usuario = 'HNDM';
        $clave = 'hyF0CuAO';

        $time_start = microtime(true);
        $opcion = $request->get('opcion') ?? "1";
        $strTipoDocumento = $request->get('TipoDocumento') ?? "";
        $strNroDocumento = $request->get('NroDocumento') ?? "";

        $strDisa = $request->get('disa') ?? "";
        $strTipoFormato = $request->get('tipoFormato') ?? "";
        $strNroContrato = $request->get('nroContrato') ?? "";
        $strCorrelativo = $request->get('correlativo') ?? "";

        $webService = new \SoapClient($url);
        $result = $webService->GetSession([
            'strUsuario' => $usuario,
            'strClave' => $clave
        ]); 

        $result = $webService->ConsultarAfiliadoFuaE([
            'intOpcion' => $opcion,
            'strAutorizacion' => $result->GetSessionResult,
            'strDni' => $dni,
            'strTipoDocumento' => $strTipoDocumento,
            'strNroDocumento' => $strNroDocumento,
            'strDisa' => $strDisa,
            'strTipoFormato' => $strTipoFormato,
            'strNroContrato' => $strNroContrato,
            'strCorrelativo' => $strCorrelativo
        ]);

        $code = $result->ConsultarAfiliadoFuaEResult->IdError;
        $message = $result->ConsultarAfiliadoFuaEResult->Resultado;

        $data = [
            'IdError' => $result->ConsultarAfiliadoFuaEResult->IdError,
            'Resultado' => $result->ConsultarAfiliadoFuaEResult->Resultado,
            'TipoDocumento' => $result->ConsultarAfiliadoFuaEResult->TipoDocumento,
            'NroDocumento' => $result->ConsultarAfiliadoFuaEResult->NroDocumento,
            'ApePaterno' => $result->ConsultarAfiliadoFuaEResult->ApePaterno,
            'ApeMaterno' => $result->ConsultarAfiliadoFuaEResult->ApeMaterno,
            'Nombres' => $result->ConsultarAfiliadoFuaEResult->Nombres,
            'FecAfiliacion' => $result->ConsultarAfiliadoFuaEResult->FecAfiliacion,
            'EESS' => $result->ConsultarAfiliadoFuaEResult->EESS,
            'DescEESS' => $result->ConsultarAfiliadoFuaEResult->DescEESS,
            'EESSUbigeo' => $result->ConsultarAfiliadoFuaEResult->EESSUbigeo,
            'DescEESSUbigeo' => $result->ConsultarAfiliadoFuaEResult->DescEESSUbigeo,
            'Regimen' => $result->ConsultarAfiliadoFuaEResult->Regimen,
            'TipoSeguro' => $result->ConsultarAfiliadoFuaEResult->TipoSeguro,
            'DescTipoSeguro' => $result->ConsultarAfiliadoFuaEResult->DescTipoSeguro,
            'Contrato' => $result->ConsultarAfiliadoFuaEResult->Contrato,
            'FecCaducidad' => $result->ConsultarAfiliadoFuaEResult->FecCaducidad,
            'Estado' => $result->ConsultarAfiliadoFuaEResult->Estado,
            'Tabla' => $result->ConsultarAfiliadoFuaEResult->Tabla,
            'IdNumReg' => $result->ConsultarAfiliadoFuaEResult->IdNumReg,
            'Genero' => $result->ConsultarAfiliadoFuaEResult->Genero,
            'FecNacimiento' => $result->ConsultarAfiliadoFuaEResult->FecNacimiento,
            'IdUbigeo' => $result->ConsultarAfiliadoFuaEResult->IdUbigeo,
            'Disa' => $result->ConsultarAfiliadoFuaEResult->Disa,
            'TipoFormato' => $result->ConsultarAfiliadoFuaEResult->TipoFormato,
            'NroContrato' => $result->ConsultarAfiliadoFuaEResult->NroContrato,
            'Correlativo' => $result->ConsultarAfiliadoFuaEResult->Correlativo,
            'IdPlan' => $result->ConsultarAfiliadoFuaEResult->IdPlan,
            'IdGrupoPoblacional' => $result->ConsultarAfiliadoFuaEResult->IdGrupoPoblacional,
            'MsgConfidencial' => $result->ConsultarAfiliadoFuaEResult->MsgConfidencial,
        ];

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $success = $code === '0';

        return [
            'success' => $success,
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'time' => $time
        ];
    }

    public function consultarAfiliacionesTemporales(Request $request)
    {
        $apellidoPaterno = $request->input('apellido_paterno');
        $apellidoMaterno = $request->input('apellido_materno');
        $nombres = $request->input('$nombres');
        $sexo = $request->input('$sexo');
        $fechaNacimiento = $request->input('$fecha_nacimiento');

        return $this->consultarAfiliacionesTemporalesConParametros(
            $apellidoPaterno,
            $apellidoMaterno,
            $nombres,
            $sexo,
            $fechaNacimiento
        );
    }

    public function consultarAfiliacionesTemporalesConParametros($apellidoPaterno, $apellidoMaterno,
                                                                 $nombres, $sexo, $fechaNacimiento)
    {
        $url = 'http://app.sis.gob.pe/sisWSAFI/Service.asmx?WSDL';
        $dni = '44226779';
        $usuario = 'HNDM';
        $clave = 'hyF0CuAO';

        $webService = new \SoapClient($url);
        $result = $webService->GetSession([
            'strUsuario' => $usuario,
            'strClave' => $clave
        ]);

        return $webService->ConsultarAfiliacionesTemporales([
            'strAutorizacion' => $result->GetSessionResult,
            'strDni' => $dni,
            'strApPaterno' => $apellidoPaterno,
            'strApMaterno' => $apellidoMaterno,
            'strNombres' => $nombres,
            'strIdSexo' => $sexo,
            'strFecNac' => $fechaNacimiento,
        ]);
    }
}
