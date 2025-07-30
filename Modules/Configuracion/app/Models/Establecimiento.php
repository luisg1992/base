<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class Establecimiento extends Model
{
    protected $table = 'Establecimientos';

    protected $primaryKey = 'IdEstablecimiento';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Codigo',
        'Nombre',
        'IdDistrito',
        'IdTipo',
    ];

    protected $casts = [
        'IdEstablecimiento' => 'int',
        'IdDistrito' => 'int',
        'IdTipo' => 'int',
    ];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'IdDistrito', 'IdDistrito');
    }
}
