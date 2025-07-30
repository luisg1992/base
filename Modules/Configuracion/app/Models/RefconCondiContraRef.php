<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class RefconCondiContraRef extends Model
{
    protected $table = 'Refcon_Condi_ContraRef';

    protected $primaryKey = 'IdCondi';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdCondi',
        'Valor',
        'Descripcion',
        'Tipo',
    ];

    protected $casts = [
        'IdCondi' => 'int',
        'Tipo' => 'int',
    ];
}
