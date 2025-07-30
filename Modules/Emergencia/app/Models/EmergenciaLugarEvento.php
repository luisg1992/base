<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class EmergenciaLugarEvento extends Model
{
    protected $table = 'EmergenciaLugarEvento';
    protected $primaryKey = 'IdLugarEvento';
    public $timestamps = false;
}
