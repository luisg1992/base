<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue';
import {useAppStore} from '@/stores/useAppStore'
import FormularioTriaje from "./components/FormularioTriaje.vue";
import BuscarPaciente from "./components/BuscarPaciente.vue";
import ShowPaciente from "./components/ShowPaciente.vue";

let appStore = useAppStore();
const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String,
    moduloActual: String,
    paciente: Object,
    accion: String
})

const emit = defineEmits(['close', 'success']);

let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let loading = ref(false);
let modalReady = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let tipoDocumento = ref(1);
let nroDocumento = ref('');
let serviciosFiltrados = ref([])
let medicoEspecialidadFiltrados = ref([])

let IdTipoSexo = ref(null)
let correoPaciente = ref('')
let telefonoPaciente = ref('')
// let loadingFoto = ref(true);
let loadingDatosPaciente = ref(false);
let recargado = ref(false);

let tiposDocumento = ref([]);
let errorResponseTriaje = ref(false);
let mensajeResponseTriaje = ref();
let isViewMode = computed(() => props.viewRecord)
let accionForm = ref('registrar');
let title = ref('');
let errors = ref(null);
let form = ref({});
let idPaciente = ref(null);
let datosPaciente = ref(null);

const initForm = () => {
    idPaciente.value = null;
    loading.value = false;
    recargado.value = false;
    errorResponseTriaje.value = false;
    mensajeResponseTriaje.value = null;
    datosPaciente.value = null;
    errors.value = null;
    form.value = {
        CodigoTriaje: null,
        IdEmpleado: null,
        IdPaciente: null,
        IdFuenteFinanciamiento: null,
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
}

const handleOpen = async () => {
    initForm();
    loading.value = true
    await firstOrNew()
    loading.value = false
}

const firstOrNew = async () => {
    tiposDocumento.value = [];
    tiposDocumento.value = [...appStore.personaTipoDocumentosIdentidad];
    tiposDocumento.value.push({
        id: 11,
        value: -1,
        label: 'HISTORIA CLÍNICA',
        CodigoSUNASA: null,
        CodigoHIS: null,
        CodigoSIS: null
    })
    if (props.accion === 'registrar') {
        title.value = 'Registrar triaje';
        datosPaciente.value = props.paciente;
        form.value.IdPaciente = datosPaciente.value.id;
        form.value.IdFuenteFinanciamiento = datosPaciente.value.IdFuenteFinanciamiento;
        form.value.IdTipoDocTriaje = datosPaciente.value.IdDocIdentidad;
        form.value.NroDocTriaje = datosPaciente.value.NroDocumento;
        form.value.ApellidoPaternoTriaje = datosPaciente.value.ApellidoPaterno;
        form.value.ApellidoMaternoTriaje = datosPaciente.value.ApellidoMaterno;
        form.value.PrimerNombreTriaje = datosPaciente.value.PrimerNombre;
        form.value.SegundoNombreTriaje = datosPaciente.value.SegundoNombre ?? null;
        form.value.TercerNombreTriaje = datosPaciente.value.TercerNombre ?? null;
        form.value.FechaNacimientoTriaje = datosPaciente.value.FechaNacimiento;
        const now = new Date();
        form.value.HoraInicio = now.toTimeString().slice(0, 8);
    } else {
        const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
        Object.assign(form.value, data.data)
        const responsePaciente = await axios.get(`/personas/pacientes/record/${form.value.IdPaciente}`);
        datosPaciente.value = responsePaciente.data.data;
        const presion = form.value.TriajePresion || '';
        const partes = presion.split('/');
        form.value.TriajePresionSis = partes[0]?.trim() || null;
        form.value.TriajePresionDia = partes[1]?.trim() || null;
        form.value.TriajeSaturacionOxigeno = form.value.TriajeSaturacionOxigeno?.trim() || '';
    }
    if (props.accion === 'editar') {
        title.value = 'Registrar triaje';
    }
    if (props.accion === 'ver') {
        title.value = 'Ver triaje';
    }

}

const onChangeServicio = (newValue) => {
    const seleccionado = serviciosFiltrados.value.find(
        (item) => item.id === newValue
    );

    if (seleccionado) {
        medicoEspecialidadFiltrados = appStore.personaMedicos
            .filter(medico =>
                Array.isArray(medico.especialidades)
            )
            .flatMap(medico =>
                medico.especialidades.filter(esp => esp.IdEspecialidad === seleccionado.IdEspecialidad)
            );
    }
}

// const successPaciente = async (datos) => {
//     console.log(datos);
//     idPaciente.value = datos.id;
//     form.value.IdTipoDocTriaje = datos.IdDocIdentidad;
//     form.value.NroDocTriaje = datos.NroDocumento;
//     form.value.ApellidoPaternoTriaje = datos.ApellidoPaterno;
//     form.value.ApellidoMaternoTriaje = datos.ApellidoMaterno;
//     form.value.PrimerNombreTriaje = datos.PrimerNombre;
//     form.value.SegundoNombreTriaje = datos.SegundoNombre ?? null;
//     form.value.TercerNombreTriaje = datos.TercerNombre ?? null;
//     form.value.FechaNacimientoTriaje = datos.FechaNacimiento;
//     form.value.IdFuenteFinanciamiento = datos.IdFuenteFinanciamiento;
//     const now = new Date();
//     form.value.HoraInicio = now.toTimeString().slice(0, 8);
//
//     if (accionForm.value === 'registrar') {
//         let {data} = await axios.get(`/emergencia/triaje-emergencia/validar_triaje_emergencia_paciente/${idPaciente.value}`)
//         if(data.success) {
//             accionForm.value = 'editar2';
//             title.value = 'Editar triaje';
//             await obtenerInformacionTriaje(data.id);
//         }
//     }
// }

// const obtenerInformacionTriaje = async (triajeId) => {
//     const {data} = await axios.get(`/emergencia/triaje-emergencia/record/${triajeId}`)
//     let formTemp = {...data.data};
//     delete formTemp.IdPaciente
//     // console.log('formTemp');
//
//     Object.keys(formTemp).forEach(key => {
//         if (typeof formTemp[key] === 'string') {
//             formTemp[key] = formTemp[key].trim();
//         }
//     });
//
//     const presion = formTemp.TriajePresion || '';
//     const partes = presion.split('/');
//
//     formTemp.TriajePresionSis = partes[0]?.trim() || null;
//     formTemp.TriajePresionDia = partes[1]?.trim() || null;
//     formTemp.TriajeSaturacionOxigeno = formTemp.TriajeSaturacionOxigeno?.trim() || '';
//
//     Object.assign(form.value, formTemp);
//
//     // console.log('formTemp');
//     // Object.assign(form.value, formTemp)
//     console.log('formTemp');
//     console.log(form.value);
//     console.log('formTemp');
//     // const presion = form.value.TriajePresion || '';
//     // const partes = presion.split('/');
//     // form.value.TriajePresionSis = partes[0]?.trim() || null;
//     // form.value.TriajePresionDia = partes[1]?.trim() || null;
//     // form.value.TriajeSaturacionOxigeno = form.value.TriajeSaturacionOxigeno?.trim() || '';
// }

const guardar = async () => {
    if (form.value.TriajePresionSis === '' || form.value.TriajePresionSis === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo PRESIÓN ARTERIAL es requerido', 'error');
    }
    if (form.value.TriajePresionDia === '' || form.value.TriajePresionDia === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo PRESIÓN ARTERIAL es requerido', 'error');
    }
    if (form.value.TriajeFrecCardiaca === '' || form.value.TriajeFrecCardiaca === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo FRECUENCIA CARDIACA es requerido', 'error');
    }
    if (form.value.TriajeFrecRespiratoria === '' || form.value.TriajeFrecRespiratoria === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo FRECUENCIA RESPIRATORIA es requerido', 'error');
    }
    if (form.value.TriajeSaturacionOxigeno === '' || form.value.TriajeSaturacionOxigeno === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo SATURACIÓN DE OXÍGENO es requerido', 'error');
    }
    if (form.value.TriajeTemperatura === '' || form.value.TriajeTemperatura === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo TEMPERATURA es requerido', 'error');
    }
    if (form.value.TriajeTalla === '' || form.value.TriajeTalla === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo TALLA es requerido', 'error');
    }
    if (form.value.TriajePeso === '' || form.value.TriajePeso === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo PESO es requerido', 'error');
    }
    if (form.value.TriajeIMC === '' || form.value.TriajeIMC === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo IMC es requerido', 'error');
    }

    isModalLoading.value = true
    const accionConfirmacion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PAR EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensajeConfirmacion = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accionConfirmacion}?`;
    const confirmado = await showAlertConfirmacion(
        'VALIDACIÓN DE DATOS REALIZADOS', mensajeConfirmacion,
        'warning'
    );

    if (confirmado) {
        loadingSubmit.value = true;
        form.value.TriajePresion = form.value.TriajePresionSis + '/' + form.value.TriajePresionDia;
        if (accionForm.value === 'editar') {
            form.value.EstadoTriaje = 'Modificado';
        } else {
            // form.value.IdPaciente = idPaciente.value;
            form.value.EstadoTriaje = 'Registrado';
            const now = new Date();
            form.value.HoraTermino = now.toTimeString().slice(0, 8);
        }
        form.value.Telefono = telefonoPaciente.value;
        form.value.Email = correoPaciente.value;

        await axios.post(`/${props.resource}`, form.value)
            .then(response => {
                loadingSubmit.value = false;
                emit('success');
                isDialogOpen.value = false;

                const accion = props.recordId ? 'ACTUALIZADO' : 'REALIZADO';
                const mensaje = `EL REGISTRO FUE ${accion} DE FORMA EXITOSA`;
                showAlert('PROCESO REALIZADO EXITOSAMENTE', mensaje, 'success');
                isModalLoading.value = false
            })
            .catch(error => {
                if (error.response.status === 422) {
                    errors.value = error.response.data.errors
                    isModalLoading.value = false
                }
            })
            .finally(() => {
                loadingSubmit.value = false;
                isModalLoading.value = false
            })
    }
};

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

const nuevaBusqueda = () => {
    initForm();
}

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :header="title"
               :viewRecord="isViewMode"
               :loading="loading"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-lg">

        <show-paciente :view-record="isViewMode"
                       :paciente="datosPaciente"
                       v-if="!loading"></show-paciente>

        <!--        <buscar-paciente :view-record="isViewMode"-->
        <!--                         :paciente-id="form.IdPaciente"-->
        <!--                         :key="form.IdPaciente === null ? 'new' : form.IdPaciente"-->
        <!--                         :accion="accionForm"-->
        <!--                         @nueva-busqueda="nuevaBusqueda"-->
        <!--                         @success="successPaciente"></buscar-paciente>-->
        <!--        <div v-if="errorResponseTriaje" class="alert alert-danger mb-4 mt-4">-->
        <!--            {{ mensajeResponseTriaje }}-->
        <!--        </div>-->
        <form @submit.prevent="guardar">
            <formulario-triaje :data="form"
                               :modulo-actual="moduloActual"
                               :is-view-mode="isViewMode"></formulario-triaje>
            <div v-if="!isViewMode" class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary" :disabled="loadingSubmit">
                    {{ loadingSubmit ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </BaseModal>
</template>
