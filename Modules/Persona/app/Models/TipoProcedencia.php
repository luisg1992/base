<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProcedencia extends Model
{
    protected $table = 'TiposProcedencia';
    protected $primaryKey = 'IdProcedencia';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'Estado'
    ];

    protected $casts = [
        'IdProcedencia' => 'int',
        'Estado' => 'int',
    ];
}
