<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'
import BaseCombo from "@/components/WSelect/WSelect.vue";
import {useAppStore} from '@/stores/useAppStore'
import WButton from "@/components/WButton/WButton.vue";
import {showToastSuccess} from "../../../../utils/alert.js";
import WDatePicker from "../../../../components/WDatePicker/WDatePicker.vue";
import WInput from "../../../../components/WInput/WInput.vue";

import Select from "primevue/select";
import WTimePicker from "../../../../components/WTimePicker/WTimePicker.vue";
import Tabs from "primevue/tabs";
import TabPanels from "primevue/tabpanels";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanel from "primevue/tabpanel";
import ModalFormulario from "./AdmisionEmergenciaNN.vue";
import FormularioTriaje from "../TriajeEmergencia/components/FormularioTriaje.vue";
import BuscarPaciente from "../TriajeEmergencia/components/BuscarPaciente.vue";
import ShowPaciente from "../TriajeEmergencia/components/ShowPaciente.vue";

let appStore = useAppStore();
const props = defineProps({
    triajeId: {
        type: Number,
        default: null
    },
    recordId: [Number, null],
    viewRecord: Boolean,
    viewRecordTriaje: Boolean,
    resource: String,
    moduloActual: String,
    paciente: Object,
    accion: String
})

const emit = defineEmits(['close', 'success']);

let isDialogOpen = ref(false);
let loading = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let accionForm = ref('registrar');

let idPaciente = ref(null)
let IdTipoSexo = ref(null)
let loadingDatosPaciente = ref(false)
let tipoOrigenAtencion = ref([]);
let servicios = ref([]);
let tieneCuentasPendientes = ref(false);
let tieneCuentasPendientesMensaje = ref();

let debounceTimeout = null;
let establecimientos = ref([]);
let loadingEstablecimientos = ref(false);
let diagnosticos = ref([]);

let showLugarEvento = ref(false);
let showTipoEvento = ref(false);
let showSeguridad = ref(false);
let showRelacionAgresorVictima = ref(false);
let showClaseAccidente = ref(false);
let showTipoVehiculo = ref(false);
let showTipoTransporte = ref(false);
let showUbicacionLesionado = ref(false);
let showPosicionLesionadoALAB = ref(false);
let showGrupoOcupacionalALAB = ref(false);
let showTipoAgenteAgan = ref(false);
let showReferencia = ref(false);
let isViewMode = computed(() => props.viewRecord);
let title = ref();
let errors = ref({});
let form = ref({});
let formTriaje = ref({});
let datosPaciente = ref(null);
const initForm = () => {
    const now = new Date();

    const horas = now.getHours().toString().padStart(2, '0')
    const minutos = now.getMinutes().toString().padStart(2, '0')
    const horaLocal = `${horas}:${minutos}`

    const today = new Date()
    today.setHours(0, 0, 0, 0)

    title.value = '';
    errors.value = {};
    form.value = {
        FechaIngreso: today, // yyyy-mm-dd
        HoraIngreso: horaLocal, // HH:MM
        RecienNacido: null,
        NombreAcompaniante: null,
        IdTipoReferencia: null,
        IdEstablecimientoReferencia: null,
        NumeroReferencia: null,
        IdEmpleado: null,
        IdPaciente: null,
        IdServicio: 2,
        IdOrigenAtencion: null,
        IdServicioIngreso: null,
        IdMedicoIngreso: null,
        IdTipoGravedad: 1,
        IdTipoFinanciamiento: null,
        IdFuenteFinanciamiento: null,
        CodigoPrestacionSIS: null,
        IdCausaExternaMorbilidad: null,
        IdLugarEvento: null,
        IdTipoEvento: null,
        IdSeguridad: null,
        IdRelacionAgresorVictima: null,
        IdClaseAccidente: null,
        IdTipoVehiculo: null,
        IdTipoTransporte: null,
        IdUbicacionLesionado: null,
        IdPosicionLesionadoALAB: null,
        IdGrupoOcupacionalALAB: null,
        IdTipoAgenteAGAN: null,
        IdTriajeEmergencia: props.triajeId,
        diagnosticos: []
    }
    formTriaje.value = {
        CodigoTriaje: null,
        IdEmpleado: null,
        IdPaciente: null,
        IdFuenteFinanciamiento: null,
        IdTipoFinanciamiento: null,
        IdServicio: null,
        IdTipoGravedad: null,

        TriajeFecha: null,
        TriajeHora: null,
        TriajeAnios: null,
        TriajeMeses: null,
        TriajeDias: null,

        TriajeEscalaDolor: null,
        TriajeSinRespiratorio: null,

        TriajePresion: null,
        TriajePresionSis: null,
        TriajePresionDia: null,
        TriajeFrecCardiaca: null,
        TriajeFrecRespiratoria: null,
        TriajeSaturacionOxigeno: null,
        TriajeTemperatura: null,
        TriajePeso: null,
        TriajeTalla: null,
        TriajeIMC: null,
        TriajeObservacion: null,
        Estacion: null,

        IdMedicoTriaje: null,
        IdMedicoTopico: null,

        Acomp: null,
        Diag_1: null,
        Diag_2: null,
        Diag_3: null,
        EstadoTriaje: null,
        idCuentaAtencion: null,

        IdTipoDocTriaje: null,
        NroDocTriaje: null,
        ApellidoPaternoTriaje: null,
        ApellidoMaternoTriaje: null,
        PrimerNombreTriaje: null,
        SegundoNombreTriaje: null,
        TercerNombreTriaje: null,
        FechaNacimientoTriaje: null,

        IdFormaIngreso: null,
        IdEstadoIngreso: null,

        IdMotivoIngreso: null,
        EstablecimientoSalud: null,
        IdTipoTriajeDiferenciado: null,
        diagnosticos: [],
        HoraInicio: null,
        HoraTermino: null,
        Estado: 1
    }
    tieneCuentasPendientes.value = false;
    tieneCuentasPendientesMensaje.value = null;
}

