<?php

// namespace App\Cache\Atencion;

// use App\Cache\TraitCache;
// use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\DB;

// class AtencionMedicaBusquedaDiagnosticoCache
// {
//     use TraitCache;

//     const string TABLE_NAME = 'atencion_medica_busqueda_diagnostico';

//     static function getCache()
//     {
//         $cache_name = self::getNameCache();

//         if (Cache::has($cache_name)) {
//             return Cache::get($cache_name);
//         }

//         // Consulta a Diagnosticos directamente
//         $records = DB::table('Diagnosticos')
//             ->select(
//                 'IdDiagnostico',
//                 'codigoCIEsinPto',
//                 'Descripcion',
//                 'CodigoCIE10',
//                 'CodigoCIE9',
//                 'EsActivo',
//                 'FechaInicioVigencia'
//             )
//             ->where('EsActivo', 1)
//             ->whereNotNull('descripcionMINSA')
//             ->orderBy('Descripcion')
//             ->orderBy('CodigoCIE10') 
//             ->get()
//             ->transform(function ($row) {
//                 return [
//                     'id' => $row->IdDiagnostico,
//                     'value' => $row->IdDiagnostico,
//                     'label' => str_to_upper_utf8($row->Descripcion),
//                     'name' => str_to_upper_utf8($row->Descripcion),
//                     'CodigoCIE10' => $row->CodigoCIE10,
//                     'CodigoCIE9' => $row->CodigoCIE9,
//                     'CodigoCIEsinPto' => $row->codigoCIEsinPto,
//                     'FechaInicioVigencia' => $row->FechaInicioVigencia,
//                 ];
//             })
//             ->toArray();

//         // Guardar en caché por 2 horas (7200 segundos)
//         Cache::put($cache_name, $records, 7200);

//         return $records;
//     }
// }
