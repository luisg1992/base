<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDestacado extends Model
{
    protected $table = 'TiposDestacados';
    protected $primaryKey = 'idDestacado';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Destacado',
        'Estado'
    ];
    protected $casts = [
        'Estado' => 'int',
    ];
}
