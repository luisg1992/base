<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

import { useAppStore } from '@/stores/useAppStore'
import BaseCombo from "@/components/WSelect/WSelect.vue";
import BaseInput from "@/components/WInput/WInput.vue";
import CitaBotones from '@/components/citas/CitaBotones.vue'
import BaseDatePicker from "@/components/WDatePicker/WDatePicker.vue";
import BasePaginatorHTML from '@/components/BasePaginatorHTML.vue';

import MotivoAnulacionDialog from '@/components/citas/MotivoAnulacionDialog.vue'
import ModalPDF from '@/components/ModalPDF.vue';
import {
    verFormatoCita,
    imprimirFormatoCita,
    confirmarYAnularCita,
    verFormatoFUA
} from '@/services/ConsultaExterna/Citas/gestionCitas';

const props = defineProps(['moduloActual']);
const motivoAnulacionRef = ref(null)
const modalPDF = ref(null);
let records = ref([])
let isModalLoading = ref(false)
let currentPage = ref(1)
let perPage = ref(50)
let totalRecords = ref(0)

// Filtros
let appStore = useAppStore();


let FechaFiltro = ref(formatearFechaActual())
let IdEspecialidad = ref(null)
let IdEmpleadoRegistra = ref(null)
let BusquedaPaciente = ref(null)
let empleados = ref([])
let rolesFiltro = ['ADMINISTRADOR DE SISTEMA', 'ADMISIONISTA - CONSULTA EXTERNA']


const totalPaginas = computed(() => {
    return Math.ceil(totalRecords.value / perPage.value)
})

const cambiarPagina = (pagina) => {
    if (pagina >= 1 && pagina <= totalPaginas.value) {
        currentPage.value = pagina
        obtenerCitas()
    }
}

const obtenerCitas = async () => {
    isModalLoading.value = true
    try {
        const { data } = await axios.post('/consulta-externa/citas/WebS_CitasFiltrar', {
            FechaFiltro: FechaFiltro.value,
            IdEspecialidad: IdEspecialidad.value,
            IdEmpleadoRegistra: IdEmpleadoRegistra.value,
            BusquedaPaciente: BusquedaPaciente.value,
            Page: currentPage.value,
            PerPage: perPage.value,
            TipoHistorial: 'CITAS'
        })
        records.value = data.CitasFiltro
        totalRecords.value = data.Total ?? 0

        empleados.value = appStore.personaEmpleadosUsuarioRoles;
    } catch (error) {
        console.error('Error al cargar las citas:', error)
    } finally {
        isModalLoading.value = false
    }
}

function formatearFechaActual() {
    const fecha = new Date()
    const dia = String(fecha.getDate()).padStart(2, '0')
    const mes = String(fecha.getMonth() + 1).padStart(2, '0')
    const anio = fecha.getFullYear()
    return `${dia}/${mes}/${anio}`
}

const onChangeFiltroCitas = () => {
    obtenerCitas()
}

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

const imprimirCita = async (IdCita, validarImpresion) => {
    await imprimirFormatoCita(IdCita, validarImpresion);
};

const notificarCita = (IdCita) => {
    console.log('Notificar cita', IdCita);
};

const verFUA = async (IdCita) => {
    isModalLoading.value = true;

    const resultado = await verFormatoFUA(IdCita);
    isModalLoading.value = false;

    if (resultado?.success && modalPDF.value) {
        showAlert("OPERACIÓN REALIZADA", "LA CITA FUE GENERADA DE FORMA EXITOSA", "success");
        modalPDF.value.openModal(resultado.pdf_base64, 'CITA MÉDICA', 'base64');
    } else {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", resultado?.mensaje || "LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTNÚE CON EL PROCESO DESEADO.", "warning");
    }
};

const anularCita = async (IdCita) => {
    const respuesta = await confirmarYAnularCita(IdCita, motivoAnulacionRef);
    if (respuesta) {
        records.value = records.value.filter(cita => cita.IdCita !== IdCita);
        showAlert("OPERACIÓN REALIZADA", "LA CITA FUE ANULADA DE FORMA EXITOSA", "success");
    } else {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", respuesta?.mensaje || "LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTNÚE CON EL PROCESO DESEADO.", "warning");
    }
};

onMounted(() => {
    obtenerCitas();
})
</script>
<template>
    <div class="col-12">
        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-md-3" v-if="$can(`${moduloActual}.tab.historial.de.citas.seleccionar.empleados`)">
                <BaseCombo v-model="IdEmpleadoRegistra" :options="empleados" label="Empleado" option-label="label"
                    option-value="id" :filter="true" placeholder="Seleccione un Empleado"
                    @update:modelValue="onChangeFiltroCitas" />
            </div>

            <div class="col-md-3 ">
                <BaseCombo v-model="IdEspecialidad" :options="appStore.configuracionEspecialidades" label="Especialidad"
                    option-label="label" option-value="id" :filter="true" placeholder="Seleccione una Especialidad"
                    @update:modelValue="onChangeFiltroCitas" />
            </div>

            <div class="col-md-2">
                <BaseDatePicker v-model="FechaFiltro" label="Fecha" placeholder="Selecciona la fecha"
                    @update:modelValue="onChangeFiltroCitas" />
            </div>

            <div class="col-md-4">
                <BaseInput v-model="BusquedaPaciente" label="Paciente"
                    placeholder="Ingrese nombre del paciente y Presione ENTER" @keyup.enter="obtenerCitas" />
            </div>
        </div>

        <!-- Cargando -->
        <ModalLoader v-if="isModalLoading" />

        <!-- Tabla -->
        <div class="row">
            <div class="col-12">
                <div v-if="records.length" class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <p style="font-size: 12px;" class="mb-0">
                            Se han encontrado <b>({{ records.length }})</b> Registros(s)
                        </p>
                        <BasePaginatorHTML :currentPage="currentPage" :totalRecords="totalRecords" :perPage="perPage"
                            :totalPaginas="totalPaginas" @cambiarPagina="cambiarPagina" />
                    </div>

                    <table class="table table-sm table-hover dataTable no-footer dtr-inline mt-2"
                        style="table-layout: auto;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha Cita</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Responsable</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(cita, index) in records" :key="cita.IdCita">
                                <td>{{ index + 1 }}</td>
                                <td>{{ cita.FechaCita }}<br>
                                    {{ cita.HorarioCita }}
                                </td>
                                <td>H.CL: {{ cita.NroHistoriaClinica }} <br>
                                    ({{ cita.Financiamientos }}) {{ cita.Documento }} <br>
                                    PAC: {{ cita.Paciente }}
                                </td>
                                <td>
                                    MED: {{ cita.Medico }} <br>
                                    CON: {{ cita.Consultorio }}
                                </td>
                                <td>SOL: {{ cita.FechaSolicitud }} ({{ cita.HoraSolicitud }}) <br>
                                    RES: {{ cita.Responsable }}
                                </td>
                                <td>
                                    <CitaBotones :cita="cita" :onVerCita="verCita" :onImprimirCita="imprimirCita"
                                        :onNotificarCita="notificarCita" :onVerFUA="verFUA" :onAnularCita="anularCita"
                                        :moduloActual="moduloActual" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="alert alert-info mt-3 mb-0 text-center">No hay citas para mostrar.</div>
            </div>
        </div>
    </div>
    <ModalPDF ref="modalPDF" />
    <MotivoAnulacionDialog ref="motivoAnulacionRef" />
</template>
