<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class NroHistoria extends Model
{
    protected $table = 'nro_historia';
    public $timestamps = false;

    protected $fillable = [
        'historia',
    ];

    protected $casts = [
        'historia' => 'int',
    ];
}
