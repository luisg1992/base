<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMotivoTriajeEmergencia extends Model
{
    protected $table = 'TiposMotivosTriajeEmergencia';

    protected $primaryKey = 'IdMotivo';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'IdPrioridad',
        'IdServicio',
        'IdEstado'
    ];

    protected $casts = [
        'IdEstado' => 'int',
        'IdPrioridad' => 'int',
        'IdServicio' => 'int'

    ];

    public function prioridad()
    {
        return $this->belongsTo(TipoGravedadAtencion::class, 'IdPrioridad', 'IdTipoGravedad');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'IdServicio', 'IdServicio');
    }
}
