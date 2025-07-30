<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class SubclasificacionDiagnosticos extends Model
{
    protected $table = 'SubclasificacionDiagnosticos';
    protected $primaryKey = 'IdSubclasificacionDx';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'IdSubclasificacionDx',
        'Codigo',
        'Descripcion',
        'IdClasificacionDx',
        'IdTipoServicio'
    ];

    protected $casts = [
        'IdSubclasificacionDx' => 'int',
        'IdClasificacionDx' => 'int',
        'IdTipoServicio' => 'int'
    ];
}
