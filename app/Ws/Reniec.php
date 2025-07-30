<?php

namespace App\Ws;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Api\Http\Controllers\Reniec\ReniecAvanzadoController;
use Modules\Core\Models\ReniecDatosCompletos;

class Reniec
{
    private $catalogoErrores = [
        5020 => 'No existe la empresa ingresada para usar el servicio',
        5021 => 'La empresa registrada no está habilitada para usar el servicio',
        5030 => 'El usuario final de consulta ingresado no es válido',
        5031 => 'No se tiene información solicitada del usuario ingresado',
        5032 => 'El DNI no puede realizar consultas por encontrarse cancelado en el RUIPN',
        5033 => 'El DNI no puede realizar consultas por encontrarse restringido en el RUIPN',
        5034 => 'El DNI no puede realizar consultas por encontrarse observado en el RUIPN',
        5036 => 'El DNI se encuentra con baja temporal en el servicio',
        5037 => 'El DNI se encuentra con baja definitiva en el servicio',
        5100 => 'Longitud de trama de consulta inválida',
        5101 => 'Error en número de coincidencias solicitadas o inicio de grupo',
        5102 => 'Coincidencias superan el límite establecido',
        5103 => 'Error en base de datos',
        5104 => 'No se encontró información de la estructura solicitada',
        5105 => 'No se encontraron los campos a mostrar para la estructura solicitada',
        5108 => 'Carácter ingresado en apellido paterno es inválido',
        5109 => 'Carácter ingresado en apellido materno es inválido',
        5110 => 'Carácter ingresado en nombres es inválido',
        5111 => 'El DNI solicitado se encuentra cancelado en el archivo magnético del RUIPN',
        5112 => 'El DNI solicitado se encuentra restringido en el archivo magnético del RUIPN',
        5113 => 'El DNI solicitado se encuentra observado en el archivo magnético del RUIPN',
        5114 => 'El DNI ingresado no es válido o el DNI ingresado es de un menor de edad',
        5200 => 'No existen los datos solicitados',
        8000 => 'Problemas de comunicación de MQ entre el MINSA y RENIEC, notificar al administrador de MQ de MINSA',
        8100 => 'Problemas con el MQ de MINSA, notificar al administrador de MQ',
        9991 => 'Error en los datos de autenticación, revisar los datos enviados',
        9992 => 'Hay restricciones en el horario de envío, consultar con el administrador del WebService',
        9993 => 'Se alcanzó la cantidad máxima de invocaciones por día, consultar con el administrador del WebService',
        9994 => 'Error en el DNI del usuario autorizador, consultar con el administrador del WebService',
        9999 => 'Error interno, notificar al administrador de servidores del MINSA',
        0000 => 'Sin ningún error',
        5002 => 'Versión inválida',
        5003 => 'Longitud de cabecera inválida',
        5004 => 'Caracteres de verificación incorrectos',
        5008 => 'Servidor no válido',
        5009 => 'Tipo de consulta inválido',
        5010 => 'Tipo de consulta no permitida',
        5011 => 'No se ha ingresado subtipo de consulta',
    ];
    private function obtenerMensajeError($codigo)
    {
        $mensaje = isset($this->catalogoErrores[$codigo]) ? $this->catalogoErrores[$codigo] : $this->catalogoErrores[9999]; // Por defecto 'Error interno, notificar al administrador de servidores del MINSA'
        return strtoupper($mensaje);
    }

