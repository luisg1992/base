<script setup>
import { ref } from 'vue'
import axios from 'axios'

import { useAppStore } from '@/stores/useAppStore';
import BaseModal from '@/components/BaseModal.vue';
import BaseCombo from "@/components/WSelect/WSelect.vue";
import ModalLoader from '@/components/ModalLoader.vue';

// Props
let appStore = useAppStore();
const props = defineProps({
    IdPaciente: {
        type: [Number, String]
    }
})

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false)
let isModalLoading = ref(false)
let IdEspecialidad = ref(null)
let mensajeUbicacionPaciente = ref(null)
let IdCuentaAtencion = ref(null)
let especialidadServicios = ref(null)


const generarCitaControl = async () => {

    if (!props.IdPaciente) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'SELECCIONE UN PACIENTE VÁLIDO PARA EL REGISTRO CORRESPONDIENTE.', 'error');
        return;
    }

    if (!IdEspecialidad.value) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'SELECCIONE UNA ESPACIALIDAD VÁLIDA PARA EL REGISTRO CORRESPONDIENTE.', 'error');
        return;
    }

    const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA?`;
    const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
    if (!confirmado) return;

    isModalLoading.value = true;

    const formData = {
        IdPaciente: props.IdPaciente,
        IdEspecialidad: IdEspecialidad.value,
        IdCuentaAtencion: IdCuentaAtencion.value ?? null,
    };

    try {
        const { data } = await axios.post('/consulta-externa/citas/WebS_InsertarCitaControl', formData);
        isModalLoading.value = false;
        if (data.success) {
            emit('success', data);
            isDialogOpen.value = false
            showAlert("VALIDACIÓN REALIZADA", data.mensaje, "success");
        } else {
            if (data.IdEstadoVincula == 2) {
                isDialogOpen.value = false
                showAlert("OPERACIÓN CANCELADA", data.mensaje, "warning", false, true);
            } else if (data.IdEstadoVincula == 1) {
                emit('success', data);
                isDialogOpen.value = false
            } else {
                showAlert("OPERACIÓN CANCELADA", data.mensaje, "warning", false, true);
            }
        }
    } catch (error) {
        isModalLoading.value = false;
        showAlert("OPERACIÓN CANCELADA", error.message || 'Ocurrió un error', "warning");
    }
};

const openDialog = (mensaje, cuentaAtencionId) => {
    isDialogOpen.value = true;
    mensajeUbicacionPaciente.value = mensaje;
    IdCuentaAtencion.value = cuentaAtencionId;

    especialidadServicios.value = appStore.configuracionEspecialidadesServicios.filter(
        item => item.IdTipoServicio === 1
    );
}


defineExpose({ openDialog })
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :loading="isModalLoading"
               size="modal-sm"
               @close="isDialogOpen = false"
               header="NUEVA CITA CONTROL">

        <div class="row">
            <div class="col-md-12 mb-4 text-center">
                <b style="font-size: 13px;">{{ mensajeUbicacionPaciente }}</b>
            </div>
            <div class="col-md-12 mb-4">
                <BaseCombo v-model="IdEspecialidad" :options="especialidadServicios" label="Especialidad"
                    option-label="label" option-value="id" :filter="true" placeholder="Seleccione una Especialidad" />
            </div>
            <div class="col-md-12">
                <button class="btn btn-outline-primary btn-sm w-100" @click="generarCitaControl()">
                    REGISTRAR CITA CONTROL
                </button>
            </div>
        </div>
    </BaseModal>
</template>
