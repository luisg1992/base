<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioAtencionSimultanea extends Model
{
    protected $table = 'ServiciosAtenSimultanea';

    public $timestamps = false;

    protected $fillable = [
        'idServicio',
        'idServicioAtencionSimultanea',
    ];

    protected $casts = [
        'idServicio' => 'int',
        'idServicioAtencionSimultanea' => 'int'
    ];
}
