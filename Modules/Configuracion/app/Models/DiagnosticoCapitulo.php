<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticoCapitulo extends Model
{
    protected $table = 'DiagnosticosCapitulos';

    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'IdCapitulo';
    protected $keyType = 'int';

    protected $fillable = [
        'Codigo',
        'Descripcion',
    ];
}