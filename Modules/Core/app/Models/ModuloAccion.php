<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuloAccion extends Model
{
    protected $table = 'ModuloAcciones';

    protected $primaryKey = 'ModuloAccionId';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'ModuloId',
        'Nombre',
        'Valor',
        'Descripcion',
    ];

    protected $casts = [
        'ModuloId' => 'int',
    ];

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'ModuloId');
    }
}
