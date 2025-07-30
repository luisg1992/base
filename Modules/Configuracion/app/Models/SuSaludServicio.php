<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class SuSaludServicio extends Model
{
    protected $table = 'SuSalud_ups';

    protected $primaryKey = 'Codigo';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
    ];
}