    public function ws_consulta_reniecDatosCompletos(Request $request)
    {
        $parametros = [
            "dni" => $request->numerodocumento,
        ];
        $datos_reniec = (new ReniecAvanzadoController())->consultar(request()->merge($parametros));

        if (!$datos_reniec['success']) {
            $data['datos'] = [];
            $data['respuesta'] = -1;
            $data['codigo'] = -1;
            $data['mensajeReniec'] = $datos_reniec['code'] . ' =  ' . $this->obtenerMensajeError($datos_reniec['code']);
            return ($data);
        }

        $data = $datos_reniec['data'];
        function limpiar_caracteres($string)
        {
            $caracteres_extraños = [
                "\n" => " ",    // Reemplaza saltos de línea por un espacio
                "\r" => " ",    // Reemplaza retornos de carro por un espacio
                "\t" => " ",    // Reemplaza tabulaciones por un espacio
                "\"" => "",     // Elimina comillas dobles
                "'" => "",      // Elimina comillas simples
                "\\" => "",     // Elimina barras invertidas
            ];
            return strtr($string, $caracteres_extraños);
        }

        // Recorre el array original y limpia los valores excepto los base64
        $datos_limpios = [];
        foreach ($data as $clave => $valor) {
            if (!in_array($clave, ['ImagenFirma', 'ImagenFoto']) && is_string($valor)) {
                $datos_limpios[$clave] = limpiar_caracteres($valor);
            } else {
                $datos_limpios[$clave] = $valor;
            }
        }

        // Luego construyes el array final
        $datos = [
            'CodigoRespuesta' => $datos_limpios['CodigoRespuesta'],
            'MensajeRespuesta' => $datos_limpios['MensajeRespuesta'],
            'NumerodeDNI' => $datos_limpios['NumeroDeDNI'],
            'DigitoVerificacion' => $datos_limpios['DigitoVerificacion'],
            'ApellidoPaterno' => $datos_limpios['ApellidoPaterno'],
            'ApellidoMaterno' => $datos_limpios['ApellidoMaterno'],
            'ApellidoDeCasada' => $datos_limpios['ApellidoDeCasada'],
            'Nombres' => $datos_limpios['Nombres'],
            'UbigeoContinDomicilio' => $datos_limpios['UbigeoContinenteDomicilio'],
            'UbigeoPaisDomicilio' => $datos_limpios['UbigeoPaisDomicilio'],
            'UbigeoDepartDomicilio' => $datos_limpios['UbigeoDepartamentoDomicilio'],
            'UbigeoProvinDomicilio' => $datos_limpios['UbigeoProvinciaDomicilio'],
            'UbigeoDistritDomicilio' => $datos_limpios['UbigeoDistritoDomicilio'],
            'UbigeoLocaliDomicilio' => $datos_limpios['UbigeoLocalidadDomicilio'],
            'ContinenteDomicilio' => $datos_limpios['ContinenteDomicilio'],
            'PaisDomicilio' => $datos_limpios['PaisDomicilio'],
            'DepartDomicilio' => $datos_limpios['DepartamentoDomicilio'],
            'ProvinDomicilio' => $datos_limpios['ProvinciaDomicilio'],
            'DistritoDomicilio' => $datos_limpios['DistritoDomicilio'],
            'LocalidaDomicilio' => $datos_limpios['LocalidadDomicilio'],
            'EstadoCivil' => $datos_limpios['EstadoCivil'],
            'CodigodGradoInstruccion' => $datos_limpios['CodigoGradoInstruccion'],
            'Sexo' => $datos_limpios['Sexo'],
            'UbigeoDepartNacimiento' => $datos_limpios['UbigeoDepartamentoNacimiento'],
            'UbigeoProvinNacimiento' => $datos_limpios['UbigeoProvinciaNacimiento'],
            'UbigeoDistritNacimiento' => $datos_limpios['UbigeoDistritoNacimiento'],
            'DepartamentoNacimiento' => $datos_limpios['DepartamentoNacimiento'],
            'ProvinciaNacimiento' => $datos_limpios['ProvinciaNacimiento'],
            'DistritoNacimiento' => $datos_limpios['DistritoNacimiento'],
            'FechaNacimiento' => $datos_limpios['FechaNacimiento'],
            'NombrePadre' => $datos_limpios['NombrePadre'] ? $datos_limpios['NombrePadre'] : null,
            'NombreMadre' => $datos_limpios['NombreMadre'] ? $datos_limpios['NombreMadre'] : null,
            'FechaInscripcion' => $datos_limpios['FechaInscripcion'],
            'FechaExpedicion' => $datos_limpios['FechaExpedicion'],
            'Restricciones' => $datos_limpios['Restricciones'],
            'Prefijodireccion' => $datos_limpios['PrefijoDireccion'],
            'Direccion' => $datos_limpios['Direccion'] ? $datos_limpios['Direccion'] : null,
            'NumeroDireccion' => $datos_limpios['NumeroDireccion'],
            'BlockChalet' => $datos_limpios['BlockChalet'],
            'PrefijoBlockChalet' => $datos_limpios['PrefijoBlockChalet'],
            'Interior' => $datos_limpios['Interior'],
            'Urbanizacion' => $datos_limpios['Urbanizacion'],
            'Etapa' => $datos_limpios['Etapa'],
            'Manzana' => $datos_limpios['Manzana'],
            'Lote' => $datos_limpios['Lote'],
            'PrefijoDptoPisoInterior' => $datos_limpios['PrefijoDptoPisoInterior'],
            'PrefijoUrbCondResid' => $datos_limpios['PrefijoUrbCondResid'],
            'ImagenFirma' =>  null,
            'ImagenFoto' =>  null,
            'FechaConsulta' => now()
        ];
        
        // 'ImagenFirma' => $datos_limpios['ImagenFirma'] ? $datos_limpios['ImagenFirma'] : null,
        // 'ImagenFoto' => $datos_limpios['ImagenFoto'] ? $datos_limpios['ImagenFoto'] : null,

        // Buscar el registro por NumerodeDNI en ReniecDatosCompletos
        $reniecRegistro = ReniecDatosCompletos::on('sqlsrvServicios')
            ->where('NumerodeDNI', $datos['NumerodeDNI'])
            ->first();
        if ($reniecRegistro) {
            $reniecRegistro->update($datos);
        } else {
            ReniecDatosCompletos::on('sqlsrvServicios')->create($datos);
        }

        // -- 0=Paciente, 1=Empleado
        if ($request->tipopersona == 0) {
            DB::update('EXEC WebS_Replicar_Reniec_En_Pacientes @NumeroDocumento = ?', [$request->numerodocumento]);
        }


        $data['datos'] = $datos;
        $data['respuesta'] = 1;
        $data['codigo'] = $data['CodigoRespuesta'];
        $data['mensajeReniec'] = $this->obtenerMensajeError($data['CodigoRespuesta']);

        return ($data);
    }


