<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Configuracion\Database\Factories\RecetaClasificacionViaAdminFactory;

class RecetaClasificacionViaAdmin extends Model
{
    protected $table = 'RecetaClasificacionViasAdmin';

    protected $primaryKey = 'IdCategoria';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdCategoria',
        'Categoria',
    ];

    protected $casts = [
        'IdCategoria' => 'int',
    ];
}
