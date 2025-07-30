<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class EmergenciaRelacionAgresorVictima extends Model
{
    protected $table = 'EmergenciaRelacionAgresorVictima';
    protected $primaryKey = 'IdRelacionAgresorVictima';
    public $timestamps = false;
}
