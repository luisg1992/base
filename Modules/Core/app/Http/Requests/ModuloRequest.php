<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Etiqueta' => [
                'required',
                'string',
                'max:100'
            ],
            'Subtitulo' => 'required|string|max:100',
            'Descripcion' => 'nullable|string',
            'Icono' => 'nullable|string',
            'Url' => 'nullable|string',
            'EsAccesoDirecto' => 'integer',
            'EstaBloqueado' => 'integer',
            'Estado' => 'integer',
            'Orden' => 'integer',
            'ModuloPadreId' => 'nullable|exists:Modulos,ModuloId',
        ];
    }
}
