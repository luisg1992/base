<?php

namespace Modules\Reporte\Exports; 

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RefConReferenciasExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($item) {
            return [
                $item['aceptacionrecibido'] ?? '',
                $item['annioact'] ?? '',
                $item['apelmatpac'] ?? '',
                $item['apelpatpac'] ?? '',
                $item['celularpac'] ?? '',
                $item['codunico'] ?? '',
                $item['codunicodest'] ?? '',
                $item['condicion'] ?? '',
                $item['consultoriocita'] ?? '',
                $item['correopac'] ?? '',
                $item['descarteraservicio'] ?? '',
                $item['descupsdestino'] ?? '',
                $item['diaact'] ?? '',
                $item['direccion'] ?? '',
                $item['edadactual'] ?? '',
                $item['envioaceptacion'] ?? '',
                $item['especialidad'] ?? '',
                $item['estadoactual'] ?? '',
                $item['fechaaceptacion'] ?? '',
                $item['fechacita'] ?? '',
                $item['fechacreacion'] ?? '',
                $item['fechaenvio'] ?? '',
                $item['fechaini'] ?? '',
                $item['fechapacrecibido'] ?? '',
                $item['fechnacpac'] ?? '',
                $item['fgestado'] ?? '',
                $item['fgobservado'] ?? '',
                $item['fgregistro'] ?? '',
                $item['fgri'] ?? '',
                $item['horaini'] ?? '',
                $item['idcarteraservicio'] ?? '',
                $item['idcita'] ?? '',
                $item['idcontrareferencia'] ?? '',
                $item['idespecialidad'] ?? '',
                $item['idestorigen'] ?? '',
                $item['idfinanciador'] ?? '',
                $item['idpaciente'] ?? '',
                $item['idreferencia'] ?? '',
                $item['idsexo'] ?? '',
                $item['idtipodoc'] ?? '',
                $item['idubigeoinei'] ?? '',
                $item['idubigeoineidest'] ?? '',
                $item['idubigeonac'] ?? '',
                $item['idubigeores'] ?? '',
                $item['idupsdestino'] ?? '',
                $item['idupsorigen'] ?? '',
                $item['medicocita'] ?? '',
                $item['mesact'] ?? '',
                $item['nombcompper'] ?? '',
                $item['nombestdestino'] ?? '',
                $item['nombestorigen'] ?? '',
                $item['nombpac'] ?? '',
                $item['nombubigeo'] ?? '',
                $item['nomcomppac'] ?? '',
                $item['nompaciente'] ?? '',
                $item['nrohis'] ?? '',
                $item['nroreferencia'] ?? '',
                $item['numafil'] ?? '',
                $item['numdoc'] ?? '',
                $item['numdocper'] ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'aceptacionrecibido', 'annioact', 'apelmatpac', 'apelpatpac', 'celularpac', 'codunico', 'codunicodest', 'condicion', 'consultoriocita', 'correopac',
            'descarteraservicio', 'descupsdestino', 'diaact', 'direccion', 'edadactual', 'envioaceptacion', 'especialidad', 'estadoactual', 'fechaaceptacion',
            'fechacita', 'fechacreacion', 'fechaenvio', 'fechaini', 'fechapacrecibido', 'fechnacpac', 'fgestado', 'fgobservado', 'fgregistro', 'fgri',
            'horaini', 'idcarteraservicio', 'idcita', 'idcontrareferencia', 'idespecialidad', 'idestorigen', 'idfinanciador', 'idpaciente', 'idreferencia',
            'idsexo', 'idtipodoc', 'idubigeoinei', 'idubigeoineidest', 'idubigeonac', 'idubigeores', 'idupsdestino', 'idupsorigen', 'medicocita', 'mesact',
            'nombcompper', 'nombestdestino', 'nombestorigen', 'nombpac', 'nombubigeo', 'nomcomppac', 'nompaciente', 'nrohis', 'nroreferencia', 'numafil',
            'numdoc', 'numdocper',
        ];
    }
}
