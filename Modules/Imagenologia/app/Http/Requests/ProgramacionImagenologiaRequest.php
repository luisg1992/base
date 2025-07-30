<?php

namespace Modules\Imagenologia\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgramacionImagenologiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'IdPuntoCarga' => 'required|integer',
            'Cupos' => 'required|integer',
            'Fecha' => 'required|date',
            'FechaReg' => 'required|date'
        ];
    }
}
