<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'Departamentos';

    protected $primaryKey = 'IdDepartamento';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'IdReniec',
    ];

    protected $casts = [
        'IdDepartamento' => 'int',
        'IdReniec' => 'int',
    ];
}
