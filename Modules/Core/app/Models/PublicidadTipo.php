<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class PublicidadTipo extends Model
{
    protected $table = 'PublicidadTipos';

    protected $primaryKey = 'IdPublicidadTipo';
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'ColorFondo',
        'ColorLetra',
    ];

    protected $casts = [
        'IdPublicidadTipo' => 'int',
    ];
}
