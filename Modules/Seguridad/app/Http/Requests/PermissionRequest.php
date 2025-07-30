<?php

namespace Modules\Seguridad\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->get('id');

        return [
            'name' => [
                'required',
                'min:4',
                Rule::unique('permissions')->ignore($id),
            ],
            'modulo_id' => [
                'required',
                'exists:Modulos,ModuloId',
            ],
            'descripcion' => [
                'required',
                'min:4',
            ],
            'tipo' => []
        ];
    }
}
