<?php

namespace Modules\Imagenologia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Configuracion\Models\FactPuntoCarga;
use Modules\Configuracion\Models\FacturacionCuentasAtencion;

class RecetaCabecera extends Model
{
    protected $table = 'RecetaCabecera';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'idReceta',
        'IdPuntoCarga',
        'FechaReceta',
        'idCuentaAtencion',
        'idServicioReceta',
        'idEstado',
        'DocumentoDespacho',
        'idComprobantePago',
        'idMedicoReceta',
        'fechaVigencia',
        'Observacion',
        'IdRecetaVincula'
    ];

    public function factPuntoCarga()
    {
        return $this->belongsTo(FactPuntoCarga::class, 'IdPuntoCarga', 'IdPuntoCarga');
    }

    public function recetaDetalle()
    {
        return $this->hasMany(RecetaDetalle::class, 'idReceta', 'idReceta');
    }

    public function facturacionCuentasAtencion()
    {
        return $this->belongsTo(FacturacionCuentasAtencion::class, 'idCuentaAtencion', 'IdCuentaAtencion');
    }

    public static function obtenerIdRecetaPorDetalle($idCuentaAtencion, $idPuntoCarga, $idProducto, $CodigoDx)
    {
        return self::query()
            ->join('RecetaDetalle', 'RecetaCabecera.idReceta', '=', 'RecetaDetalle.idReceta')
            ->where('RecetaCabecera.idCuentaAtencion', $idCuentaAtencion)
            ->where('RecetaCabecera.IdPuntoCarga', $idPuntoCarga)
            ->where('RecetaDetalle.idItem', $idProducto)
            ->where('RecetaDetalle.CodigoDx', $CodigoDx)
            ->where('RecetaDetalle.idEstadoDetalle', 1)
            ->value('RecetaDetalle.idReceta');
    }
}
