<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class RecetaViaAdministracion extends Model
{
    protected $table = 'RecetaViaAdministracion';

    protected $primaryKey = 'IdViaAdministracion';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdViaAdministracion',
        'Descripcion',
        'IdCategoria'
    ];

    protected $casts = [
        'IdViaAdministracion' => 'int',
        'IdCategoria' => 'int'
    ];

    public function categoria()
    {
        return $this->belongsTo(RecetaClasificacionViaAdmin::class, 'IdCategoria', 'IdCategoria');
    }
}
