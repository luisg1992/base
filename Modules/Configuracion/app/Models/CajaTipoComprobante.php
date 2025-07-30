<?php

namespace Modules\Configuracion\Models;


use Illuminate\Database\Eloquent\Model;

class CajaTipoComprobante extends Model
{
    protected $table = 'CajaTiposComprobante';
    protected $primaryKey = 'IdTipoComprobante';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'IdTipoComprobante',
        'Descripcion',
        'Estado'
    ];

    protected $casts = [
        'IdTipoComprobante' => 'int',
        'Estado' => 'int'
    ];
}