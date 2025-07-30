<script setup>
import { ref, reactive } from 'vue'
import axios from 'axios'
import Dialog from 'primevue/dialog'
import PacienteNombres from './PacienteNombres.vue'

const emit = defineEmits(['enviarDatos', 'cerrado'])

const visible = ref(false)
const cerradoPorBoton = ref(false)
const modalBusquedaNombres = ref(false)

const form = reactive({
    documentoReferencia: '',
    nroHistoriaClinica: ''
})

const openDialog = () => {
    form.documentoReferencia = ''
    form.nroHistoriaClinica = ''
    visible.value = true
}

const closeDialog = () => {
    visible.value = false
}

const abrirModalNombres = () => {
    modalBusquedaNombres.value = true
}

const cerrarModalNombres = () => {
    modalBusquedaNombres.value = false
}

const seleccionarPaciente = (paciente) => {
    form.nroHistoriaClinica = paciente.NroHistoriaClinica
    modalBusquedaNombres.value = false
}

const emitirDatos = () => {
    if (!form.nroHistoriaClinica) {
        return showAlert(
            'VALIDACIÓN',
            'Ingrese el número de historia clínica.',
            'warning', false, true
        );
    }
    if (!form.documentoReferencia) {
        return showAlert(
            'VALIDACIÓN',
            'Ingrese el documento de referencia.',
            'warning', false, true
        );
    }

    const parametros = {
        nroHistoriaClinica: form.nroHistoriaClinica,
        numerodocumento: form.documentoReferencia,
        tipodocumento: 5
    }

    emit('enviarDatos', parametros)
    closeDialog()
}

const onClose = () => {
    if (!cerradoPorBoton.value) {
        emit('cerrado')
    }
    cerradoPorBoton.value = false
    form.documentoReferencia = ''
    form.nroHistoriaClinica = ''
}

defineExpose({ openDialog, closeDialog })
</script>

<template>
    <Dialog v-model:visible="visible" modal header="BÚSQUEDA DE REFERENCIAS S/D" :style="{ width: '500px' }"
        @hide="onClose">

        <div class="row g-3">
            <div class="col-md-12">
                <label><b>HISTORIA CLÍNICA:</b></label>
                <div class="input-group">
                    <input type="text" class="form-control" v-model="form.nroHistoriaClinica" autocomplete="off" />
                    <button class="btn btn-primary" type="button" @click="abrirModalNombres">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-12">
                <label><b>DOCUMENTO DE REFERENCIA:</b></label>
                <input type="text" class="form-control" v-model="form.documentoReferencia" autocomplete="off" />
            </div>
        </div>

        <div class="mt-4 text-end">
            <button class="btn btn-primary" @click="emitirDatos">
                <i class="pi pi-search"></i> CONSULTA REFERENCIA
            </button>
        </div>

        <PacienteNombres :visible="modalBusquedaNombres" @cerrar="cerrarModalNombres"
            @seleccionar="seleccionarPaciente" />
    </Dialog>
</template>
