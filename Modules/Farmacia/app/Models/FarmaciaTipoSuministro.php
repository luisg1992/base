<?php

namespace Modules\Farmacia\Models;

use Illuminate\Database\Eloquent\Model;

class FarmaciaTipoSuministro extends Model
{
    protected $table = 'farmTipoSuministro';
    protected $primaryKey = 'idTipoSuministro';
    public $incrementing = false;
}
