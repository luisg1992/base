<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    protected $table = 'TiposServicio';

    protected $primaryKey = 'IdTipoServicio';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdTipoServicio',
        'Descripcion',
        'Estado'
    ];

    protected $casts = [
        'Estado' => 'int',
        'IdTipoServicio' => 'int'
    ];

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'IdTipoServicio', 'IdTipoServicio');
    }
}