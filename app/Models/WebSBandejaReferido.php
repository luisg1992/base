<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebSBandejaReferido extends Model
{
    protected $table = 'WebS_ReferenciasBandejaRecibidos';
    protected $primaryKey = 'Id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'TipoDocumento',
        'NumeroDocumento',
        'Nombres',
        'PrimerApellido',
        'SegundoApellido',
        'Sexo',
        'Direccion',
        'Ubigeo',
        'Celular',
        'Especialidad',
        'Condicion',
        'FechaReferencia',
        'HoraReferencia',
        'TipoTransporte',
        'ServicioOrigen',
        'CodigoEstablecimientoOrigen',
        'EstablecimientoOrigen',
        'ServicioDestino',
        'NumeroReferencia',
        'IdReferencia',
        'FechaEnvio',
        'ResumeAnamnesis',
        'ResumeExfisico',
        'MotivoReferencia',
        'FechaAceptacion',
        'Trama'
    ];

    protected $casts = [
        'Id' => 'int',
        'FechaReferencia' => 'date',
        'HoraReferencia' => 'datetime:H:i:s',
        'FechaEnvio' => 'date',
        'FechaAceptacion' => 'date',
        'Trama' => 'array'
    ];
}
