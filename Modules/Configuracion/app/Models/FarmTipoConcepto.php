<?php

namespace Modules\Configuracion\Models;


use Illuminate\Database\Eloquent\Model;

class FarmTipoConcepto extends Model
{
    protected $table = 'farmTipoConceptos';
    protected $primaryKey = 'idTipoConcepto';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'idTipoConcepto',
        'codigoMINSA',
        'Concepto',
        'Estado'

    ];
    protected $casts = [
        'idTipoConcepto' => 'int',
        'Estado' => 'int'
    ];
}