const handleOpen = async () => {
    initForm()
    loading.value = true;
    tipoOrigenAtencion.value = appStore.tablasCache.tipoOrigenAtencionCache.filter(row => row.IdTipoServicio === 2);
    if (props.accion === 'admitir') {
        title.value = 'Admitir admisión de emergencia';
        await obtenerInformacionTriaje(props.triajeId);
        const {data} = await axios.get(`/personas/pacientes/record/${formTriaje.value.IdPaciente}`);
        datosPaciente.value = data.data;
        form.value.IdPaciente = formTriaje.value.IdPaciente;
        form.value.IdTriajeEmergencia = props.triajeId;
    } else if (props.accion === 'ver') {
        title.value = 'Ver admisión de emergencia';
        await obtenerInformacionAdmision(props.recordId);
        await obtenerInformacionTriaje(form.value.IdTriajeEmergencia);
        const {data} = await axios.get(`/personas/pacientes/record/${formTriaje.value.IdPaciente}`);
        datosPaciente.value = data.data;
    } else {
        title.value = 'Registrar admisión de emergencia';
        datosPaciente.value = props.paciente;
        form.value.IdPaciente = datosPaciente.value.id;
        formTriaje.value.IdPaciente = datosPaciente.value.id;
        formTriaje.value.IdFuenteFinanciamiento = datosPaciente.value.IdFuenteFinanciamiento;
        formTriaje.value.IdTipoDocTriaje = datosPaciente.value.IdDocIdentidad;
        formTriaje.value.NroDocTriaje = datosPaciente.value.NroDocumento;
        formTriaje.value.ApellidoPaternoTriaje = datosPaciente.value.ApellidoPaterno;
        formTriaje.value.ApellidoMaternoTriaje = datosPaciente.value.ApellidoMaterno;
        formTriaje.value.PrimerNombreTriaje = datosPaciente.value.PrimerNombre;
        formTriaje.value.SegundoNombreTriaje = datosPaciente.value.SegundoNombre ?? null;
        formTriaje.value.TercerNombreTriaje = datosPaciente.value.TercerNombre ?? null;
        formTriaje.value.FechaNacimientoTriaje = datosPaciente.value.FechaNacimiento;
        formTriaje.value.IdFuenteFinanciamiento = datosPaciente.value.IdFuenteFinanciamiento;
        const now = new Date();
        formTriaje.value.HoraInicio = now.toTimeString().slice(0, 8);
    }
    loading.value = false;
}

