<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class EmergenciaSeguridad extends Model
{
    protected $table = 'EmergenciaSeguridad';
    protected $primaryKey = 'IdSeguridad';
    public $timestamps = false;
}
