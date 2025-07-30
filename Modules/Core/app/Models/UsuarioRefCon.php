<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\UsuarioRefConFactory;

class UsuarioRefCon extends Model
{
    /* use HasFactory; */

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'UsuariosRefCon';

    protected $primaryKey = 'IdUsuarioRefCon';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombres',
        'Usuario',
        'Clave',
        'Estado'
    ];

    protected $casts = [
        'Estado' => 'int'
    ];

    // protected static function newFactory(): UsuarioRefConFactory
    // {
    //     // return UsuarioRefConFactory::new();
    // }
}
