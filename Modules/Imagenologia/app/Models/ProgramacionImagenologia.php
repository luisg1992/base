<?php

namespace Modules\Imagenologia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Modules\Imagenologia\Database\Factories\ProgramacionImagenologiaFactory;

class ProgramacionImagenologia extends Model
{
    use HasFactory;

    protected $table = 'ProgramacionImagenologia';

    protected $primaryKey = 'IdProgramacion';
    public $incrementing = true;
    protected $keyTYpe = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdPuntoCarga',
        'Fecha',
        'Cupos',
        'Descripcion',
        'FechaReg' 
    ];

    protected $casts = [
        'IdPuntoCarga' => 'int',
        'Fecha' => 'datetime',
        'Cupos' => 'int',
        'FechaReg' => 'datetime',
        
    ];

    public function factPuntoCarga(): BelongsTo
    {
        return $this->belongsTo(FactPuntoCarga::class, 'IdPuntoCarga', 'IdPuntoCarga');
    }
    
}
