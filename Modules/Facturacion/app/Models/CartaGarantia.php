<?php

namespace Modules\Facturacion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Configuracion\Models\FuenteFinanciamiento;
use Modules\Configuracion\Models\TipoFinanciamiento;
use Modules\Persona\Models\Paciente;

// use Modules\Facturacion\Database\Factories\CartaGarantiaFactory;

class CartaGarantia extends Model
{
    /* use HasFactory; */

    protected $table = 'CartasGarantia';

    protected $primaryKey = 'IdCartaGarantia';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'IdFuenteFinanciamiento',
        'IdTipoFinanciamiento',
        'IdPaciente',
        'FechaInicio',
        'FechaFinal',
        'NumeroPlaca',
        'NumeroPoliza',
        'NumeroSiniestro',
        'Estado',
        'FechaRegistro',
        'IdEmpleadoRegistra'
    ];

    protected $casts = [
        'Estado' => 'int',
        'IdTipoFinanciamiento' => 'int',
        'IdFuenteFinanciamiento' => 'int',
        'IdPaciente' => 'int',
    ];

    // Relaciones con otras tablas
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'IdPaciente', 'IdPaciente');
    }

    public function fuenteFinanciamiento()
    {
        return $this->belongsTo(FuenteFinanciamiento::class, 'IdFuenteFinanciamiento', 'IdFuenteFinanciamiento');
    }

    public function tipoFinancimiento()
    {
        return $this->belongsTo(TipoFinanciamiento::class, 'IdTipoFinanciamiento', 'IdTipoFinanciamiento');
    }
    // protected static function newFactory(): CartaGarantiaFactory
    // {
    //     // return CartaGarantiaFactory::new();
    // }
}
