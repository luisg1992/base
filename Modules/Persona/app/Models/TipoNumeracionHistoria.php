<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoNumeracionHistoria extends Model
{
    protected $table = 'TiposNumeracionHistoria';
    protected $primaryKey = 'IdTipoNumeracion';
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
