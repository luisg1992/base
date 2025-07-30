<?php

namespace Modules\AtencionMedica\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Configuracion\Models\Diagnostico;

class AtencionDiagnostico extends Model
{
    protected $table = 'AtencionesDiagnosticos';

    protected $primaryKey = 'IdAtencionDiagnostico';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'IdAtencion',
        'IdClasificacionDx',
        'IdDiagnostico',
        'IdSubclasificacionDx',
        'labConfHIS',
        'GrupoHIS',
        'SubGrupoHIS',
        'TipoOperacion',
        'TipoAccidDañoDiscp',
        'CodigoDiscp',
        'GravDiscp',
        'AñoDiscp',
        'MesDiscp',
        'DiaDiscp',
        'AyudaTecnicaDiscp',
        'AltaDiscp',
        'ProdDiscp',
        'labConfHIS_2',
        'labConfHIS_3'
    ];

    protected $casts = [
        'IdAtencion' => 'int',
        'IdClasificacionDx' => 'int',
        'IdDiagnostico' => 'int',
        'IdSubclasificacionDx' => 'int',
        'GrupoHIS' => 'int',
        'SubGrupoHIS' => 'int',
        'AñoDiscp' => 'int',
        'MesDiscp' => 'int',
        'DiaDiscp' => 'int',
        'AyudaTecnicaDiscp' => 'int',
        'AltaDiscp' => 'int',
        'ProdDiscp' => 'int',
        'labConfHIS' => 'int',
        'labConfHIS_2' => 'int',
        'labConfHIS_3' => 'int'
    ];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'IdDiagnostico', 'IdDiagnostico');
    }
 
}
