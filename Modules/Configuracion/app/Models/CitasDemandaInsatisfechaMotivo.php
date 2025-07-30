<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class CitasDemandaInsatisfechaMotivo extends Model
{
    protected $table = 'WebS_CitasDemandaInsatisfechaMotivo';

    protected $primaryKey = 'IdDemandaInsatisfechaMotivo';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion', 
        'Estado'
    ];

    protected $casts = [
        'Estado' => 'int'
    ];
}
