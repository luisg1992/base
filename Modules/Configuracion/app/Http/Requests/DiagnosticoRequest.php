<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiagnosticoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'EsActivo' => [
                'required',
                'int',
            ],

            'Descripcion' => [
                'required',
                'string',
                'min:5',
                'max:50'
            ],
            'DescripcionMINSA' => [
                'required',
                'string',
                'min:5',
                'max:50'
            ],
            'IdCapitulo' => [
                'required',
                'int'
            ],
            'IdGrupo' => [
                'required',
                'int'
            ],
            'IdCategoria' => [
                'required',
                'int'
            ],
            'CodigoExportacion' => [
                'required',
                'string',
                'min:1',
                'max:5'

            ],
            'CodigoCIE9' => [
                'required',
                'string',
                'min:1',
                'max:5'


            ],
            'CodigoCIE10' => [
                'required',
                'string',
                'min:1',
                'max:7'
            ],

            'EdadMaxDias' => [
                'required',
                'int'
            ],
            'EdadMinDias' => [
                'required',
                'int'
            ],
            'IdTipoSexo' => [
                'required',
                'int'
            ],

            'codigoCIEsinPto' => 'nullable|string',
            'CodigoCIE10' => 'nullable|string',
            'Restriccion' => 'nullable|boolean',
            'Gestacion' => 'nullable|boolean',
            'Morbilidad' => 'nullable|boolean',
            'Intrahospitalario' => 'nullable|boolean',
            'FechaInicioVigencia' => 'nullable|date',
        ];
    }
}
