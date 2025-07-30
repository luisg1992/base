<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class HisColegio extends Model
{
    protected $table = 'HIS_Colegios';

    protected $primaryKey = 'cod_col';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'cod_col',
        'des_col',
    ];
}