const obtenerInformacionTriaje = async (triajeId) => {
    const {data} = await axios.get(`/emergencia/triaje-emergencia/record/${triajeId}`)
    Object.assign(formTriaje.value, data.data)
    const presion = formTriaje.value.TriajePresion || '';
    const partes = presion.split('/');
    formTriaje.value.TriajePresionSis = partes[0]?.trim() || null;
    formTriaje.value.TriajePresionDia = partes[1]?.trim() || null;
    formTriaje.value.TriajeSaturacionOxigeno = formTriaje.value.TriajeSaturacionOxigeno?.trim() || '';
}

const obtenerInformacionAdmision = async (admisionId) => {
    const {data} = await axios.get(`/emergencia/admision-emergencia/record/${admisionId}`)
    Object.assign(form.value, data.data)
}

const onChangeOrigenAtencion = () => {
    let origenAtencion = appStore.tablasCache.tipoOrigenAtencionCache.find(row => row.id === form.value.IdOrigenAtencion);
    form.value.IdTipoReferencia = null;
    form.value.IdEstablecimientoReferencia = null;
    form.value.NumeroReferencia = null;
    showReferencia.value = origenAtencion.Codigo === 'R' || origenAtencion.Codigo === 'C'
}

const onChangeCausaExternaMorbilidad = () => {
    form.value.IdLugarEvento = null;
    form.value.IdTipoEvento = null;
    form.value.IdSeguridad = null;
    form.value.IdRelacionAgresorVictima = null;
    form.value.IdClaseAccidente = null;
    form.value.IdTipoVehiculo = null;
    form.value.IdTipoTransporte = null;
    form.value.IdUbicacionLesionado = null;
    form.value.IdPosicionLesionadoALAB = null;
    form.value.IdGrupoOcupacionalALAB = null;
    form.value.IdTipoAgenteAGAN = null;
    showOptions();
}

const showOptions = () => {
    showLugarEvento.value = false;
    showTipoEvento.value = false;
    showSeguridad.value = false;
    showRelacionAgresorVictima.value = false;
    showClaseAccidente.value = false;
    showTipoVehiculo.value = false;
    showTipoTransporte.value = false;
    showUbicacionLesionado.value = false;
    showPosicionLesionadoALAB.value = false;
    showGrupoOcupacionalALAB.value = false;
    showTipoAgenteAgan.value = false;

    if (form.value.IdCausaExternaMorbilidad === 1) {
        showLugarEvento.value = true;
        showTipoEvento.value = true;
        showRelacionAgresorVictima.value = true;
    } else if (form.value.IdCausaExternaMorbilidad === 2) {
        showLugarEvento.value = true;
        showTipoEvento.value = true;
        showSeguridad.value = true;
        showClaseAccidente.value = true;
        showTipoVehiculo.value = true;
        showTipoTransporte.value = true;
        showUbicacionLesionado.value = true;
    } else if (form.value.IdCausaExternaMorbilidad === 3) {
        showLugarEvento.value = true;
        showTipoEvento.value = true;
        showSeguridad.value = true;
        showClaseAccidente.value = true;
        showTipoVehiculo.value = true;
        showTipoTransporte.value = true;
        showUbicacionLesionado.value = true;
    } else if (form.value.IdCausaExternaMorbilidad === 4) {
        showLugarEvento.value = true;
    } else if (form.value.IdCausaExternaMorbilidad === 5) {
        showLugarEvento.value = true;
        showTipoEvento.value = true;
        showSeguridad.value = true;
        showPosicionLesionadoALAB.value = true;
        showGrupoOcupacionalALAB.value = true;
    } else if (form.value.IdCausaExternaMorbilidad === 6) {
        showLugarEvento.value = true;
        showTipoEvento.value = true;
        showSeguridad.value = true;
        showTipoAgenteAgan.value = true;
    } else if (form.value.IdCausaExternaMorbilidad === 7) {
        showTipoEvento.value = true;
        showSeguridad.value = true;
        showTipoAgenteAgan.value = true;
    } else if (form.value.IdCausaExternaMorbilidad === 8) {
        showTipoEvento.value = true;
        showSeguridad.value = true;
        showTipoAgenteAgan.value = true;
    } else if (form.value.IdCausaExternaMorbilidad === 9) {
        showTipoEvento.value = true;
        showSeguridad.value = true;
        showTipoAgenteAgan.value = true;
    }
}

const fetchOptionsEstablecimientos = async (query) => {
    if (!query) {
        establecimientos.value = [];
        return;
    }
    loadingEstablecimientos.value = true;
    try {
        const response = await axios.post('/filtrar_catalogo_establecimientos', {buscar: query});
        establecimientos.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingEstablecimientos.value = false;
    }
}

