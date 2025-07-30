<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class RefconMotivoContraRef extends Model 
{
    protected $table = 'Refcon_Motivo_ContraRef';

    protected $primaryKey = 'IdMotivo';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdMotivo',
        'Descripcion',
    ];

    protected $casts = [
        'IdMotivo' => 'int',
    ];
}
