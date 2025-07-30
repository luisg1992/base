<script setup>
import { ref } from 'vue'
import axios from 'axios'

import { useAppStore } from '@/stores/useAppStore'
import BaseCombo from "@/components/WSelect/WSelect.vue";
import BaseDatePicker from "@/components/WDatePicker/WDatePicker.vue";
import ModalPDF from '@/components/ModalPDF.vue'

import { verFormatoFUA, imprimirFormatoFUA, generarLoteFUA } from '@/services/ConsultaExterna/Citas/gestionCitas'
import TreeTable from 'primevue/treetable'
import Column from 'primevue/column'

const modalPDF = ref(null)
const nodes = ref([])

const isModalLoading = ref(false)
const appStore = useAppStore()
const FechaFiltro = ref(null)
const IdEspecialidad = ref(null)

let selectedKey = ref([])
 
const ejecutarValidacionCuentasPendientes = async (IdPaciente) => {
    const { data } = await axios.post('/consulta-externa/citas/WebS_Validar_Cuentas_Pendientes', {
        IdPaciente: IdPaciente
    });

    if (data.success) {
        isModalLoading.value = false;
        showAlert(
            "VALIDACIÓN REALIZADA",
            data.mensaje,
            "warning",
            true,
            true
        );
        return data.success;
    }
    return false;
};

const obtenerCitas = async () => {
    if (!FechaFiltro.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE FECHA ES NECESARIA PARA PODER REALIZAR LA BÚSQUEDA NECESARIA, POR FAVOR VERIFIQUE LA INFORMACIÓN INGRESADA.',
            'warning', false, true
        );
    }

    if (IdEspecialidad.value) {
        isModalLoading.value = true
        try {
            const { data } = await axios.post('/consulta-externa/citas/WebS_CitasFiltrar', {
                FechaFiltro: FechaFiltro.value,
                IdEspecialidad: IdEspecialidad.value,
                TipoHistorial: 'FUA'
            })
            nodes.value = data.CitasFiltro || []
        } catch (error) {
            console.error('Error al cargar las citas:', error)
        } finally {
            isModalLoading.value = false
        }
    } else {
        nodes.value = []
    }
}

const onChangeFiltroCitas = () => {
    obtenerCitas()
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
const esPadre = (node) => !!node.children;
const accionPadre = async (node) => {
    const seleccionados = selectedKey.value || {};
    const hijosSeleccionados = (node.children || []).filter(hijo => seleccionados[hijo.key]);

    if (hijosSeleccionados.length === 0) {
        showAlert("SIN CITAS SELECCIONADAS", "No se han seleccionado citas para imprimir.", "warning");
        return;
    }

    showAlert("GENERANDO REGISTRO DE CITAS", `<span id="alert-message">Preparando impresión...</span>`, "info", true);

    const externalIds = [];

    for (let i = 0; i < hijosSeleccionados.length; i++) {
        // Desestructurar cita y paciente
        const { IdCita, IdPaciente } = hijosSeleccionados[i];

        const success = await ejecutarValidacionCuentasPendientes(IdPaciente);
        if (!success) {
            const alertMessage = document.getElementById('alert-message');
            if (alertMessage) {
                alertMessage.innerText = `Procesando cita ID: ${IdCita} (${i + 1} de ${hijosSeleccionados.length})...`;
            }

            // Aquí puedes usar IdPaciente si lo necesitas en la función
            const externalId = await imprimirFormatoFUA(IdCita, false, 0, false);
            if (externalId) {
                externalIds.push(externalId);
            }

            await sleep(300);
        }
    }

    if (externalIds.length > 0) {
        const alertMessage = document.getElementById('alert-message');
        if (alertMessage) {
            alertMessage.innerText = `Generando lote de impresión con ${externalIds.length} registros...`;
        }
        await generarLoteFUA(externalIds);
    }

    showAlert("IMPRESIÓN PROCESADA", "SE HAN GENERADOS LAS TRAMAS DE IMPRESIÓN, SE GENERARÁ EL ENVÍO NECESARIO PARA LA IMPRESIÓN MASIVA DE LA TRAMA DE FUAS", "success");
};

const verFUA = async (data) => {
    isModalLoading.value = true;
    const success = await ejecutarValidacionCuentasPendientes(data.IdPaciente);
    if (!success) {
        const resultado = await verFormatoFUA(data.IdCita);
        isModalLoading.value = false;

        if (resultado?.success && modalPDF.value) {
            showAlert("OPERACIÓN REALIZADA", "LA CITA FUE GENERADA DE FORMA EXITOSA", "success");
            modalPDF.value.openModal(resultado.pdf_base64, 'CITA MÉDICA', 'base64');
        } else {
            showAlert("LA SOLICITUD NO PUDO SER PROCESADA", resultado?.mensaje || "LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTNÚE CON EL PROCESO DESEADO.", "warning");
        }
    }
};

</script>

<template>
    <ModalLoader v-if="isModalLoading" />

    <div class="col-12">
        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-md-3">
                <BaseCombo v-model="IdEspecialidad" :options="appStore.configuracionEspecialidades" label="Especialidad"
                    option-label="label" option-value="id" :filter="true" placeholder="Seleccione una Especialidad"
                    @update:modelValue="onChangeFiltroCitas" />
            </div>

            <div class="col-md-2">
                <BaseDatePicker v-model="FechaFiltro" label="Fecha" placeholder="Selecciona la fecha"
                    @update:modelValue="onChangeFiltroCitas" :minDate="new Date()" />
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div v-if="nodes.length" class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                    <TreeTable :value="nodes" filterMode="lenient" selectionMode="checkbox" v-model:selectionKeys="selectedKey">
                        <Column field="paciente" header="Paciente / Consultorio" style="width: 40%" expander />
                        <Column field="cita" header="Horario" style="width: 20%" class="text-end" />
                        <Column field="medico" header="Médico / Tipo" style="width: 30%" class="text-end">
                            <template #body="slotProps">
                                <span>{{ slotProps.node.data.medico }}</span>
                            </template>
                        </Column>

                        <!-- ✅ Columna de acciones solo para nodos padre -->
                        <Column header="Acciones" style="width: 10%">
                            <template #body="{ node }">
                                <!-- Botón para padres -->
                                <template v-if="esPadre(node)">
                                    <button class="btn btn-outline-primary btn-sm" @click="accionPadre(node)">
                                        IMPRIMIR SELECCIÓN
                                    </button>
                                </template>

                                <!-- Botón para hijos solo si IdFuenteFinanciamiento !== 1 -->
                                <template v-else-if="node.IdFuenteFinanciamiento !== 1">
                                    <button class="btn btn-outline-secondary btn-sm" @click="verFUA(node)">
                                        <i class="ti ti-printer"></i>
                                    </button>
                                </template>
                            </template>
                        </Column>
                    </TreeTable>
                </div>
                <div v-else class="alert alert-info mt-3 mb-0 text-center">No hay citas para mostrar.</div>
            </div>
        </div>
    </div>

    <ModalPDF ref="modalPDF" />
</template>
