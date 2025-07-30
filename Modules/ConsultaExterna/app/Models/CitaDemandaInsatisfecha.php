<?php

namespace Modules\ConsultaExterna\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Modules\Persona\Models\Paciente;

class CitaDemandaInsatisfecha extends Model
{
    protected $table = 'WebS_CitasDemandaInsatisfecha';
    protected $primaryKey = 'IdDemaInsatisfecha';
    public $timestamps = false;

    protected $fillable = [
        'TipoDocumento',
        'NumeroDocumento',
        'IdEspecialidad',
        'IdMotivo',
        'FechaRegistro',
        'Estado',
        'IdEmpleadoRegistro',
        'OrigenRegistro',
        'Celular',
        'IdPaciente',
        'EnvioSMS',

        'IdCita',
        'FechaRegistroCita',
        'FechaAtencion',
    ];

    protected $casts = [
        'Fecha' => 'datetime',
        'Estado' => 'boolean',
    ];

    public static function CitaDemandaInsatisfechaStore(array $data)
    {
        $user = Auth::user();
        return self::create([
            'TipoDocumento'    => $data['TipoDocumento'],
            'NumeroDocumento'  => $data['NumeroDocumento'],
            'IdEspecialidad'   => $data['IdEspecialidad'],
            'IdMotivo'         => $data['IdMotivo'],
            'FechaRegistro'            => now(),
            'Estado'           => true,
            'IdEmpleadoRegistro'       => $user->IdEmpleado,
            'OrigenRegistro'   => $data['OrigenRegistro'],
            'Celular'   => $data['Celular'],
            'IdPaciente'   => $data['IdPaciente'],
            'EnvioSMS'   => $data['EnvioSMS'] ?? 0,

            'IdCita'   => null,
            'FechaRegistroCita'   => null,
            'FechaAtencion'   => null,
        ]);
    }
}
