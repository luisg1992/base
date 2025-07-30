<?php

namespace Modules\Configuracion\Models;


use Illuminate\Database\Eloquent\Model;
use Modules\Persona\Models\TipoSexo;

class Diagnostico extends Model
{
    protected $table = 'Diagnosticos';
    protected $primaryKey = 'IdDiagnostico';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'CodigoCIE2004',
        'Descripcion',
        'IdCapitulo',
        'IdGrupo',
        'IdCategoria',
        'CodigoExportacion',
        'CodigoCIE9',
        'CodigoCIE10',
        'Gestacion',
        'Morbilidad',
        'Intrahospitalario',
        'Restriccion',
        'EdadMaxDias',
        'EdadMinDias',
        'IdTipoSexo',
        'ClaseDxHIS',
        'DescripcionMINSA',
        'codigoCIEsinPto',
        'FechaInicioVigencia',
        'EsActivo'
    ];

    protected $casts = [
        'EsActivo' => 'int',
        'IdCapitulo' => 'int',
        'IdGrupo' => 'int',
        'IdCategoria' => 'int',
        'IdTipoSexo' => 'int',
        'Gestacion'  => 'int',
        'Morbilidad' => 'int',
        'Intrahospitalario' => 'int',
        'Restriccion' => 'int'


    ];
    public function capitulo()
    {
        return $this->belongsTo(DiagnosticoCapitulo::class, 'IdCapitulo', 'IdCapitulo');
    }
    public function grupo()
    {
        return $this->belongsTo(DiagnosticoGrupo::class, 'IdGrupo', 'IdGrupo');
    }
    public function categoria()
    {
        return $this->belongsTo(DiagnosticoCategorias::class, 'IdCategoria', 'IdCategoria');
    }
    public function tiposexo()
    {
        return $this->belongsTo(TipoSexo::class, 'IdTipoSexo', 'IdTipoSexo');
    }
}