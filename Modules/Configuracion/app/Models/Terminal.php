<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class Terminal extends Model
{
    protected $table = 'Terminales';

    protected $primaryKey = 'TerminalesId';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdUbicacionesFisicas',
        'Nombre',
        'IpAddress',
        'IpV6',
        'MacAddress',
        'Estado'
    ];

    protected $casts = [
        'IdUbicacionesFisicas' => 'int',
        'Estado' => 'int'
    ];

    public function ubicacionFisica()
    {
        return $this->belongsTo(UbicacionFisica::class, 'IdUbicacionesFisicas', 'IdUbicacionFisica');
    }

    public function impresoras()
    {
        return $this->hasMany(Impresora::class, 'IdTerminales', 'TerminalesId');
    }
}
