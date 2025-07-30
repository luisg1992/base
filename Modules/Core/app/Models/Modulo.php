<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;

class Modulo extends Model
{
    protected $table = 'Modulos';

    protected $primaryKey = 'ModuloId';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'ModuloPadreId',
        'Etiqueta',
        'Subtitulo',
        'Descripcion',
        'Icono',
        'Url',
        'EsAccesoDirecto',
        'EstaBloqueado',
        'Estado',
        'Orden',
        'Valor',
    ];

    protected $casts = [
        'ModuloPadreId' => 'int',
        'EsAccesoDirecto' => 'int',
        'EstaBloqueado' => 'int',
        'Estado' => 'int',
        'Orden' => 'int',
    ];


    public function padre(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'ModuloPadreId');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(Modulo::class, 'ModuloPadreId');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'modulo_id');
    }

    public function getFullLabel(): string
    {
        if ($this->padre) {
            return $this->padre->getFullLabel() . ' > ' . $this->Etiqueta;
        }
        return $this->Etiqueta;
    }

    public function acciones(): HasMany
    {
        return $this->hasMany(ModuloAccion::class, 'ModuloId');
    }
}
