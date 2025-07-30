<?php

namespace App\Core\Table;

class TableBuilder
{
    protected array $columns = [];

    public function agregarColumna(Column $column): self
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * Retorna la configuración de las columnas en un array.
     */
    public function obtenerColumnasTabla(): array
    {
        return array_map(fn($column) => $column->toArray(), $this->columns);
    }
}
