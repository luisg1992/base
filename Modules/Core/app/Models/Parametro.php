<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $table = 'Parametros';

    protected $primaryKey = 'IdParametro';
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdParametro',
        'Tipo',
        'Codigo',
        'ValorTexto',
        'ValorInt',
        'ValorFloat',
        'Descripcion',
        'Grupo',
    ];

    protected $casts = [
        'IdParametro' => 'int',
        'ValorInt' => 'int',
        'ValorFloat' => 'float',
    ];
}
