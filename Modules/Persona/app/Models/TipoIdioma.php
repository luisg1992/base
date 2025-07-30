<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIdioma extends Model
{
    protected $table = 'TiposIdiomas';
    protected $primaryKey = 'IdIdioma';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Codigo',
        'Lengua',
        'Estado'
    ];
    protected $casts = [
        'Estado' => 'int',
    ];
}
