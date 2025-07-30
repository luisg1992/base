<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServicioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = request()->IdServicio;

        return [
            'IdDepartamento' => ['required'],
            'IdEspecialidad' => ['required'],
//            'Codigo' => ['required', 'max:6', Rule::unique('Servicios', 'Codigo')->ignore($id, 'IdServicio')],
            'Nombre' => ['required', 'string', 'max:50'],
            'codigoServicioHIS' => ['required_if:IdTipoServicio,1'],
            'IdProducto' => ['required_unless:IdTipoServicio,1'],
        ];
    }
}
