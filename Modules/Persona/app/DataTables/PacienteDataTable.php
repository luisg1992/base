<?php

namespace Modules\Persona\DataTables;

use App\Core\Table\Button;
use App\Core\Table\Column;
use App\Core\Table\Filter;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\TableBuilder;
use App\Core\Table\FilterBuilder;
use App\Core\Table\ButtonBuilder;
use App\DataTables\Traits\PaginationTrait;
use Modules\Persona\Http\Resources\PacienteCollection;
use Modules\Persona\Models\Paciente;

trait PacienteDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Pacientes';
        $this->titulo_tabla = 'Pacientes';
        $this->nombre_tabla = 'pacientes';
        $this->columns = $this->obtenerColumnasTabla();
        $this->getConfiguracionDataTable();
        $this->filters = $this->obtenerCamposFiltro();

        return [
            'titulo_pagina' => $this->titulo_pagina,
            'titulo_tabla' => $this->titulo_tabla,
            'nombre_tabla' => $this->nombre_tabla,
            'columns' => $this->columns,
            'visible_columns' => $this->visibleColumns,
            'pagination' => $this->initPagination(),
            'filters' => $this->filters,
            'header_buttons' => $this->obtenerBotonesCabecera(),
        ];
    }

    private function obtenerBotonesCabecera(): array
    {
        $valorPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
        $acciones = [
            'crear' => Button::botonCrear()->label('Nuevo'),
        ];

        $builder = new ButtonBuilder();
        $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones);
        $builder->agregarBoton(Button::botonRecargar());

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
        $builder->agregarColumna(Column::make('ImagenFoto')->label('Foto'));
        $builder->agregarColumna(Column::make('NombreCompleto')->label('Nombre')->sortable(true));
        $builder->agregarColumna(Column::make('NroDocumento')->label('Nro.Documento')->sortable(true));
        $builder->agregarColumna(Column::make('FechaNacimiento')->label('Fecha de nacimiento')->sortable(true));
        $builder->agregarColumna(Column::make('NroHistoriaClinica')->label('Nro.Historia')->sortable(true));
//        $builder->agregarColumna(Column::make('Usuario')->label('¿Tiene usuario?')->sortable(false)->type('indicador'));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('nombre')
                ->label('Apellidos y nombres')
                ->cssClass('col-12')
                ->placeholder('Ingrese el apellido, nombre o DNI y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $nombre = optional($filters->firstWhere('name', 'nombre'))['value'] ?? '';

        $query = Paciente::query();
        if ($nombre !== '') {
            $words = explode(' ', $nombre);
            $query->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->where(function ($subq) use ($word) {
                        $subq->where('ApellidoPaterno', 'LIKE', "{$word}%")
                            ->orWhere('ApellidoMaterno', 'LIKE', "{$word}%")
                            ->orWhere('PrimerNombre', 'LIKE', "{$word}%")
                            ->orWhere('SegundoNombre', 'LIKE', "{$word}%")
                            ->orWhere('NroDocumento', 'LIKE', "{$word}%");
                    });
                }
            });
        }

        $query->orderByDesc('IdPaciente');

        return (new PacienteCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
