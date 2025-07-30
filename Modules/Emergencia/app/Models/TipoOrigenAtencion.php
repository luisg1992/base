<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class TipoOrigenAtencion extends Model
{
    protected $table = 'TiposOrigenAtencion';
    protected $primaryKey = 'IdOrigenAtencion';
    public $timestamps = false;
}
