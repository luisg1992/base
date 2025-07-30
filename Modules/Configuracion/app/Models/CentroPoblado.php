<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CentroPoblado extends Model
{
    protected $table = 'CentrosPoblados';

    protected $primaryKey = 'IdCentroPoblado';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'IdDistrito',
    ];

    protected $casts = [
        'IdCentroPoblado' => 'int',
        'IdDistrito' => 'int',
    ];

    public function distrito(): BelongsTo
    {
        return $this->belongsTo(Distrito::class, 'IdDistrito', 'IdDistrito');
    }
}
