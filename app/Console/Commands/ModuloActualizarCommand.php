<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Models\Modulo;
use Spatie\Permission\Models\Permission;

class ModuloActualizarCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modulo:actualizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar módulo';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Hora de inicio: ' . date('Y-m-d H:i:s'));
        $this->info('Inicializando Proceso');

        $modulosRaiz = Modulo::query()->whereNull('ModuloPadreId')->get();

        foreach ($modulosRaiz as $modulo) {
            Permission::findOrCreate($modulo->Valor);
            $this->actualizarValorRecursivo($modulo, null);
        }

        $this->info('Finalizando Proceso');
        $this->info('Hora de término: ' . date('Y-m-d H:i:s'));
    }

    protected function actualizarValorRecursivo(Modulo $modulo, ?string $valorPadre)
    {
        $slug = Str::slug($modulo->Etiqueta, '.');
        $valorCompleto = $valorPadre ? "$valorPadre.$slug" : $slug;
        $modulo->Valor = $valorCompleto;
        $modulo->save();
//        Permission::findOrCreate($valorCompleto);
        $this->info("[$modulo->ModuloId] => $valorCompleto");
        $hijos = Modulo::query()->where('ModuloPadreId', $modulo->ModuloId)->get();
        if (count($hijos) === 0) {
            $acciones = [
                ['Nombre' => 'Acceder', 'Valor' => 'acceder', 'Descripcion' => 'Permite acceder al módulo'],
                ['Nombre' => 'Crear', 'Valor' => 'crear', 'Descripcion' => 'Permite crear un nuevo registro'],
                ['Nombre' => 'Editar', 'Valor' => 'editar', 'Descripcion' => 'Permite editar un registro'],
                ['Nombre' => 'Eliminar', 'Valor' => 'eliminar', 'Descripcion' => 'Permite eliminar un registro'],
                ['Nombre' => 'Visualizar', 'Valor' => 'visualizar', 'Descripcion' => 'Permite visualizar un registro'],
            ];
            foreach ($acciones as $accion) {
                $accionValor = Str::slug($accion['Nombre'], '.');
                $modulo->acciones()->updateOrCreate([
                    'Valor' => $accionValor,
                ], [
                    'Nombre' => $accion['Nombre'],
                    'Descripcion' => $accion['Descripcion'],
                ]);
                Permission::findOrCreate("{$modulo->Valor}.{$accionValor}");
            }
        }
        foreach ($hijos as $hijo) {
            $this->actualizarValorRecursivo($hijo, $valorCompleto);
        }
    }
}


