<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoSexo extends Model
{
    protected $table = 'TiposSexo';
    protected $primaryKey = 'IdTipoSexo';
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
