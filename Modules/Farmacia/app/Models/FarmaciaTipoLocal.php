<?php

namespace Modules\Farmacia\Models;

use Illuminate\Database\Eloquent\Model;

class FarmaciaTipoLocal extends Model
{
    protected $table = 'farmTipoLocal';
    protected $primaryKey = 'idTipoLocal';
    public $incrementing = false;
}
