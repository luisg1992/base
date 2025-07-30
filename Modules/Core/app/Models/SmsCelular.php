<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCelular extends Model
{
    protected $table = 'SmsCelulares';

    protected $primaryKey = 'IdSmsCelular';
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Url',
        'Token',
        'Usuario',
        'Celular',
        'Estado'
    ];

    protected $casts = [
        'IdSmsCelular' => 'int',
        'Estado' => 'int',
    ];
}
