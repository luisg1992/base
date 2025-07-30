<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Referencia extends Model
{
    use HasFactory;

    protected $table = 'Referencias'; // Nombre de la tabla
    protected $primaryKey = 'IdReferencia'; // Definir la clave primaria
    public $timestamps = false;

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'IdReferenciaRefCon',
        'IdPaciente',
        'CodigoEstado',
        'EstadoReferencia',
        'NumeroReferencia',
        'FechaEnvio',
        'UpsOrigen',
        'DescUpsOrigen',
        'UpsDestino',
        'DescUpsDestino',
        'CodigoEstablecimientoOrigen',
        'EstablecimientoOrigen',
        'TipoDocumento',
        'NumeroDocumento',
        'CodigoEspecialidad',
        'Especialidad'
    ];

    // Definir las columnas de fechas que deben ser tratadas como instancias de Carbon
    protected $dates = [
        'FechaEnvio',
    ];

    public static function createOrUpdate(array $dataReferencia): ?string
    {
        // Buscar si ya existe una referencia con el campo IdReferenciaRefCon
        $referencia = self::where('IdReferenciaRefCon', $dataReferencia['idReferencia'])->first();
        if ($referencia) {
            // Actualizar los datos si la referencia existe
            $referencia->fill([
                'IdReferenciaRefCon' => $dataReferencia['idReferencia'],
                'IdPaciente' => $dataReferencia['IdPaciente'],
                'CodigoEstado' => $dataReferencia['codigoEstado'],
                'EstadoReferencia' => $dataReferencia['estado'],
                'NumeroReferencia' => $dataReferencia['numeroReferencia'],
                'FechaEnvio' => $dataReferencia['fechaEnvio'],
                'UpsOrigen' => $dataReferencia['upsOrigen'],
                'DescUpsOrigen' => $dataReferencia['descUpsOrigen'],
                'UpsDestino' => $dataReferencia['upsDestino'],
                'DescUpsDestino' => $dataReferencia['descUpsDestino'],
                'codigoEstablecimientoOrigen' => $dataReferencia['codigoEstablecimientoOrigen'] ?? $dataReferencia['codigoestablecimientoOrigen'],
                'EstablecimientoOrigen' => $dataReferencia['establecimientoOrigen'],
                'TipoDocumento' => $dataReferencia['tipoDocumento'],
                'NumeroDocumento' => $dataReferencia['numeroDocumento'],
                'CodigoEspecialidad' => $dataReferencia['codigoEspecialidad'],
                'Especialidad' => $dataReferencia['especialidad'],
            ]);

            $referencia->save();
        } else {
            // Crear una nueva referencia si no existe
            $referencia = self::create([
                'IdReferenciaRefCon' => $dataReferencia['idReferencia'],
                'IdPaciente' => $dataReferencia['IdPaciente'],
                'CodigoEstado' => $dataReferencia['codigoEstado'],
                'EstadoReferencia' => $dataReferencia['estado'],
                'NumeroReferencia' => $dataReferencia['numeroReferencia'],
                'FechaEnvio' => $dataReferencia['fechaEnvio'],
                'UpsOrigen' => $dataReferencia['upsOrigen'],
                'DescUpsOrigen' => $dataReferencia['descUpsOrigen'],
                'UpsDestino' => $dataReferencia['upsDestino'],
                'DescUpsDestino' => $dataReferencia['descUpsDestino'],
                'codigoEstablecimientoOrigen' => $dataReferencia['codigoEstablecimientoOrigen'] ?? $dataReferencia['codigoestablecimientoOrigen'],
                'EstablecimientoOrigen' => $dataReferencia['establecimientoOrigen'],
                'TipoDocumento' => $dataReferencia['tipoDocumento'],
                'NumeroDocumento' => $dataReferencia['numeroDocumento'],
                'CodigoEspecialidad' => $dataReferencia['codigoEspecialidad'],
                'Especialidad' => $dataReferencia['especialidad']
            ]);
        }

        // Retornar únicamente el campo IdReferenciaRefCon
        return $referencia->IdReferencia;
    }
}
