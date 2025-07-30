<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class UpsServicio extends Model
{
    protected $table = 'UPServicios';

    protected $primaryKey = 'IdUPS';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estado',
        'UPShis'
    ];

    protected $casts = [
        'estado' => 'int'
    ];
}
