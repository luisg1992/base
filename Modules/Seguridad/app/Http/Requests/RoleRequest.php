<?php

namespace Modules\Seguridad\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {  
        return [
            'name' => [
                'required',
                'min:4'
            ],
        ];
    }
}
