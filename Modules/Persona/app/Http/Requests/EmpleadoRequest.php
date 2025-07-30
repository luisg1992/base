<?php

namespace Modules\Persona\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpleadoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = request()->input('id');
        return [
            'DNI' => [
                'required',
                'string',
                'size:8',
                Rule::unique('Empleados', 'DNI')->ignore($id, 'IdEmpleado'),
            ],
        ];
    }
}
