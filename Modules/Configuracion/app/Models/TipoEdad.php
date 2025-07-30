<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEdad extends Model
{
    protected $table = 'TiposEdad';

    protected $primaryKey = 'IdTipoEdad';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Codigo',
        'Descripcion',
        'tipoEdadSEM'
    ];

    protected $casts = [
        'IdTipoEdad' => 'int'
    ];
}
