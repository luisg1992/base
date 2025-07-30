<?php

namespace Modules\Persona\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'Pacientes';
    protected $primaryKey = 'IdPaciente';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'ApellidoPaterno',
        'ApellidoMaterno',
        'PrimerNombre',
        'SegundoNombre',
        'TercerNombre',

        'FechaNacimiento',
        'NroDocumento',
        'Telefono',
        'DireccionDomicilio',
        'Autogenerado',
        'IdTipoSexo',
        'IdProcedencia',
        'IdGradoInstruccion',
        'IdEstadoCivil',
        'IdDocIdentidad',

        'IdTipoOcupacion',
        'IdCentroPobladoNacimiento',
        'IdCentroPobladoDomicilio',
        'NombrePadre',
        'NombreMadre',
        'NroHistoriaClinica',
        'IdTipoNumeracion',
        'IdCentroPobladoProcedencia',
        'Observacion',
        'IdPaisDomicilio',

        'IdPaisProcedencia',
        'IdPaisNacimiento',
        'IdDistritoProcedencia',
        'IdDistritoDomicilio',
        'IdDistritoNacimiento',
        'FichaFamiliar',
        'IdEtnia',
        'GrupoSanguineo',
        'FactorRh',
        'UsoWebReniec',


        'IdIdioma',
        'Email',
        'madreDocumento',
        'madreApellidoPaterno',
        'madreApellidoMaterno',
        'madrePrimerNombre',
        'madreSegundoNombre',
        'NroOrdenHijo',
        'madreTipoDocumento',
        'Sector',

        'Sectorista',
        'PacienteCrearNroAutogenerado',
        'id_etnia',
        'IdReligion',
        'ImagenFirma',
        'ImagenFoto', 
    ];

    protected $casts = [
        'FechaNacimiento' => 'date',
        'IdEstadoCivil' =>  'integer',
        'IdGradoInstruccion' =>  'integer',
    ];

    // Nuevo método agregado aquí
    public static function buscarPorDocumentoYTipo($nroDocumento, $tipodocumento)
    {
        $cacheKey = "paciente_{$tipodocumento}_{$nroDocumento}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $hidden = ['Anexo11', 'Anexo12', 'Archivo', 'NombreArchivo11', 'NombreArchivo12'];
        if ($tipodocumento == 1) {
            $paciente = self::where('NroDocumento', $nroDocumento)
                ->where('IdDocIdentidad', $tipodocumento)
                //->where('UsoWebReniec', 1)
                ->first()?->makeHidden($hidden);
        } else {
            $paciente = self::where('NroDocumento', $nroDocumento)
                ->where('IdDocIdentidad', $tipodocumento)
                ->first()?->makeHidden($hidden);
        }

        // Solo guarda en caché si encontró un resultado
        if ($paciente) {
            Cache::put($cacheKey, $paciente, now()->addHours(1));
        }

        return $paciente;
    }

    // Nuevo método agregado aquí
    public static function buscarPorNroHistoriaClinica($NroHistoriaClinica)
    {
        $cacheKey = "paciente_hc_{$NroHistoriaClinica}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        $paciente = self::where('NroHistoriaClinica ', $NroHistoriaClinica)->first();
        if ($paciente) {
            Cache::put($cacheKey, $paciente, now()->addHours(1));
        }

        return $paciente;
    }


    public static function buscarPorDocumentoYTipoUpdate($nroDocumento, $tipodocumento)
    {
        $cacheKey = "paciente_{$tipodocumento}_{$nroDocumento}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $hidden = ['Anexo11', 'Anexo12', 'Archivo', 'NombreArchivo11', 'NombreArchivo12'];
        $paciente = self::where('NroDocumento', $nroDocumento)
            ->where('IdDocIdentidad', $tipodocumento)
            ->first()?->makeHidden($hidden);

        // Solo guarda en caché si encontró un resultado
        if ($paciente) {
            Cache::put($cacheKey, $paciente, now()->addHours(1));
        }

        return $paciente;
    }
}
