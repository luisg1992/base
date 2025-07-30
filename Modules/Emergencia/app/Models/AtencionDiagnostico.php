<?php

namespace Modules\Emergencia\Models;

use Illuminate\Database\Eloquent\Model;

class AtencionDiagnostico extends Model
{
    protected $table = 'AtencionesDiagnosticos';
    protected $primaryKey = 'IdAtencionDiagnostico';
    public $timestamps = false;
}
