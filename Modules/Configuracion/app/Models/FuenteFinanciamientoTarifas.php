<?php

namespace Modules\Configuracion\Models;


use Illuminate\Database\Eloquent\Model;

class FuenteFinanciamientoTarifas extends Model
{
    protected $table = 'FuentesFinanciamientoTarifas';

    public $incrementing = false; // Porque es una clave compuesta
    public $timestamps = false;
    protected $primaryKey = null; // Laravel no soporta claves compuestas directamente

    protected $fillable = [
        'idFuenteFinanciamiento',
        'idTipoFinanciamiento',
    ];

    protected $casts = [
        'idFuenteFinanciamiento' => 'int',
        'idTipoFinanciamiento' => 'int',
    ];
}