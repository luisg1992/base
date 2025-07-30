<?php

namespace Modules\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEtnia extends Model
{
    protected $table = 'HIS_tabetnia';
    protected $primaryKey = 'codetni';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'desetni',
        'codgen',
        'etnias',
        'id_etnia',
        'Estado'
    ];
    protected $casts = [
        'Estado' => 'int',
    ];
}
