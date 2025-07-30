<script setup>
import { ref } from 'vue'
import axios from 'axios'

import { useAppStore } from '@/stores/useAppStore';
import BaseModal from '@/components/BaseModal.vue';
import BaseCombo from "@/components/WSelect/WSelect.vue";

// Props
let appStore = useAppStore();
const props = defineProps({
    IdPaciente: {
        type: [Number, String]
    },
    IdCuentaAtencion: {
        type: [Number, String]
    }
})

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false)
let isModalLoading = ref(false)
let IdEspecialidad = ref(null)
let especialidadServicios = ref(null)


const generarCitaInterconsulta = async () => {
    if (!props.IdPaciente) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'SELECCIONE UN PACIENTE VÁLIDO PARA EL REGISTRO CORRESPONDIENTE.', 'error');
        return;
    }

    if (!props.IdCuentaAtencion) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE NÚMERO DE CUENTA ES NECESARIO PARA EL FILTRO CORRESPONDIENTE, POR FAVOR INGRESE LA INFORMACIÓN SOLICITADA.',
            'warning', false, true
        );
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
        IdCuentaAtencion: props.IdCuentaAtencion,
        IdPaciente: props.IdPaciente,
        IdEspecialidad: IdEspecialidad.value,
    };

    try {
        const { data } = await axios.post('/consulta-externa/citas/WebS_InsertarInterconsulta_PreQuirurgico', formData);
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

const openDialog = () => {
    isDialogOpen.value = true;
    especialidadServicios.value = appStore.configuracionEspecialidadesServicios.filter(
        item => item.IdTipoServicio === 1
    );
}


defineExpose({ openDialog })
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               size="modal-sm"
               :loading="isModalLoading"
               @close="isDialogOpen = false"
               header="NUEVO REGISTRO">

        <div class="row">
            <div class="col-md-12 mb-4">
                <BaseCombo v-model="IdEspecialidad" :options="especialidadServicios" label="Especialidad"
                    option-label="label" option-value="id" :filter="true" placeholder="Seleccione una Especialidad" />
            </div>
            <div class="col-md-12">
                <button class="btn btn-outline-primary btn-sm w-100" @click="generarCitaInterconsulta()">
                    REGISTRAR CITA POR INTERCONSULTA
                </button>
            </div>
        </div>
    </BaseModal>
</template>
