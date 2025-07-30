<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class EmergenciaTipoEvento extends Model
{
    protected $table = 'EmergenciaTipoEvento';
    protected $primaryKey = 'IdTipoEvento';
    public $timestamps = false;
}
