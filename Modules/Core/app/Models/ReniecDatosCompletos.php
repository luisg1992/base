<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReniecDatosCompletos extends Model
{
    use HasFactory;

    protected $table = 'ReniecDatosCompletos';
    protected $primaryKey = 'Id';
    public $timestamps = false;
    protected $connection = 'sqlsrvServicios';

    protected $fillable = [
        'CodigoRespuesta',
        'MensajeRespuesta',
        'NumerodeDNI',
        'DigitoVerificacion',
        'ApellidoPaterno',
        'ApellidoMaterno',
        'ApellidoDeCasada',
        'Nombres',
        'UbigeoContinDomicilio',
        'UbigeoPaisDomicilio',
        'UbigeoDepartDomicilio',
        'UbigeoProvinDomicilio',
        'UbigeoDistritDomicilio',
        'UbigeoLocaliDomicilio',
        'ContinenteDomicilio',
        'PaisDomicilio',
        'DepartDomicilio',
        'ProvinDomicilio',
        'DistritoDomicilio',
        'LocalidaDomicilio',
        'EstadoCivil',
        'CodigodGradoInstruccion',
        'Sexo',
        'UbigeoDepartNacimiento',
        'UbigeoProvinNacimiento',
        'UbigeoDistritNacimiento',
        'DepartamentoNacimiento',
        'ProvinciaNacimiento',
        'DistritoNacimiento',
        'FechaNacimiento',
        'NombrePadre',
        'NombreMadre',
        'FechaInscripcion',
        'FechaExpedicion',
        'Restricciones',
        'Prefijodireccion',
        'Direccion',
        'NumeroDireccion',
        'BlockChalet',
        'PrefijoBlockChalet',
        'Interior',
        'Urbanizacion',
        'Etapa',
        'Manzana',
        'Lote',
        'PrefijoDptoPisoInterior',
        'PrefijoUrbCondResid',
        'ImagenFoto',
        'ImagenFirma',
        'FechaConsulta',
    ];
}