    // public function ws_consulta_reniecDatosBasicos($numerodocumento)
    // {
    //     $datos_reniec = (new ApiServicios())->api_reniec($numerodocumento, false);
    //     if (!$datos_reniec['success']) {
    //         $data['datos'] = [];
    //         $data['respuesta'] = -1;
    //         $data['descripcion'] = 'ERROR DE CONSULTA RENIEC';
    //         $data['codigo'] = -1;
    //         return ($data);
    //     }

    //     $data = $datos_reniec['data'];

    //     $datos = [
    //         'CodigoRespuesta' => $data['CodigoRespuesta'],
    //         'ApellidoPaterno' => $data['ApellidoPaterno'],
    //         'ApellidoMaterno' => $data['ApellidoMaterno'],
    //         'ApellidoDeCasada' => $data['ApellidoDeCasada'],
    //         'Nombres' => $data['Nombres'],
    //         'UbigeoContinDomicilio' => $data['UbigeoContinDomicilio'],
    //         'UbigeoPaisDomicilio' => $data['UbigeoPaisDomicilio'],
    //         'UbigeoDepartDomicilio' => $data['UbigeoDepartDomicilio'],
    //         'UbigeoProvinDomicilio' => $data['UbigeoProvinDomicilio'],
    //         'UbigeoDistritDomicilio' => $data['UbigeoDistritDomicilio'],
    //         'UbigeoDistritoLocalidad' => $data['UbigeoDistritoLocalidad'],
    //         'ContinenteDomicilio' => $data['ContinenteDomicilio'],
    //         'PaisDomicilio' => $data['PaisDomicilio'],
    //         'DepartDomicilio' => $data['DepartDomicilio'],
    //         'ProvinDomicilio' => $data['ProvinDomicilio'],
    //         'DistritoDomicilio' => $data['DistritoDomicilio'],
    //         'LocalidaDomicilio' => $data['LocalidaDomicilio'],
    //         'Direccion' => $data['Direccion'],
    //         'Sexo' => $data['Sexo'],
    //         'FechaNacimiento' => $data['FechaNacimiento'],
    //         'FechaExpedicion' => $data['FechaExpedicion'],
    //         'NumeroDocumento' => $data['NumeroDocumento'],
    //         'TipoDocumento' => $data['TipoDocumento'],

    //         'IdError_SIS' => '',
    //         'Resultado_SIS' => '',
    //         'NroDocumento' => '',
    //         'FecAfiliacion_SIS' => '',
    //         'EESS_SIS' => '',
    //         'DescEESS_SIS' => '',
    //         'EESSUbigeo_SIS' => '',
    //         'DescEESSUbigeo_SIS' => '',
    //         'Regimen_SIS' => '',
    //         'TipoSeguro_SIS' => '',
    //         'DescTipoSeguro_SIS' => '',
    //         'Contrato_SIS' => '',
    //         'FecCaducidad_SIS' => '',
    //         'Estado_SIS' => '',
    //         'Tabla_SIS' => '',
    //         'IdNumReg_SIS' => '',
    //         'Genero_SIS' => '',
    //         'FecNacimiento_SIS' => '',
    //         'IdUbigeo_SIS' => '',
    //         'Disa_SIS' => '',
    //         'TipoFormato_SIS' => '',
    //         'NroContrato_SIS' => '',
    //         'Correlativo_SIS' => '',
    //         'IdPlan_SIS' => '',
    //         'IdGrupoPoblacional_SIS' => '',
    //         'MsgConfidencial_SIS' => '',
    //     ];

    //     $obtenerDatosCompletos = $data['CodigoRespuesta'];

    //     $data['datos'] = $datos;
    //     $data['respuesta'] = 1;
    //     $data['descripcion'] = 'CONSULTA RENIEC EXITOSA ' . $obtenerDatosCompletos;
    //     $data['codigo'] = $obtenerDatosCompletos;

    //     return ($data);
    // }
}
