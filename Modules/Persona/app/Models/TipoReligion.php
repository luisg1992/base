<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoReligion extends Model
{
    protected $table = 'TiposReligion';
    protected $primaryKey = 'IdReligion';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'DEscripcion',
        'Estado'
    ];
    protected $casts = [
        'Estado' => 'int',
    ];
}
