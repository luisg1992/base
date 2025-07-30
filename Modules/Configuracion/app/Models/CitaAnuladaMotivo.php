<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class CitaAnuladaMotivo extends Model
{
    protected $table = 'WebS_CitasAnuladasMotivo';

    protected $primaryKey = 'IdMotivo';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'IdEstado'
    ];

    protected $casts = [
        'IdEstado' => 'int'
    ];
}
