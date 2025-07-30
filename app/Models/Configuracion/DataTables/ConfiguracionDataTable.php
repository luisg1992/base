<?php

namespace App\Models\Configuracion\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfiguracionDataTable extends Model
{
    protected $table = 'configuracion_tablas_datos';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'tabla',
        'columnas_visibles',
        'registros_por_pagina',
    ];

    protected $casts = [
        'columnas_visibles' => 'array',
        'registros_por_pagina' => 'int',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
