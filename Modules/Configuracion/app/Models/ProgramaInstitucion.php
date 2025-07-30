<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\ProgramaInstitucionFactory;

class ProgramaInstitucion extends Model
{
    /* use HasFactory; */
    protected $table = 'ProgramasInstituciones';

    protected $primaryKey = 'IdInstitucion';
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

    // protected static function newFactory(): ProgramaInstitucionFactory
    // {
    //     // return ProgramaInstitucionFactory::new();
    // }
}
