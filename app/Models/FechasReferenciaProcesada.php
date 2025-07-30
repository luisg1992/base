<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FechasReferenciaProcesada extends Model
{
    protected $table = 'FechasReferenciaProcesadas';

    protected $primaryKey = 'IdFechaReferenciaProcesada';
    public $incrementing = true;
    public $timestamps = false; 

    protected $fillable = [
        'FechaEnvio',
        'FechaRegistro',
        'Observacion',
    ];

    protected $casts = [
        'FechaEnvio' => 'date',
        'FechaRegistro' => 'datetime',
    ];
}
