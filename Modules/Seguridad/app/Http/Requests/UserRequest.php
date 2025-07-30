<?php

namespace Modules\Seguridad\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
                'min:4',
            ],
            'email' => [
                'required', 
                'min:4' 
            ],
        ];
    }
}
