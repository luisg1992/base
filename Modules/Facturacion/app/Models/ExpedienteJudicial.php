<?php

namespace Modules\Facturacion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Configuracion\Models\Especialidad;
use Modules\Configuracion\Models\FuenteFinanciamiento;
use Modules\Configuracion\Models\TipoFinanciamiento;
use Modules\Configuracion\Models\TipoServicio;
use Modules\Core\Models\ProgramaInstitucion;
use Modules\Core\Models\ProgramaTipoDocumento;
use Modules\Persona\Models\Paciente;

// use Modules\Facturacion\Database\Factories\ExpedienteJudicialFactory;

class ExpedienteJudicial extends Model
{
    /* use HasFactory; */
    protected $table = 'ProgramasExpedientesJudiciales';

    protected $primaryKey = 'IdProgramaExpedienteJudicial';
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
        'IdProgramaInstitucion',
        'IdTipoDocumento',
        'IdTipoServicio',
        'IdEspecialidad',
        'NumeroDocumento',
        'FechaDocumento',
        'FechaVencimiento',
        'NumeroExpedienteTramiteDocumentario',
        'FechaRegistro',
        'IdEmpleadoRegistra',
        'Estado',
    ];

    protected $casts = [
        'Estado' => 'int',
        'IdTipoFinanciamiento' => 'int',
        'IdFuenteFinanciamiento' => 'int',
        'IdPaciente' => 'int',
        'IdProgramaInstitucion' => 'int',
        'IdTipoDocumento' => 'int',
        'IdTipoServicio' => 'int',
        'IdEspecialidad' => 'int',
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

    public function programaInstitucion()
    {
        return $this->belongsTo(ProgramaInstitucion::class, 'IdProgramaInstitucion', 'IdInstitucion');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(ProgramaTipoDocumento::class, 'IdTipoDocumento', 'IdTipoDocumento');
    }

    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class, 'IdTipoServicio', 'IdTipoServicio');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'IdEspecialidad', 'IdEspecialidad');
    }

    // protected static function newFactory(): ExpedienteJudicialFactory
    // {
    //     // return ExpedienteJudicialFactory::new();
    // }
}
