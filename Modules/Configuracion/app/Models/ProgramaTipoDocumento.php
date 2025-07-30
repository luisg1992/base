<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\ProgramaTipoDocumentoFactory;

class ProgramaTipoDocumento extends Model
{
    /* use HasFactory; */
    protected $table = 'ProgramasTiposDocumentos';

    protected $primaryKey = 'IdTipoDocumento';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'Descripcion',
        'Estado',
        'FechaRegistro',
        'IdEmpleadoRegistra',
    ];

    protected $casts = [
        'Estado' => 'int'
    ];

    // protected static function newFactory(): ProgramaTipoDocumentoFactory
    // {
    //     // return ProgramaTipoDocumentoFactory::new();
    // }
}
