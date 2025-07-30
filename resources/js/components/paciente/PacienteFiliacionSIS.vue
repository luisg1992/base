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
    nroHistoriaClinica: '',
    nroDocumento: '',
    nombreCompleto: '',
    disa: '230',
    tipoFormato: 'E',
    nroContrato: '',
    correlativo: ''
})

const openDialog = () => {
    form.nroHistoriaClinica = ''
    form.disa = '230'
    form.tipoFormato = 'E'
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
    form.nroDocumento = paciente.NroDocumento
    form.nombreCompleto = paciente.NombreCompleto
    modalBusquedaNombres.value = false
}

const emitirDatos = async () => {
    const parametros = {
        opcion: "2",
        TipoDocumento: "",
        NroDocumento: form.nroDocumento,
        nroHistoriaClinica: form.nroHistoriaClinica,
        disa: form.disa,
        tipoFormato: form.tipoFormato,
        nroContrato: form.nroContrato,
        correlativo: form.correlativo,
    }
    await obtenerDatosSis(parametros);
}

async function obtenerDatosSis(parametros = {}) {
    try {
        if (!parametros.nroHistoriaClinica) {
            return showAlert('VALIDACIÓN', 'Ingrese el número de historia clínica.', 'warning', false, true);
        }

        if (!parametros.disa || !parametros.tipoFormato || !parametros.nroContrato) {
            return showAlert('VALIDACIÓN', 'Complete los campos requeridos.', 'warning', false, true);
        }

        showAlert("Consultando SIS...", "Espere un momento", "warning", true);
        const response = await axios.post('/core/servicios/obtenerDatosFiliacionSis', parametros);

        if (response?.data.respuesta == 1) {
            showAlert("CONSULTA EXITOSA", "Consulta realizada correctamente.", "success");
            cerradoPorBoton.value = true
            emit('enviarDatos', response?.data?.data, true)
            closeDialog()
        } else {
            if (response?.data?.codigo == '14') {
                const confirmado = await showAlertConfirmacion('Paciente sin SIS', '¿Desea continuar como particular?', 'warning');
                if (confirmado) {
                    emit('enviarDatos', response?.data?.data, false)
                    closeDialog()
                }
            } else {
                showAlert("VALIDACIÓN", response?.data?.descripcion, "warning", false, true);
            }
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

const onClose = () => {
    if (!cerradoPorBoton.value) {
        emit('cerrado')
    }
    cerradoPorBoton.value = false

    Object.assign(form, {
        nroHistoriaClinica: '',
        disa: '',
        tipoFormato: '',
        nroContrato: '',
        correlativo: '',
        nroDocumento: '',
        nombreCompleto: ''
    })
}

defineExpose({ openDialog, closeDialog })
</script>

<template>
    <Dialog v-model:visible="visible" modal header="BÚSQUEDA DE PACIENTES" :style="{ width: '500px' }" @hide="onClose">
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
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-3">
                <input type="text" class="form-control" v-model="form.disa" maxlength="4" autocomplete="off" />
            </div>

            <div class="col-md-2">
                <input type="text" class="form-control" v-model="form.tipoFormato" maxlength="4" autocomplete="off" />
            </div>

            <div class="col-md-5">
                <input type="text" class="form-control" v-model="form.nroContrato" maxlength="15" autocomplete="off" />
            </div>

            <div class="col-md-2">
                <input type="text" class="form-control" v-model="form.correlativo" maxlength="14" autocomplete="off" />
            </div>
        </div>

        <div class="mt-4 text-end">
            <button class="btn btn-primary" @click="emitirDatos">
                <i class="fas fa-search me-2"></i> CONSULTA PACIENTE
            </button>
        </div>

        <PacienteNombres :visible="modalBusquedaNombres" @cerrar="cerrarModalNombres"
            @seleccionar="seleccionarPaciente" />
    </Dialog>
</template>
