<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class RecetaDosisUnidadMedida extends Model
{
    protected $table = 'RecetaDosisUnidadMedida';

    protected $primaryKey = 'IdRecetaDosisUnidadMedida';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdRecetaDosisUnidadMedida',
        'DosisUnidadMedida'
    ];

    protected $casts = [
        'IdRecetaDosisUnidadMedida' => 'int'
    ];
}
