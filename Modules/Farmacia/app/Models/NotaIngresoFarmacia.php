<?php

namespace Modules\Farmacia\Models;

use Illuminate\Database\Eloquent\Model;

class NotaIngresoFarmacia extends Model
{
    protected $table = 'farmacias';

    protected $fillable = [
        'nombre',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
