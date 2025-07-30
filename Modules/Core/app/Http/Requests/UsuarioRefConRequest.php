<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioRefConRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'Nombres' => [
                'required',
                'string',
                'min:4',
                'max:255'
            ],
            'Usuario' => [
                'required',
                'string',
                'min:4',
                'max:255'
            ],
            'Clave' => [
                'required',
                'string',
                'min:4',
                'max:255'
            ],
            'Estado' => [
                'required',
                'int',
            ]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
