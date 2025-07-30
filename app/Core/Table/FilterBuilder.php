<?php

namespace App\Core\Table;

class FilterBuilder
{
    protected array $filters = [];

    public function agregarFiltro(Filter $filter): self
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Retorna la configuración de las filtros en un array.
     */
    public function obtenerCamposFiltro(): array
    {
        return array_map(fn($filter) => $filter->toArray(), $this->filters);
    }
}
