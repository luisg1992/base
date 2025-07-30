<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class EmergenciaTipoVehiculo extends Model
{
    protected $table = 'EmergenciaTipoVehiculo';
    protected $primaryKey = 'IdTipoVehiculo';
    public $timestamps = false;
}
