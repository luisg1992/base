<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import ModalLoader from '@/components/ModalLoader.vue'
import ModalPDF from '@/components/ModalPDF.vue';
import { verFormatoCita } from '@/services/ConsultaExterna/Citas/gestionCitas';

let cuposDisponibles = ref([])
let searchTerm = ref('')

const modalPDF = ref(null);
const props = defineProps({
    recordId: {
        type: [Number, String]
    },
    itemSeleccion: Object
})

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false)
let isModalLoading = ref(false)

const obtenerCuposDisponibles = async () => {
    isModalLoading.value = true
    if (!props.recordId) {
        cuposDisponibles.value = []
        isModalLoading.value = false
        return
    }
    try {
        const { data } = await axios.post('/consulta-externa/citas/WebS_Programacion_CuposDisponibles', {
            IdEspecialidad: props.recordId
        })
        cuposDisponibles.value = data
    } catch (error) {
        console.error('Error al cargar las citas:', error)
    } finally {
        isModalLoading.value = false
    }
}

const cuposFiltrados = computed(() => {
    if (!searchTerm.value) return cuposDisponibles.value
    const term = searchTerm.value.toLowerCase()
    return cuposDisponibles.value.filter(cupo =>
        (cupo.Servicio ?? '').toLowerCase().includes(term) ||
        (cupo.Medico ?? '').toLowerCase().includes(term) ||
        (cupo.Fecha ?? '').toLowerCase().includes(term)
    )
})


const generarCita = async (parametros) => {
    const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA?`;
    const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
    if (!confirmado) return;

    isModalLoading.value = true;

    const formData = {
        IdPaciente: props.itemSeleccion.IdPaciente,
        Tipo: props.itemSeleccion.Tipo,
        IdIdentificador: props.itemSeleccion.IdSeleccion,
        IdFuentefinanciamiento: props.itemSeleccion.idfuentefinanciamiento,
        IdTipoFinanciamiento: props.itemSeleccion.IdTipoFinanciamiento,
        IdProgramacion: parametros.IdProgramacion,
    };

    try {
        const { data } = await axios.post('/consulta-externa/citas/WebS_InsertarCita_Interconsulta_CitaControl', formData);
        isModalLoading.value = false;
        if (data.success) {
            verCita(data.IdCita);
            emit('success')
            isDialogOpen.value = false
            showAlert("VALIDACIÓN REALIZADA", data.mensaje, "success");
        } else {
            showAlert("OPERACIÓN CANCELADA", data.mensaje, "warning");
        }
    } catch (error) {
        isModalLoading.value = false;
        showAlert("OPERACIÓN CANCELADA", error.message || 'Ocurrió un error', "warning");
    }
};


const verCita = async (IdCita) => {
    isModalLoading.value = true;

    const resultado = await verFormatoCita(IdCita);
    isModalLoading.value = false;

    if (resultado?.success && modalPDF.value) {
        showAlert("OPERACIÓN REALIZADA", "LA CITA FUE GENERADA DE FORMA EXITOSA", "success");
        modalPDF.value.openModal(resultado.pdf_base64, 'CITA MÉDICA', 'base64');
    } else {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", resultado?.mensaje || "LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTNÚE CON EL PROCESO DESEADO.", "warning");
    }
};


const openDialog = () => {
    isDialogOpen.value = true
    obtenerCuposDisponibles()
}

defineExpose({ openDialog })
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :recordId="props.recordId"
               :loading="isModalLoading"
               size="modal-sm"
               @close="isDialogOpen = false"
        header="CUPOS DISPONIBLES">

        <BaseInput v-model="searchTerm" placeholder="Buscar por servicio, médico o fecha..." class="mb-2" />

        <ul class="list-group">
            <template v-if="cuposFiltrados.length">
                <template v-for="(progMedSer, index) in cuposFiltrados" :key="index">
                    <li class="list-group-item" style="font-weight: 600; font-size: 11.5px;">
                        <span class="me-2 text-success">
                            <b>{{ index + 1 }}. {{ progMedSer.Servicio }} - ({{ progMedSer.Fecha }})</b>
                        </span><br>
                        <b>MÉDICO: {{ progMedSer.Medico }}</b>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-primary">( {{ progMedSer.NroCupos }} ) CUPOS DISPONIBLES</small>
                            <button class="btn btn-sm btn-outline-success" @click="generarCita(progMedSer)">
                                CITAR
                            </button>
                        </div>
                    </li>
                </template>
            </template>
            <template v-else>
                <li class="list-group-item text-center text-muted">No hay coincidencias con el filtro.</li>
            </template>
        </ul>
    </BaseModal>
    <ModalPDF ref="modalPDF" />
</template>
