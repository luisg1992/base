<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFinanciamiento extends Model
{
    protected $table = 'TiposFinanciamiento';
    protected $primaryKey = 'IdTipoFinanciamiento';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'IdTipoFinanciamiento',
        'Descripcion',
        'esOficina',
        'esSalida',
        'SeIngresPrecios',
        'EsFarmacia',
        'idCajaTiposComprobante',
        'tipoVenta',
        'SeImprimeComprobante',
        'esFuenteFinanciamiento',
        'GeneraPago',
        'idTipoConcepto',
        'Estado'
    ];

    protected $casts = [
        'IdTipoFinanciamiento' => 'int',
        'Estado' => 'int',
        'esOficina'  => 'int',
        'esSalida'  => 'int',
        'SeIngresPrecios'  => 'int',
        'EsFarmacia'  => 'int',
        'idCajaTiposComprobante'  => 'int',
        'SeImprimeComprobante'  => 'int',
        'esFuenteFinanciamiento'  => 'int',
        'GeneraPago'  => 'int',
        'idTipoConcepto'  => 'int',
    ];
    public function tipoconcepto()
    {
        return $this->belongsTo(FarmTipoConcepto::class, 'idTipoConcepto', 'idTipoConcepto');
    }
    public function tipocomprobante()
    {
        return $this->belongsTo(CajaTipoComprobante::class, 'idCajaTiposComprobante', 'IdTipoComprobante');
    }
}
