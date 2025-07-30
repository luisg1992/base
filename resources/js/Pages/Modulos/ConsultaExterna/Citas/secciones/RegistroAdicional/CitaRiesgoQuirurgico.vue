<script setup>
import {ref} from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'
import ToggleSwitch from 'primevue/toggleswitch';
import ModalLoader from '@/components/ModalLoader.vue'
import CitaInterconsulta from './CitaInterconsulta.vue'


// Props
const props = defineProps({
    IdPaciente: {
        type: [Number, String]
    }
})

let IdCuentaAtencion = ref('');
let inputIdCuentaAtencionRef = ref(null);
let modalCitaInterconsulta = ref(null)
const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false)
let isModalLoading = ref(false)
let consultaCuenta = ref(false)

let cuentaBusqueda = ref(false)
let interconsultas = ref([])
let laboratorio = ref([])
let imageneologia = ref([])

let switches = ref({})

const buscarCuentaPaciente = async () => {
    consultaCuenta.value = false;
    if (!IdCuentaAtencion.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE NÚMERO DE CUENTA ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.',
            'warning', false, true
        );
    }

    isModalLoading.value = true;
    cuentaBusqueda.value = true;
    interconsultas.value = [];
    laboratorio.value = [];
    imageneologia.value = [];

    try {
        const formData = {
            IdPaciente: props.IdPaciente,
            IdCuentaAtencion: IdCuentaAtencion.value
        };

        const {data} = await axios.post('/consulta-externa/citas/WebS_PreQuirurgico_BuscarFiltro', formData);
        isModalLoading.value = false;

        if (data && Array.isArray(data)) {
            consultaCuenta.value = true;
            interconsultas.value = data.filter(item => item.Tipo === 'Interconsulta');
            laboratorio.value = data.filter(item => item.Tipo === 'Laboratorio');
            imageneologia.value = data.filter(item => item.Tipo === 'Imagenes');

            // ✅ Marcarlos por defecto
            interconsultas.value.forEach(inter => {
                switches.value[inter.IdInterconsulta] = true;
            });

        } else {
            showAlert("OPERACIÓN CANCELADA", "EL NÚMERO DE CUENTA INGRESADO NO CORRESPONDE AL PACIENTE INDICADO, POR FAVOR VERIFIQUE LA INFORMACIÓN INGRESADA.", "warning", false, true);
        }
    } catch (error) {
        showAlert("OPERACIÓN CANCELADA", error.message || 'Ocurrió un error', "warning");
    }
}


const clearSearchDocumento = () => {
    IdCuentaAtencion.value = '';
    inputIdCuentaAtencionRef.value?.focus();
};

const generarCitaRiesgoQuirurgico = async () => {
    isModalLoading.value = true;

    // Filtrar solo las interconsultas marcadas con el switch
    const interconsultasSeleccionadas = interconsultas.value.filter(inter => switches.value[inter.IdInterconsulta]);

    if (interconsultasSeleccionadas.length === 0) {
        isModalLoading.value = false;
        return showAlert(
            "VALIDACIÓN DE DATOS",
            "Debe seleccionar al menos una interconsulta antes de continuar.",
            "warning",
            false,
            true
        );
    }

    // Obtener lista de especialidades únicas desde las seleccionadas
    const especialidades = interconsultasSeleccionadas.reduce((acc, curr) => {
        if (!acc.some(e => e.IdEspecialidad === curr.IdEspecialidad)) {
            acc.push({
                IdEspecialidad: curr.IdEspecialidad,
                IdInterconsulta: curr.IdInterconsulta,
                Especialidad: curr.Especialidad
            });
        }
        return acc;
    }, []);

    // Emitimos la data
    emit('success', especialidades);
    isModalLoading.value = false;
    isDialogOpen.value = false;
};


async function generarNuevaInterconsultaPaciente() {
    isModalLoading.value = true;
    if (!props.IdPaciente) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'REALICE LA CONSULTA DE DATOS NUEVAMENTE, EL PACIENTE NO FUE UBICADO EN EL SISTEMA.',
            'warning', false, true
        );
    }

    if (!IdCuentaAtencion.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE NÚMERO DE CUENTA ES NECESARIO PARA EL FILTRO CORRESPONDIENTE, POR FAVOR INGRESE LA INFORMACIÓN SOLICITADA.',
            'warning', false, true
        );
    }

    isModalLoading.value = false;
    modalCitaInterconsulta.value.openDialog();
}


