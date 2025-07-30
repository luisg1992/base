<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class RecetaDosis extends Model
{
    protected $table = 'RecetaDosis';

    protected $primaryKey = 'idDosis';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'idDosis',
        'NumeroDosis'
    ];

    protected $casts = [
        'idDosis' => 'int'
    ];
}
