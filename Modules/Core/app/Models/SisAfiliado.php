<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SisAfiliado extends Model
{
    use HasFactory;

    protected $table = 'SisAfiliados';
    protected $primaryKey = 'Id';
    public $timestamps = false;
    protected $connection = 'sqlsrvServicios';

    protected $fillable = [
        'IdError',
        'Resultado',
        'TipoDocumento',
        'NroDocumento',
        'ApePaterno',
        'ApeMaterno',
        'Nombres',
        'FecAfiliacion',
        'EESS',
        'DescEESS',
        'EESSUbigeo',
        'DescEESSUbigeo',
        'Regimen',
        'TipoSeguro',
        'DescTipoSeguro',
        'Contrato',
        'FecCaducidad',
        'Estado',
        'Tabla',
        'IdNumReg',
        'Genero',
        'FecNacimiento',
        'IdUbigeo',
        'Disa',
        'TipoFormato',
        'NroContrato',
        'Correlativo',
        'IdPlan',
        'IdGrupoPoblacional',
        'MsgConfidencial'
    ];
}