async function eliminarInterconsultaPaciente(IdInterconsulta) {
    isModalLoading.value = true;
    if (!IdInterconsulta) {
        showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'SELECCIONE UNA INTERCONSULTA VÁLIDA PARA REALIZAR LA ELIMINACIÓN CORRESPONDIENTE.',
            'error'
        );
        isModalLoading.value = false;
        return;
    }

    const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR LA ELIMINACIÓN, PARA LA INFORMACIÓN SELECCIONADA?`;
    const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
    if (!confirmado) return;

    isModalLoading.value = true;
    const formData = {IdInterconsulta: IdInterconsulta};
    try {
        const {data} = await axios.post('/consulta-externa/citas/WebS_EliminarInterconsulta_PreQuirurgico', formData);
        isModalLoading.value = false;

        if (data.success) {
            interconsultas.value = interconsultas.value.filter(inter => inter.IdInterconsulta !== IdInterconsulta);
            showAlert('OPERACIÓN REALIZADA', 'SE ELIMINÓ LA INTERCONSULTA CORRECTAMENTE.', 'success', false);
        } else {
            showAlert("OPERACIÓN CANCELADA", data.mensaje, "warning", false, true);
        }
    } catch (error) {
        isModalLoading.value = false;
        showAlert("OPERACIÓN CANCELADA", error.message || 'Ocurrió un error', "warning");
    }
}


const openDialog = () => {
    consultaCuenta.value = false;
    isDialogOpen.value = true;
    interconsultas.value = [];
    laboratorio.value = [];
    imageneologia.value = [];
    IdCuentaAtencion.value = '';
    inputIdCuentaAtencionRef.value?.focus();
    cuentaBusqueda.value = false
}

const onInterconsultaGenerada = async (data) => {
    isModalLoading.value = true;
    if (data.success) {
        interconsultas.value.push({
            IdInterconsulta: data.IdInterconsulta,
            IdEspecialidad: data.IdEspecialidad,
            Especialidad: data.Especialidad,
            Tipo: data.Tipo,
            Flag: "0",
            CodPrueba: null,
            DescripcionPrueba: null,
            FechaRegImag: null,
            FechaRegLab: null,
            Producto: null,
            PuntoCarga: null,
            ResultadoImag: null,
            ResultadoLab: null,
        });
        isModalLoading.value = false;
    }
};

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               size="modal-md"
               :loading="isModalLoading"
               @close="isDialogOpen = false"
               header="NUEVA CITA RIESGO QUIRÚRGICO">

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex align-items-center mt-2">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="NÚMERO DE CUENTA" aria-label="Text input"
                               v-model="IdCuentaAtencion" ref="inputIdCuentaAtencionRef" autocomplete="off"
                               @keydown.enter="buscarCuentaPaciente">
                        <button class="btn btn-primary waves-effect" type="button" style="height: 2rem !important;"
                                @click="buscarCuentaPaciente">
                            <span class="ti ti-search" style="font-size: 17px;"></span>
                        </button>
                        <button class="btn btn-danger" @click="clearSearchDocumento">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Acordeón de resultados -->
            <div class="accordion" id="accordionResultados">
                <!-- INTERCONSULTAS -->
                <div class="accordion-item" v-if="cuentaBusqueda">
                    <h2 class="accordion-header" id="headingInterconsulta">
                        <div class="accordion-button d-flex justify-content-between align-items-center"
                             style="cursor: default;">
                            <b class="text-primary" style="font-size: 0.80rem !important; margin: 0;">
                                INTERCONSULTAS
                            </b>
                            <button class="btn btn-sm btn-outline-primary ms-2"
                                    @click="generarNuevaInterconsultaPaciente">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </h2>

                    <!-- Siempre visible, sin data-bs-parent -->
                    <div id="collapseInterconsulta" class="accordion-collapse collapse show"
                         aria-labelledby="headingInterconsulta">
                        <div class="accordion-body p-0">
                            <ul class="list-group mt-2">
                                <template v-for="(inter, index) in interconsultas" :key="inter.IdInterconsulta">
                                    <li class="list-group-item"
                                        style="border: none; border-bottom: 1px solid #e6e6e8; font-size: 0.7rem !important; padding: 0.5rem 1.25rem !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger me-2"
                                                        v-if="inter.Flag == 0"
                                                        @click="eliminarInterconsultaPaciente(inter.IdInterconsulta)">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                                <b class="text-dark">
                                                    ESPECIALIDAD: {{ inter.Especialidad || 'SIN ESPECIALIDAD' }}
                                                </b>
                                            </div>

                                            <!-- ToggleSwitch -->
                                            <div>
                                                <ToggleSwitch v-model="switches[inter.IdInterconsulta]"/>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-3" v-if="consultaCuenta">
                    <button class="btn btn-success btn-sm w-100" @click="generarCitaRiesgoQuirurgico()">
                        REGISTRAR CITA RIESGO QUIRÚRGICO
                    </button>
                </div>

                <!-- IMAGENOLOGÍA -->
                <div class="accordion-item" v-if="imageneologia.length">
                    <h2 class="accordion-header" id="headingImagenes">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseImagenes" aria-expanded="false"
                                aria-controls="collapseImagenes">
                            <b class="text-primary" style="font-size: 0.80rem !important;">RESULTADOS DE
                                IMAGENOLOGÍA</b>
                        </button>
                    </h2>
                    <div id="collapseImagenes" class="accordion-collapse collapse" aria-labelledby="headingImagenes">
                        <!-- ❌ data-bs-parent quitado -->
                        <div class="accordion-body p-0">
                            <ul class="list-group mt-2">
                                <template v-for="(img, index) in imageneologia" :key="index">
                                    <li class="list-group-item"
                                        style="border: none; border-bottom: 1px solid #e6e6e8; font-size: 0.7rem !important; padding: 0.5rem 1.25rem !important;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <b class="text-dark">{{ img.Producto || 'SIN DESCRIPCIÓN' }}</b>
                                            <span class="badge text-white"
                                                  :class="img.ResultadoImag === 'SI' ? 'bg-success' : 'bg-danger'">
                                                {{ img.ResultadoImag === 'SI' ? 'CON RESULTADO' : 'PENDIENTE' }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                FECHA: {{ img.FechaRegImag || 'N/A' }}<br>
                                                PUNTO DE CARGA: {{ img.PuntoCarga || 'N/A' }}
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- LABORATORIO -->
                <div class="accordion-item" v-if="laboratorio.length">
                    <h2 class="accordion-header" id="headingLaboratorio">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseLaboratorio" aria-expanded="false"
                                aria-controls="collapseLaboratorio">
                            <b class="text-primary" style="font-size: 0.80rem !important;">RESULTADOS DE LABORATORIO</b>
                        </button>
                    </h2>
                    <div id="collapseLaboratorio" class="accordion-collapse collapse"
                         aria-labelledby="headingLaboratorio">
                        <!-- ❌ data-bs-parent quitado -->
                        <div class="accordion-body p-0">
                            <ul class="list-group mt-2">
                                <template v-for="(lab, index) in laboratorio" :key="index">
                                    <li class="list-group-item" v-if="lab.DescripcionPrueba"
                                        style="border: none; border-bottom: 1px solid #e6e6e8; font-size: 0.7rem !important; padding: 0.5rem 1.25rem !important;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <b class="text-dark">{{ lab.DescripcionPrueba || 'SIN DESCRIPCIÓN' }}</b>
                                            <span class="badge text-white"
                                                  :class="lab.ResultadoLab === 'SI' ? 'bg-success' : 'bg-danger'">
                                                {{ lab.ResultadoLab === 'SI' ? 'CON RESULTADO' : 'PENDIENTE' }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                FECHA: {{ lab.FechaRegLab || 'N/A' }}<br>
                                                CÓDIGO: {{ lab.CodPrueba || 'N/A' }}
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CitaInterconsulta ref="modalCitaInterconsulta" :IdPaciente="IdPaciente" :IdCuentaAtencion="IdCuentaAtencion"
                           @success="onInterconsultaGenerada"/>
    </BaseModal>
</template>
