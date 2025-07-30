<?php

namespace Modules\Imagenologia\Models;

use Illuminate\Database\Eloquent\Model;

class RecetaDetalle extends Model
{
    protected $table = 'RecetaDetalle';
    protected $primaryKey = 'idReceta';
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'idReceta',
        'idItem',
        'CantidadPedida',
        'Precio',
        'Total',
        'SaldoEnRegistroReceta',
        'SaldoEnDespachoReceta',
        'CantidadDespachada',
        'idDosisRecetada',
        'idEstadoDetalle',
        'MotivoAnulacionMedico',
        'observaciones',
        'IdViaAdministracion',
        'Duracion',
        'CodigoDx',
        'IdRecetaDosisUnidadMedida',
        'IdRecetaFrecuencia' ,
        'IdDiagnostico' 
    ];

    protected $casts = [
        'idReceta' => 'int',
        'idItem' => 'int',
        'CantidadPedida' => 'int',
        'Precio' => 'decimal:2', // Money lo manejas como decimal(2)
        'Total' => 'decimal:2',
        'SaldoEnRegistroReceta' => 'int',
        'SaldoEnDespachoReceta' => 'int',
        'CantidadDespachada' => 'int',
        'idDosisRecetada' => 'int',
        'idEstadoDetalle' => 'int',
        'IdViaAdministracion' => 'int',
        'IdRecetaDosisUnidadMedida' => 'int',
        'IdRecetaFrecuencia' => 'int', 
        'IdDiagnostico' => 'int', 
    ];
}
