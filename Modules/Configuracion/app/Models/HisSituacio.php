<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class HisSituacio extends Model
{
    protected $table = 'HIS_situacio';
    protected $primaryKey = 'IdHisSituacio';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'IdHisSituacio',
        'valores',
        'descripcio',
        'codigo',
        'est'
    ];

    protected $casts = [
        'IdHisSituacio' => 'int',
        'codigo' => 'float',  
        'valores' => 'string',
        'descripcio' => 'string',
        'est' => 'string'
    ];
}
