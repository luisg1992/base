<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFinanciamiento extends Model
{
    protected $table = 'TiposFinanciamiento';
    protected $primaryKey = 'IdTipoFinanciamiento';
    public $timestamps = false;
}
