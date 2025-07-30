<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class EstablecimientoNoMinsa extends Model
{
    protected $table = 'EstablecimientosNoMinsa';

    protected $primaryKey = 'IdEstablecimientoNoMinsa';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'Nombre',
        'IdDistrito',
        'IdTipoSubsector',
    ];

    protected $casts = [
        'IdEstablecimientoNoMinsa' => 'int',
        'IdDistrito' => 'int',
        'IdTipoSubsector' => 'int',
    ];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'IdDistrito', 'IdDistrito');
    }
}
