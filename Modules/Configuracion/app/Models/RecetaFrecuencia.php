<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class RecetaFrecuencia extends Model
{
    protected $table = 'RecetaFrecuencia';

    protected $primaryKey = 'IdRecetaFrecuencia';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdRecetaFrecuencia',
        'Descripcion',
        'Orden'
    ];

    protected $casts = [
        'IdRecetaFrecuencia' => 'int',
        'Orden' => 'int'
    ];
}
