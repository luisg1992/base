<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEdadCategoria extends Model
{
    protected $table = 'EdadCategorias';

    protected $primaryKey = 'IdEdadCategoria';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'GrupoEdad',
        'EdadMinima',
        'EdadMaxima',
        'Estado'
    ];

    protected $casts = [
        'IdEdadCategoria' => 'int',
        'EdadMinima' => 'int',
        'EdadMaxima' => 'int',
        'Estado' => 'bool'
    ];
}
