<?php

namespace App\Core\Services;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StoredProcedureService
{
    protected string $connection;

    public function __construct(string $connection = 'sqlsrv')
    {
        $this->connection = $connection;
    }

    /**
     * Ejecuta un procedimiento almacenado con parámetros y devuelve los resultados.
     *
     * @param string $procedure Nombre del procedimiento (ej. 'dbo.MiProcedimiento')
     * @param array $params Lista de parámetros
     * @return array
     */
    public function execute(Request $request, string $procedure, array $params = []): LengthAwarePaginator|array
    {
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $sql = "EXEC $procedure $placeholders";

        $results = DB::connection($this->connection)->select($sql, $params);
        $records = collect($results);
        $total = $records->first()->TotalCount ?? 0;
        $limit = $request->input('limit');
        $page = $request->input('page');

        return new LengthAwarePaginator(
            $results,
            (int) $total,
            $limit,
            $page,
        );
    }

    public function executeSinPaginacion(string $procedure, array $params = [])
    {
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $sql = "EXEC $procedure $placeholders";

        return DB::connection($this->connection)->select($sql, $params);
    }
}
