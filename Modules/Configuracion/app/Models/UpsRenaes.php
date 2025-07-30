<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class UpsRenaes extends Model
{
    protected $table = 'UPSRenaes';

    protected $primaryKey = 'IdUPS';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'UPS',
        'Descripcion',
        'IdEstado',
    ];

    protected $casts = [
        'IdEstado' => 'int'
    ];
}
