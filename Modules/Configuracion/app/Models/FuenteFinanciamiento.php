<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class FuenteFinanciamiento extends Model
{
    protected $table = 'FuentesFinanciamiento';
    protected $primaryKey = 'IdFuenteFinanciamiento';

    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'IdFuenteFinanciamiento',
        'Descripcion',
        'IdTipoFinanciamiento',
        'idTipoConceptoFarmacia',
        'UtilizadoEn',
        'CodigoFuenteFinanciamientoSEM',
        'idAreaTramitaSeguros',
        'EsUsadoEnCaja',
        'CodigoHIS',
        'idTipoFinanciador',
        'codigo',
        'Orden',
        'idEstado'
    ];

    protected $casts = [
        'idEstado' => 'int',
        'IdTipoFinanciamiento' => 'int',
        'idTipoConceptoFarmacia' => 'int',
        'UtilizadoEn' => 'int',
        'CodigoFuenteFinanciamientoSEM' => 'int',
        'idAreaTramitaSeguros' => 'int',
        'EsUsadoEnCaja'  => 'int',
        'CodigoHIS' => 'int',
        'idTipoFinanciador' => 'int',
        'codigo' => 'int',
        'Orden' => 'int'
    ];
    public function tipoconceptofarmacia()
    {
        return $this->belongsTo(FarmTipoConcepto::class, 'idTipoConceptoFarmacia', 'idTipoConcepto');
    }
    public function fuentefinanciador()
    {
        return $this->belongsTo(TipoFinanciador::class, 'idTipoFinanciador', 'idTipoFinanciador');
    }
    public function areatramitaseguros()
    {
        return $this->belongsTo(AreaTramitaSeguros::class, 'idAreaTramitaSeguros', 'idAreaTramitaSeguros');
    }
    public function tarifas()
    {
        return $this->hasMany(FuenteFinanciamientoTarifas::class, 'idFuenteFinanciamiento');
    }
}