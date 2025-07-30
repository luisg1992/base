<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class EmergenciaTipoTransporte extends Model
{
    protected $table = 'EmergenciaTipoTransporte';
    protected $primaryKey = 'IdTipoTransporte';
    public $timestamps = false;
}
