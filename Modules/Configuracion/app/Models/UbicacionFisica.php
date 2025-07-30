<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class UbicacionFisica extends Model
{
    protected $table = 'UbicacionFisica';

    protected $primaryKey = 'IdUbicacionFisica';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Estado',
        'TipoUbicacionFisica',
        'IdEspecialidadPrimaria',
    ];

    protected $casts = [
        'Estado' => 'int',
        'IdEspecialidadPrimaria' => 'int',
    ];
     
     
    public function terminales()
    {
        return $this->hasMany(Terminal::class, 'IdUbicacionesFisicas', 'IdUbicacionFisica');
    }

    public function especialidadPrimaria()
    {
        return $this->belongsTo(EspecialidadPrimaria::class, 'IdEspecialidadPrimaria', 'IdEspecialidadPrimaria');
    }
}