const onFilterEstablecimientos = (event) => {
    if (event.value.length > 3) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsEstablecimientos(event.value);
        }, 500);
    }
}

const onChangeMotivoIngreso = (data) => {
    form.value.IdTipoGravedad = data.IdTipoGravedad;
    form.value.IdServicioIngreso = data.IdServicio;
}

const onChangeMedicoTopico = (data) => {
    form.value.IdMedicoIngreso = data.IdMedicoTopico;
}

const onSubmit = async () => {

    if (formTriaje.value.TriajePresionSis === '' || formTriaje.value.TriajePresionSis === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo PRESIÓN ARTERIAL es requerido', 'error');
    }
    if (formTriaje.value.TriajePresionDia === '' || formTriaje.value.TriajePresionDia === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo PRESIÓN ARTERIAL es requerido', 'error');
    }
    if (formTriaje.value.TriajeFrecCardiaca === '' || formTriaje.value.TriajeFrecCardiaca === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo FRECUENCIA CARDIACA es requerido', 'error');
    }
    if (formTriaje.value.TriajeFrecRespiratoria === '' || formTriaje.value.TriajeFrecRespiratoria === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo FRECUENCIA RESPIRATORIA es requerido', 'error');
    }
    if (formTriaje.value.TriajeSaturacionOxigeno === '' || formTriaje.value.TriajeSaturacionOxigeno === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo SATURACIÓN DE OXÍGENO es requerido', 'error');
    }
    if (formTriaje.value.TriajeTemperatura === '' || formTriaje.value.TriajeTemperatura === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo TEMPERATURA es requerido', 'error');
    }
    if (formTriaje.value.IdFuenteFinanciamiento === '' || formTriaje.value.IdFuenteFinanciamiento === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Financiamiento es requerido', 'error');
    }
    if (formTriaje.value.IdTipoFinanciamiento === '' || formTriaje.value.IdTipoFinanciamiento === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Producto/Plan es requerido', 'error');
    }
    if (formTriaje.value.IdTipoGravedad === '' || formTriaje.value.IdTipoGravedad === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Gravedad es requerido', 'error');
    }
    if (form.value.IdOrigenAtencion === '' || formTriaje.value.IdOrigenAtencion === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Origen de atención es requerido', 'error');
    }

    const accionConfirmacion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PAR EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensajeConfirmacion = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accionConfirmacion}?`;
    const confirmado = await showAlertConfirmacion(
        'VALIDACIÓN DE DATOS REALIZADOS', mensajeConfirmacion,
        'warning'
    );

    if (confirmado) {
        isModalLoading.value = true;
        loadingSubmit.value = true;
        let success = false;
        if (props.accion !== 'admitir') {
            formTriaje.value.TriajePresion = formTriaje.value.TriajePresionSis + '/' + formTriaje.value.TriajePresionDia;
            formTriaje.value.EstadoTriaje = 'Registrado';
            const now = new Date();
            formTriaje.value.HoraTermino = now.toTimeString().slice(0, 8);
            try {
                const {data} = await axios.post(`/emergencia/triaje-emergencia`, formTriaje.value)
                success = data.success;
                form.value.IdTriajeEmergencia = data.data.IdTriajeEmergencia
            } catch (error) {
                isModalLoading.value = false
                loadingSubmit.value = false;
                return;
            }
        } else {
            success = true;
        }


        // formTriaje.value.TriajePresion = formTriaje.value.TriajePresionSis + '/' + formTriaje.value.TriajePresionDia;
        // let formTriajeTemp;
        // if (accionForm.value === 'registrar') {
        //     formTriaje.value.EstadoTriaje = 'Registrado';
        //     formTriajeTemp = {...formTriaje.value};
        //     formTriajeTemp.IdPaciente = idPaciente.value;
        //     const now = new Date();
        //     formTriajeTemp.HoraTermino = now.toTimeString().slice(0, 8);
        // } else {
        //     formTriajeTemp = {...formTriaje.value};
        // }

        // let success = false;
        // let IdTriajeEmergencia;
        // if (accionForm.value === 'registrar') {
        //     let response = null;
        //     try {
        //         response = await axios.post(`/emergencia/triaje-emergencia`, formTriajeTemp)
        //         success = response.data.success;
        //         IdTriajeEmergencia = response.data.data.IdTriajeEmergencia
        //     } catch (error) {
        //         console.log(error);
        //         isModalLoading.value = false
        //         loadingSubmit.value = false;
        //         return;
        //     }
        // }
        //
        // if (accionForm.value === 'registrar_con_triaje') {
        //     success = true;
        //     IdTriajeEmergencia = formTriajeTemp.id;
        // }

        if (success) {
            try {
                let formAdmisionTemp = {...form.value};
                formAdmisionTemp.IdTipoGravedad = formTriaje.value.IdTipoGravedad;
                formAdmisionTemp.IdTipoFinanciamiento = formTriaje.value.IdTipoFinanciamiento;
                formAdmisionTemp.IdFuenteFinanciamiento = formTriaje.value.IdFuenteFinanciamiento;
                formAdmisionTemp.IdServicioIngreso = formTriaje.value.IdServicio;
                let responseAdmision = await axios.post(`/${props.resource}`, formAdmisionTemp)
                const data = responseAdmision.data;
                if (data.success) {
                    emit('success');
                    closeDialog();
                    showToastSuccess(data.mensaje)
                } else {
                    showToastError(data.mensaje)
                }
            } catch (error) {
                console.log(error);
            } finally {
                isModalLoading.value = false
                loadingSubmit.value = false;
            }
        }
    }
};

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    isDialogOpen.value = false;
}

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :recordId="form.id"
               :header="title"
               :loading="loading"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-lg">
        <div class="row">
            <div class="col-12">
                <show-paciente :view-record="isViewMode"
                               :paciente="datosPaciente"
                               v-if="!loading"></show-paciente>
            </div>
            <div class="col-12">
                <Tabs value="0">
                    <TabList>
                        <Tab value="0">Triaje</Tab>
                        <Tab value="1">Ingreso</Tab>
                        <Tab value="2">Morbilidad</Tab>
                    </TabList>
                    <TabPanels style="padding: 16px 0">
                        <TabPanel value="0">
                            <formulario-triaje :data="formTriaje"
                                               :is-view-mode="viewRecordTriaje"
                                               :modulo-actual="moduloActual"
                                               @on-change-motivo-ingreso="onChangeMotivoIngreso"
                                               @on-change-medico-topico="onChangeMedicoTopico"></formulario-triaje>
                        </TabPanel>
                        <TabPanel value="1">
                            <div class="row">
                                <div class="col-xl-2 col-lg-3 col-md-12">
                                    <w-date-picker v-model="form.FechaIngreso"
                                                   :disabled="isViewMode"
                                                   label="Fecha de ingreso"></w-date-picker>
                                </div>
                                <div class="col-xl-2 col-lg-3 col-md-12">
                                    <w-time-picker v-model="form.HoraIngreso"
                                                   :disabled="isViewMode"
                                                   label="Hora de ingreso"></w-time-picker>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12">
                                    <BaseCombo v-model="form.IdOrigenAtencion"
                                               :options="tipoOrigenAtencion"
                                               label="Origen"
                                               :disabled="isViewMode"
                                               :showClear="false"
                                               @update:modelValue="onChangeOrigenAtencion"/>
                                </div>
                                <template v-if="showReferencia">
                                    <div class="col-xl-4 col-lg-6 col-md-12">
                                        <BaseCombo v-model="form.IdTipoReferencia"
                                                   :options="appStore.tablasCache.tipoReferenciaCache"
                                                   label="Tipo de referencia"
                                                   :disabled="isViewMode"/>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-md-12" v-if="form.IdTipoReferencia === 1">
                                        <label class="form-label">Establecimiento de referencia</label>
                                        <Select v-model="form.IdEstablecimientoExterno"
                                                :options="establecimientos"
                                                option-label="label"
                                                option-value="value"
                                                filter
                                                filterPlaceholder="Buscar..."
                                                @filter="onFilterEstablecimientos"
                                                :loading="loadingEstablecimientos"
                                                placeholder="Seleccione una opción"
                                                :showClear="true"
                                                class="w-full"
                                                size="small"
                                                style="width: 100%;"
                                                :disabled="isViewMode"
                                                :autoFilterFocus="true"></Select>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-md-12" v-else>
                                        <BaseCombo v-model="form.IdEstablecimientoExterno"
                                                   :options="appStore.tablasCache.establecimientoNoMinsaCache"
                                                   filter
                                                   label="Establecimiento de referencia"
                                                   :disabled="isViewMode"/>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-md-12">
                                        <w-input v-model="form.NumeroReferencia"
                                                 label="Número de referencia"></w-input>
                                    </div>
                                </template>
                                <div class="col-xl-4 col-lg-6 col-md-12">
                                    <w-input v-model="form.NombreAcompaniante"
                                             :disabled="isViewMode"
                                             label="Nombre del acompañante"></w-input>
                                </div>
                            </div>
                        </TabPanel>
                        <TabPanel value="2">
                            <div class="row">
                                <div class="col-xl-4 col-lg-6 col-md-12">
                                    <BaseCombo v-model="form.IdCausaExternaMorbilidad"
                                               :options="appStore.tablasCache.emergenciaCausaExternaMorbilidadCache"
                                               label="Causa externa de morbilidad"
                                               :filter="true"
                                               :disabled="isViewMode"
                                               @update:model-value="onChangeCausaExternaMorbilidad"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showLugarEvento">
                                    <BaseCombo v-model="form.IdLugarEvento"
                                               :options="appStore.tablasCache.emergenciaLugarEventoCache"
                                               label="Lugar del evento"
                                               :filter="true"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showTipoEvento">
                                    <BaseCombo v-model="form.IdTipoEvento"
                                               :options="appStore.tablasCache.emergenciaTipoEventoCache"
                                               label="Tipo de evento"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showSeguridad">
                                    <BaseCombo v-model="form.IdSeguridad"
                                               :options="appStore.tablasCache.emergenciaSeguridadCache"
                                               label="Seguridad"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showRelacionAgresorVictima">
                                    <BaseCombo v-model="form.IdRelacionAgresorVictima"
                                               :options="appStore.tablasCache.emergenciaRelacionAgresorVictimaCache"
                                               label="Relación agresor víctima"
                                               :filter="true"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showClaseAccidente">
                                    <BaseCombo v-model="form.IdClaseAccidente"
                                               :options="appStore.tablasCache.emergenciaClaseAccidenteCache"
                                               label="Clase de accidente"
                                               :filter="true"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showTipoVehiculo">
                                    <BaseCombo v-model="form.IdTipoVehiculo"
                                               :options="appStore.tablasCache.emergenciaTipoVehiculoCache"
                                               label="Tipo de vehículo"
                                               :filter="true"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showTipoTransporte">
                                    <BaseCombo v-model="form.IdTipoTransporte"
                                               :options="appStore.tablasCache.emergenciaTipoTransporteCache"
                                               label="Tipo de transporte"
                                               :filter="true"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showUbicacionLesionado">
                                    <BaseCombo v-model="form.IdUbicacionLesionado"
                                               :options="appStore.tablasCache.emergenciaUbicacionLesionadoCache"
                                               label="Ubicación del lesionado"
                                               :filter="true"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showPosicionLesionadoALAB">
                                    <BaseCombo v-model="form.IdPosicionLesionadoALAB"
                                               :options="appStore.tablasCache.emergenciaPosicionLesionadoAlabCache"
                                               label="Posición del lesionado ALAB"
                                               :filter="true"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12" v-if="showGrupoOcupacionalALAB">
                                    <BaseCombo v-model="form.IdGrupoOcupacionalALAB"
                                               :options="appStore.configuracionEmergenciaFormasIngreso"
                                               label="Grupo ocupacional ALAB"
                                               :filter="true"
                                               :disabled="isViewMode"/>
                                </div>
                                <div class="col-xl-4 col-lg-6" v-if="showTipoAgenteAgan">
                                    <BaseCombo v-model="form.IdTipoAgenteAGAN"
                                               :options="appStore.tablasCache.emergenciaTipoAgenteAganCache"
                                               label="Tipo de agente AGAN"
                                               :filter="true"
                                               :disabled="isViewMode"/>
                                </div>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
            <div class="col-12 d-flex justify-content-end mt-4">
                <w-button type="secondary"
                          label="Cerrar"
                          text
                          @click="closeDialog"
                          :disabled="loadingSubmit"/>
                <w-button type="primary"
                          :label="`${loadingSubmit?'Guardando...' : 'Guardar'}`"
                          @click="onSubmit"
                          v-if="!isViewMode && !tieneCuentasPendientes"
                          :disabled="loadingSubmit"/>
            </div>
        </div>
    </BaseModal>
</template>
