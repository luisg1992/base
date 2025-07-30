<?php

namespace Modules\Persona\Observers;


use Modules\Persona\Models\NroHistoria;
use Modules\Persona\Models\Paciente;

class PacienteObserver
{
    /**
     * Handle the Paciente "created" event.
     */
    public function creating(Paciente $paciente): void
    {
        $nroHistoria = NroHistoria::query()->first();
        $nroHistoriaNuevo = $nroHistoria->historia + 1;
        $paciente->NroHistoriaClinica = $nroHistoriaNuevo;
    }
}
