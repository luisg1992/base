<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Publicidad extends Model
{
    protected $table = 'Publicidades';

    protected $primaryKey = 'IdPublicidad';
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Titulo',
        'Descripcion',
        'TamanoLetra',
        'IdPublicidadTipo',
        'PosicionVertical',
        'Estado'
    ];

    protected $casts = [
        'IdPublicidad' => 'int',
        'TamanoLetra' => 'int',
        'IdPublicidadTipo' => 'int',
        'PosicionVertical' => 'boolean',
        'Estado' => 'int',
    ];

    public function tipo()
    {
        return $this->belongsTo(PublicidadTipo::class, 'IdPublicidadTipo', 'IdPublicidadTipo');
    }
}
