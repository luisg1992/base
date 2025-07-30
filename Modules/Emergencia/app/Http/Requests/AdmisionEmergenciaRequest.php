<?php

namespace Modules\Emergencia\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdmisionEmergenciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'IdPaciente' => ['required', 'integer'],
        ];
    }
}
