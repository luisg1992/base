<script setup>
defineProps({
    cita: {
        type: Object,
        required: true
    },
    onVerCita: {
        type: Function,
        required: true
    },
    onImprimirCita: {
        type: Function,
        required: true
    },
    onNotificarCita: {
        type: Function,
        required: true
    },
    onVerFUA: {
        type: Function,
        required: true
    },
    onAnularCita: {
        type: Function,
        required: true
    },
    onGenerarCitaProcedimiento: {
        type: Function,
        required: false
    },
    moduloActual: {
        type: String
    },
    validacionCuentasPendientes: {
        type: Boolean,
        default: false // 👈 valor por defecto
    },
    validarProcedimiento: {
        type: Boolean,
        default: false // 👈 valor por defecto
    },
    IdFuenteFinanciamiento: {
        type: Number
    }
})
</script>

<template>
    <div class="text-end" style="font-size: 11px !important;">
        <button v-if="validarProcedimiento && cita.Validar == '0' && cita?.IdFuenteFinanciamiento !== '1' &&
            $can(`${moduloActual}.tab.gestionar.citas.registrar.cita`)" class="btn btn-xs btn-icon btn-primary me-1"
            @click="onGenerarCitaProcedimiento(cita)">
            <i class="ti ti-calendar text-white"></i>
        </button>

        <button v-if="$can(`${moduloActual}.visualizar.cita`)" class="btn btn-xs btn-icon btn-info me-1"
            @click="onVerCita(cita.IdCita)">
            <i class="ti ti-ticket text-white"></i>
        </button>

        <button v-if="$can(`${moduloActual}.imprimir.cita`) && cita.Validar == '1'"
            class="btn btn-xs btn-icon btn-dark me-1" @click="onImprimirCita(cita.IdCita, true)">
            <i class="ti ti-printer text-white"></i>
        </button>

        <button v-if="$can(`${moduloActual}.notificar.sms.cita`) && cita.Validar == '1'"
            class="btn btn-xs btn-icon btn-warning me-1" @click="onNotificarCita(cita.IdCita)">
            <i class="ti ti-message text-white"></i>
        </button>

        <button
            v-if="!validacionCuentasPendientes && (cita?.IdFuenteFinanciamiento !== '1' && IdFuenteFinanciamiento != 1) && $can(`${moduloActual}.visualizar.fua`)"
            class="btn btn-xs btn-icon btn-success me-1" @click="onVerFUA(cita.IdCita)">
            <i class="ti ti-file text-white"></i>
        </button>

        <button v-if="$can(`${moduloActual}.anular.cita`) && cita.Validar == '1'" class="btn btn-xs btn-icon btn-danger"
            @click="onAnularCita(cita.IdCita)">
            <i class="ti ti-ban text-white"></i>
        </button>
    </div>

</template>
