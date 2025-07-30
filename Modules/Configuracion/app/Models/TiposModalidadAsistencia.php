<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TiposModalidadAsistencia extends Model
{
    protected $table = 'TiposModalidadAsistencia';

    protected $primaryKey = 'IdModalidad';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdModalidad',
        'Descripcion',
        'IdEstado'
    ];

    protected $casts = [
        'IdModalidad' => 'int',
        'IdEstado' => 'int'
    ];
}
