<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class EspecialidadCE extends Model
{
    protected $table = 'EspecialidadCE';

    protected $primaryKey = 'IdEspecialidadCE';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdEspecialidad',
        'TiempoPromedioAtencion',
        'IdProductoConsulta',
        'IdProductoInterconsulta'
    ];

    protected $casts = [
        'IdEspecialidad' => 'int',
        'IdProductoConsulta' => 'int',
        'IdProductoInterconsulta' => 'int'
    ];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'IdEspecialidad', 'IdEspecialidad');
    }
}
