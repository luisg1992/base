<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoConsultorio extends Model
{
    protected $table = 'TiposConsultorio';

    protected $primaryKey = 'IdTipoConsultorio';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdTipoConsultorio',
        'Descripcion',
        'Estado'
    ];

    protected $casts = [
        'IdTipoConsultorio' => 'int',
        'Estado' => 'int'
    ];

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class, 'IdTipoConsultorio', 'IdTipoConsultorio');
    }
}
