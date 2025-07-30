<?php

namespace Modules\Configuracion\Models;


use Illuminate\Database\Eloquent\Model;

class TipoFinanciador extends Model
{
    protected $table = 'TipoFinanciador';
    protected $primaryKey = 'idTipoFinanciador';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'denominacion',
        'codigo',
        'Estado'
    ];

    protected $casts = [
        'Estado' => 'int',
        'codigo' => 'int',
    ];
}