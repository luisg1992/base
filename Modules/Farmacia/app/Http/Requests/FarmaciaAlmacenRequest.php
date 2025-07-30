<?php

namespace Modules\Farmacia\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FarmaciaAlmacenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->get('id');
        return [
            'descripcion' => [
                'required',
                'min:4',
            ],
            'idTipoSuministro' => [
                'required',
            ]
        ];
    }
}
