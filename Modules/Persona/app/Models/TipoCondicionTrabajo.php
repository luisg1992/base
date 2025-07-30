<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCondicionTrabajo extends Model
{
    protected $table = 'TiposCondicionTrabajo';
    protected $primaryKey = 'IdCondicionTrabajo';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'Estado'
    ];
    protected $casts = [
        'Estado' => 'int',
    ];
}
