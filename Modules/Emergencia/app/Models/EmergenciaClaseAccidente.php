<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class EmergenciaClaseAccidente extends Model
{
    protected $table = 'EmergenciaClaseAccidente';
    protected $primaryKey = 'IdClaseAccidente';
    public $timestamps = false;
}
