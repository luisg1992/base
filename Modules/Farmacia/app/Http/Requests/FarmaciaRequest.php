<?php

namespace Modules\Farmacia\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FarmaciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->get('id');
        return [
            'nombre' => [
                'required',
                'min:4',
            ]
        ];
    }
}
