<?php

namespace Modules\Configuracion\Models;


use Illuminate\Database\Eloquent\Model;

class TipoReferencia extends Model
{
    protected $table = 'TiposReferencia';
    protected $primaryKey = 'IdTipoReferencia';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
}
