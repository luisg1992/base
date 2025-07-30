<script setup>
import { reactive, ref, computed } from 'vue'
import axios from 'axios'
import { useForm } from '@inertiajs/vue3'

import InputText from 'primevue/inputtext';
import BaseInput from "@/components/WInput/WInput.vue";
import InputGroup from 'primevue/inputgroup';
import BaseModal from '@/components/BaseModal.vue'
import BaseDivider from '@/components/BaseDivider.vue'

import Textarea from 'primevue/textarea';
import ToggleSwitch from 'primevue/toggleswitch';
import BaseCombo from "@/components/WSelect/WSelect.vue";
import { useAppStore } from '@/stores/useAppStore'
import FormularioTriaje from "./components/FormularioTriaje.vue";

let appStore = useAppStore();
const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success']);

let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let modalReady = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
// Variables reactivas
let tipoDocumento = ref(1);
let nroDocumento = ref('');
let pacienteSeleccionado = ref(null)
// let estadosFiltrados = ref([])
let serviciosFiltrados = ref([])
let medicoEspecialidadFiltrados = ref([])

// Datos del paciente
let idPaciente = ref(null)
let nombrePaciente = ref('')
let dniPaciente = ref('')
let hcPaciente = ref('')
let edadPaciente = ref('')
let sexoPaciente = ref('')
let iafaPaciente = ref('')
let servicioPaciente = ref('')
let fechaIngresoPaciente = ref('')
let tipoServicioPaciente = ref('')
let estadoCuentaPaciente = ref('')
let IdTipoSexo = ref(null)
let correoPaciente = ref('')
let telefonoPaciente = ref('')
let fotoPaciente = ref(null)
let loadingFoto = ref(true)
let loadingDatosPaciente = ref(false)
// let alergia = ref(false);

let inputDocumentoRef = ref(null);
let tiposDocumento = ref([]);

let isViewMode = ref(false);//computed(() => props.viewRecord)
let title = ref('');
let errors = ref(null);
let form = ref({});

let errorResponseTriaje = ref(false);

const initForm = () => {
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
        FechaNacimientoTriaje: null,

        IdFormaIngreso: null,
        IdEstadoIngreso: null,

        IdMotivoIngreso: null,
        EstablecimientoSalud: null,
        IdTipoTriajeDiferenciado: null,

        Estado: 1
    }
    limpiarPaciente()
}

function limpiarPaciente() {
    pacienteSeleccionado.value = null
    idPaciente.value = null
    nombrePaciente.value = ''
    dniPaciente.value = ''
    hcPaciente.value = ''
    edadPaciente.value = ''
    sexoPaciente.value = ''
    iafaPaciente.value = ''
    tipoServicioPaciente.value = ''
    estadoCuentaPaciente.value = ''
    fechaIngresoPaciente.value = ''
    servicioPaciente.value = ''
    IdTipoSexo.value = null
    correoPaciente.value = ''
    telefonoPaciente.value = ''
    fotoPaciente.value = null
    loadingFoto.value = true
    loadingDatosPaciente.value = false
    nroDocumento.value = null
}

const handleOpen = async () => {
    errorResponseTriaje.value = false;
    isViewMode.value = props.viewRecord || false;
    tiposDocumento.value = [];
    //tiposDocumento.value = [...appStore.personaTipoDocumentosIdentidad];
    // Agregar un nuevo tipo de documento al arreglo
    tiposDocumento.value.push({
        id: 11,
        value: 1,
        label: 'NUMERO DE CUENTA',
        CodigoSUNASA: null,
        CodigoHIS: null,
        CodigoSIS: null
    })
    initForm();
    loadingDatosPaciente.value = false
    loadingFoto.value = false

    // alergia.value = false
    nroDocumento.value = null

    /*CARGAR LISTA DE SERVICIOS FILTRADA*/


    if (props.recordId) {
        title.value = 'Editar triaje';
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const { data } = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form.value, data.data)

            /*  const presion = form.value.TriajePresion || '';
             const partes = presion.split('/'); */

            /* form.value.TriajePresionSis = partes[0]?.trim() || null;
            form.value.TriajePresionDia = partes[1]?.trim() || null;
            form.value.TriajeSaturacionOxigeno = form.value.TriajeSaturacionOxigeno?.trim() || ''; */

            // if (form.value.IdEstadoIngreso) {
            //     estadosFiltrados.value = appStore.configuracionEmergenciaEstadosIngreso.filter(row => row.IdFormaIngreso === form.value.IdFormaIngreso);
            //     form.value.IdEstadoIngreso = form.value.IdEstadoIngreso || null;
            // }

            tipoDocumento.value = 1//form.value.IdTipoDocTriaje;
            nroDocumento.value = form.value.datos2.IdAtencion;
            console.log(form.value);
            await buscarPacienteTriajeEmergencia(false);
            //onChangeServicio(form.value.IdServicio);
            modalReady.value = true;
            showAlert(
                "CONSULTA REALIZADA",
                "LA CONSULTA FUE REALIZADA DE FORMA EXITOSA",
                "success"
            );



        } catch (error) {
            if (error.status === 403) {

                closeDialog();
            }
        } finally {
            showModal.value = true;
            isModalLoading.value = false;
        }
    } else {
        title.value = 'Registrar triaje';
        showModal.value = true;
    }
}

async function actualizarDatosPaciente(datos) {
    try {
        loadingFoto.value = true
        loadingDatosPaciente.value = false
        fotoPaciente.value = null

        const defaultFoto = datos.IdTipoSexo == '1'
            ? '../../assets/img/sexo1.gif'
            : '../../assets/img/sexo2.gif'
        fotoPaciente.value = defaultFoto ?? null
        IdTipoSexo.value = datos.IdTipoSexo
        const edadCompleta = calcularEdadCompleta(datos.FechaNacimiento)

        nombrePaciente.value = `${datos.ApellidoPaterno || ''} ${datos.ApellidoMaterno || ''} ${datos.PrimerNombre || ''} ${datos.SegundoNombre || ''} ${datos.TercerNombre || ''}`
        dniPaciente.value = `DNI: ${datos.NroDocumento}`
        hcPaciente.value = `HC: ${datos.NroHistoriaClinica ?? 'SIN DATOS'}`
        edadPaciente.value = `EDAD: ${edadCompleta.years} A, ${edadCompleta.months} M, ${edadCompleta.days} D`
        sexoPaciente.value = `SEXO: ${datos.IdTipoSexo == '1' ? 'MASCULINO' : 'FEMENINO'}`
        iafaPaciente.value = `IAFA Act: ${datos.dFuenteFinanciamiento || 'SIN DATOS'}`
        fechaIngresoPaciente.value = `F.Ing: ${formatearFecha(datos.FechaIngreso) || 'SIN DATOS'}`
        tipoServicioPaciente.value = `T. Serv.: ${datos.dTipoServicio || 'SIN DATOS'}`
        estadoCuentaPaciente.value = `Est: ${datos.estadoCta || 'SIN DATOS'}`
        servicioPaciente.value = `Servicio: ${datos.Servicio.Nombre || 'SIN DATOS'}`
        correoPaciente.value = datos.Email ?? ''
        telefonoPaciente.value = datos.Telefono ?? ''
        idPaciente.value = datos.IdPaciente ?? null

        /*DATOS DE FORMULARIO*/
        form.value.IdPaciente = datos.IdPaciente ?? null
        form.value.IdTipoDocTriaje = datos.IdDocIdentidad ?? null
        form.value.NroDocTriaje = datos.NroDocumento ?? null
        form.value.ApellidoPaternoTriaje = datos.ApellidoPaterno ?? null
        form.value.ApellidoMaternoTriaje = datos.ApellidoMaterno ?? null
        form.value.PrimerNombreTriaje = datos.PrimerNombre ?? null
        form.value.FechaNacimientoTriaje = datos.FechaNacimiento ?? null

        loadingDatosPaciente.value = true
        loadingFoto.value = false
    } catch (e) {
        console.error('Error cargando imagen:', e)
        fotoPaciente.value = null
        loadingFoto.value = false
        loadingDatosPaciente.value = true
    }
}

function calcularEdadCompleta(fechaNacimientoStr) {
    const fechaNacimiento = new Date(fechaNacimientoStr)
    const hoy = new Date()
    let anios = hoy.getFullYear() - fechaNacimiento.getFullYear()
    let meses = hoy.getMonth() - fechaNacimiento.getMonth()
    let dias = hoy.getDate() - fechaNacimiento.getDate()

    if (dias < 0) {
        meses--
        dias += new Date(hoy.getFullYear(), hoy.getMonth(), 0).getDate()
    }
    if (meses < 0) {
        anios--
        meses += 12
    }
    form.value.TriajeAnios = anios ?? null;
    form.value.TriajeMeses = meses ?? null;
    form.value.TriajeDias = dias ?? null;
    return { years: anios, months: meses, days: dias }
}

const buscarPacienteTriajeEmergencia = async (consultarSis = true) => {
    if (!tipoDocumento.value) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'EL CAMPO DE TIPO DE DOCUMENTO ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.', 'error');
        return;
    }

    nroDocumento.value = (nroDocumento.value || '').replace(/\s+/g, '');
    if (tipoDocumento.value == 1) {
        /* if (!nroDocumento.value || nroDocumento.value.length !== 8) {
            showAlert(
                'VALIDACIÓN DE CAMPO REALIZADO',
                'EL NÚMERO DE DOCUMENTO DEBE TENER EXACTAMENTE 8 CARACTERES.',
                'error'
            );
            return;
        } */
    }

    try {
        if (consultarSis) {
            showAlert("VERIFICANDO DATOS EN RENIEC ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        }
        const response = await axios.post('/personas/persona/PacienteBuscarPorNumeroCuenta', {
            //nombremodulo: 'ADMISIÓN Y CITAS',
            //!SECTIONtipodocumento: tipoDocumento.value,
            IdCuentaAtencion: nroDocumento.value,
            //tipopersona: 0,
        })
        console.log('response', response.data);
        /*VALIDAMOS LA RESPUESTA DE RENIEC SEA EXITOSA O LA CONSULTA A LA BD*/
        if (response.data.success) {
            /*SI ES EXTRANJERO NO PASA POR RENIEC*/
            pacienteSeleccionado.value = response?.data.data
            await actualizarDatosPaciente(response?.data.data)
            //reniec.value = true
            /*SI ES EXTRANJERO NO PASA POR RENIEC*/

            if (pacienteSeleccionado.value.IdEstado != '1' || pacienteSeleccionado.value.IdEstado != '4') {
                console.log('Paciente inactivo o fallecido');
                isViewMode.value = true;
                errorResponseTriaje.value = true;
            }

            showAlert(
                "CONSULTA REALIZADA",
                "LA CONSULTA FUE REALIZADA DE FORMA EXITOSA",
                "success"
            );

            if (consultarSis) {
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
                const parametros = {
                    tipodocumento: tipoDocumento.value,
                    numerodocumento: nroDocumento.value,
                    consultareniec: true,
                    tipopersona: 0,
                }
                //await obtenerDatosSis(parametros);
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
            }

        } else {
            // reniec.value = false
            if (consultarSis) {
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
                /* const parametros = {
                    tipodocumento: tipoDocumento.value,
                    numerodocumento: nroDocumento.value,
                    consultareniec: false,
                    tipopersona: 0,
                }
                await obtenerDatosSis(parametros); */
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
            }
        }
    } catch (error) {
        if (error.response) {
            showAlert('ERROR', error.response.data.message || 'Error al buscar paciente.', 'error');
        } else {
            showAlert('ERROR', error.message || 'Error de conexión.', 'error');
        }
    }
}

async function obtenerDatosSis(parametros = {}) {
    try {
        form.value.EstablecimientoSalud = null
        showAlert("VERIFICANDO AFILIACIÓN SIS ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        const response = await axios.post('/core/servicios/obtenerDatosSisCompletos', {
            numerodocumento: parametros.numerodocumento,
            tipodocumento: parametros.tipodocumento,
            consultareniec: parametros.consultareniec,
            tipopersona: parametros.tipopersona,
            modulo_origen: 'admicion_ce',
        });

        if (response?.data.respuesta == 1) {
            // sis.value = true
            if (parametros.consultareniec == false) {
                // reniec.value = true
                if (response?.data.data) {
                    pacienteSeleccionado.value = response?.data.data
                    await actualizarDatosPaciente(response?.data.data);
                }
            }

            if (response?.data.contingencia == 'S') {
                /*SI VIENE DEL CONSUMO DEL SIS DE CONTINGENCIA, TIENE QUE LLAMAR A UN MODAL PARA REGISTRAR DATOS ADICIONALES*/
            }
            form.value.IdFuenteFinanciamiento = 3
            form.value.EstablecimientoSalud = response?.data?.data?.EESS;

            showAlert(
                "CONSULTA REALIZADA",
                "LA CONSULTA FUE REALIZADA DE FORMA EXITOSA",
                "success"
            );

        } else {

            showAlert("CONSULTA REALIZADA", response?.data.descripcion, 'warning');
            if (response?.data.codigo != '14' || response?.data.codigo != 14) {

            } else {
                if (idPaciente.value) {
                    form.value.IdFuenteFinanciamiento = 1
                }
            }
        }
    } catch (error) {
        console.error('Error al obtener datos del SIS:', error);
        return null;
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
        if (props.recordId) {
            form.value.EstadoTriaje = 'Modificado';
        } else {
            form.value.EstadoTriaje = 'Registrado';
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

const clearSearchDocumento = () => {
    nroDocumento.value = '';
    inputDocumentoRef.value?.focus();
};

const formatearFecha = (fecha) => {
    const d = new Date(fecha);
    const dia = String(d.getDate()).padStart(2, '0');
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const anio = d.getFullYear();
    return `${dia}/${mes}/${anio}`;
}

defineExpose({ openDialog })
</script>

<template>
    <BaseModal :isVisible="isDialogOpen" :recordId="form.id" :header="title" :viewRecord="isViewMode"
        :loading="isModalLoading" @close="closeDialog" @open="handleOpen" size="modal-lg">
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-12">
                <div class="p-3 border rounded" style="border: 1px solid #7367f0!important; height: 125px;">
                    <BaseCombo v-model="tipoDocumento" :options="tiposDocumento" optionLabel="label" optionValue="value"
                        label="BUSCAR POR:" :disabled="true" />
                    <div class="d-flex align-items-center mt-2">

                        <div class="input-group">
                            <input type="text" class="form-control" ref="inputDocumentoRef"
                                placeholder="NÚMERO DE BÚSQUEDA" aria-label="Text input" :disabled="isViewMode"
                                v-model="nroDocumento" autocomplete="off" autofocus
                                @keydown.enter="buscarPacienteTriajeEmergencia">
                            <button class="btn btn-primary waves-effect" type="button" id="btnCosultarPacienteCita"
                                :disabled="isViewMode" name="btnCosultarPacienteCita" style=" height: 2rem !important;"
                                @click="buscarPacienteTriajeEmergencia">
                                <span class="ti ti-search" style="font-size: 17px;"></span>
                            </button>
                            <button class="btn btn-danger" @click="clearSearchDocumento"  :disabled="isViewMode">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Perfil paciente -->
            <div v-if="loadingDatosPaciente" class="col-xl-4 col-lg-4 col-md-12">
                <div class="p-3 border rounded" style="border: 1px solid #7367f0!important;">
                    <div class="d-flex align-items-start">
                        <div class="me-3 position-relative">
                            <img :src="fotoPaciente" :key="fotoPaciente" alt="Foto paciente" class="img-fluid rounded"
                                style="width: 98px; height: 98px;" />
                            <div v-if="loadingFoto"
                                class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-light rounded">
                                <div class="spinner-border text-primary" role="status"
                                    style="width: 1.5rem; height: 1.5rem;"></div>
                            </div>
                        </div>
                        <div style="font-size: 0.7rem; text-transform: uppercase;">
                            <label><b>INFORMACIÓN GENERAL:</b></label><br />
                            <strong class="text-success">{{ nombrePaciente }}</strong><br />
                            <strong>{{ dniPaciente }}</strong><br />
                            <strong>{{ hcPaciente }}</strong><br />
                            <strong>{{ edadPaciente }}</strong><br />
                            <strong>{{ sexoPaciente }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacto -->
            <div v-if="loadingDatosPaciente" class="col-xl-4 col-lg-6 col-md-12">
                <div class="p-3 border rounded"
                    style="border: 1px solid #7367f0!important;font-size: 0.7rem; text-transform: uppercase;">
                    <label><b>DATOS DE CONTACTO:</b></label><br />
                    <strong>{{ fechaIngresoPaciente }}</strong><br />
                    <strong>{{ tipoServicioPaciente }}</strong><br />
                    <strong>{{ estadoCuentaPaciente }}</strong><br />
                    <strong>{{ servicioPaciente }}</strong><br />
                    <strong>{{ iafaPaciente }}</strong>
                    <!-- <div class="mb-2">
                        <InputText v-model="correoPaciente" placeholder="CORREO ELECTRÓNICO" class="w-full"
                                   :disabled="isViewMode"/>
                    </div>
                    <div class="flex gap-2">
                        <InputText v-model="telefonoPaciente" placeholder="NÚMERO DE CELULAR" class="flex-1"
                                   :disabled="isViewMode"/>
                    </div> -->
                </div>
            </div>
        </div>

        <div v-if="errorResponseTriaje" class="alert alert-danger mb-0 mt-4">
            Ese estado de cuenta no se encuentra abierta
        </div>

        <form @submit.prevent="guardar">
            <formulario-triaje :data="form" :is-view-mode="isViewMode"></formulario-triaje>
            <!--            <div class="row">-->
            <!--                <BaseDivider title="I. DETALLE DE INGRESO"/>-->
            <!--                <div class="col-xl-4 col-lg-6 col-md-12">-->
            <!--                    <BaseCombo v-model="form.IdFuenteFinanciamiento"-->
            <!--                               :options="appStore.configuracionFuenteFinanciamiento"-->
            <!--                               label="F.Financiamiento" option-label="label" option-value="id" :filter="true"-->
            <!--                               :disabled="isViewMode"-->
            <!--                               placeholder="Seleccione una Fuente de Financiamiento"/>-->
            <!--                </div>-->
            <!--                <div class="col-xl-4 col-lg-6 col-md-12">-->
            <!--                    <BaseCombo v-model="form.IdFormaIngreso" :options="appStore.configuracionEmergenciaFormasIngreso"-->
            <!--                               label="Forma de Ingreso" option-label="label" option-value="id" :filter="true"-->
            <!--                               :disabled="isViewMode"-->
            <!--                               placeholder="Seleccione una Forma de Ingreso" @update:modelValue="onChangeFormaIngreso"/>-->
            <!--                </div>-->
            <!--                <div class="col-xl-4 col-lg-6 col-md-12">-->
            <!--                    <BaseCombo v-model="form.IdEstadoIngreso" :options="estadosFiltrados" label="Estado de Ingreso"-->
            <!--                               :disabled="isViewMode" option-label="label" option-value="id" :filter="true"-->
            <!--                               placeholder="Seleccione un Estado de Ingreso"/>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--            <div class="row">-->
            <!--                <BaseDivider title="II. SIGNOS VITALES"/>-->
            <!--                <div class="col-xl-4 col-lg-6 col-md-12">-->
            <!--                    <InputGroup>-->
            <!--                        <div class="flex-grow-1 me-2">-->
            <!--                            <BaseInput v-model="form.TriajePresionSis" :label="'P.A SIS'" step="0.1"-->
            <!--                                       :labelExtra="form.TriajePresionSis !== null && form.TriajePresionSis !== '' ? getCalificacionParametro('TriajePresionSis', form.TriajePresionSis).label : ''"-->
            <!--                                       :labelExtraClass="form.TriajePresionSis !== null && form.TriajePresionSis !== '' ? getCalificacionParametro('TriajePresionSis', form.TriajePresionSis).color : ''"-->
            <!--                                       :disabled="isViewMode" :error="errors.TriajePresionSis" type="number"-->
            <!--                                       :min="rangosSignosVitales.TriajePresionSis.min"-->
            <!--                                       :max="rangosSignosVitales.TriajePresionSis.max"-->
            <!--                                       @input="validarRango('TriajePresionSis', $event.target.value)"/>-->
            <!--                        </div>-->

            <!--                        <div class="flex-grow-1">-->
            <!--                            <BaseInput v-model="form.TriajePresionDia" :label="'P.A DIA'" step="0.1"-->
            <!--                                       :labelExtra="form.TriajePresionDia !== null && form.TriajePresionDia !== '' ? getCalificacionParametro('TriajePresionDia', form.TriajePresionDia).label : ''"-->
            <!--                                       :labelExtraClass="form.TriajePresionDia !== null && form.TriajePresionDia !== '' ? getCalificacionParametro('TriajePresionDia', form.TriajePresionDia).color : ''"-->
            <!--                                       :disabled="isViewMode" :error="errors.TriajePresionDia" required type="number"-->
            <!--                                       :min="rangosSignosVitales.TriajePresionDia.min"-->
            <!--                                       :max="rangosSignosVitales.TriajePresionDia.max"-->
            <!--                                       @input="validarRango('TriajePresionDia', $event.target.value)"/>-->
            <!--                        </div>-->
            <!--                    </InputGroup>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; Frecuencia Cardiaca &ndash;&gt;-->
            <!--                <div class="col-xl-4 col-lg-6 col-md-12">-->
            <!--                    <BaseInput v-model="form.TriajeFrecCardiaca" :label="'F.CAR'" placeholder="FRE.CARDIACA" step="0.1"-->
            <!--                               :labelExtra="form.TriajeFrecCardiaca !== null && form.TriajeFrecCardiaca !== '' ? getCalificacionParametro('TriajeFrecCardiaca', form.TriajeFrecCardiaca).label : ''"-->
            <!--                               :labelExtraClass="form.TriajeFrecCardiaca !== null && form.TriajeFrecCardiaca !== '' ? getCalificacionParametro('TriajeFrecCardiaca', form.TriajeFrecCardiaca).color : ''"-->
            <!--                               :disabled="isViewMode" :error="errors.TriajeFrecCardiaca" required type="number"-->
            <!--                               :min="rangosSignosVitales.TriajeFrecCardiaca.min"-->
            <!--                               :max="rangosSignosVitales.TriajeFrecCardiaca.max"-->
            <!--                               @input="validarRango('TriajeFrecCardiaca', $event.target.value)"/>-->
            <!--                </div>-->
            <!--                <div class="col-xl-4 col-lg-6 col-md-12">-->
            <!--                    <BaseInput v-model="form.TriajeFrecRespiratoria" :label="'F.RES'" placeholder="FRE.RESPIRATORIA"-->
            <!--                               step="0.1"-->
            <!--                               :labelExtra="form.TriajeFrecRespiratoria !== null && form.TriajeFrecRespiratoria !== '' ? getCalificacionParametro('TriajeFrecRespiratoria', form.TriajeFrecRespiratoria).label : ''"-->
            <!--                               :labelExtraClass="form.TriajeFrecRespiratoria !== null && form.TriajeFrecRespiratoria !== '' ? getCalificacionParametro('TriajeFrecRespiratoria', form.TriajeFrecRespiratoria).color : ''"-->
            <!--                               :disabled="isViewMode" :error="errors.TriajeFrecRespiratoria" required type="number"-->
            <!--                               :min="rangosSignosVitales.TriajeFrecRespiratoria.min"-->
            <!--                               :max="rangosSignosVitales.TriajeFrecRespiratoria.max"-->
            <!--                               @input="validarRango('TriajeFrecRespiratoria', $event.target.value)"/>-->
            <!--                </div>-->
            <!--                <div class="col-xl-4 col-lg-6 col-md-12">-->
            <!--                    <BaseInput v-model="form.TriajeSaturacionOxigeno" :label="'SAT'" placeholder="SAT" step="0.1"-->
            <!--                               :labelExtra="form.TriajeSaturacionOxigeno !== null && form.TriajeSaturacionOxigeno !== '' ? getCalificacionParametro('TriajeSaturacionOxigeno', form.TriajeSaturacionOxigeno).label : ''"-->
            <!--                               :labelExtraClass="form.TriajeSaturacionOxigeno !== null && form.TriajeSaturacionOxigeno !== '' ? getCalificacionParametro('TriajeSaturacionOxigeno', form.TriajeSaturacionOxigeno).color : ''"-->
            <!--                               :disabled="isViewMode" :error="errors.TriajeSaturacionOxigeno" required type="number"-->
            <!--                               :min="rangosSignosVitales.TriajeSaturacionOxigeno.min"-->
            <!--                               :max="rangosSignosVitales.TriajeSaturacionOxigeno.max"-->
            <!--                               @input="validarRango('TriajeSaturacionOxigeno', $event.target.value)"/>-->
            <!--                </div>-->
            <!--                <div class="col-xl-4 col-lg-6 col-md-12">-->
            <!--                    <BaseInput v-model="form.TriajeTemperatura" :label="'TEM'" placeholder="TEM" step="0.1"-->
            <!--                               :labelExtra="form.TriajeTemperatura !== null && form.TriajeTemperatura !== '' ? getCalificacionParametro('TriajeTemperatura', form.TriajeTemperatura).label : ''"-->
            <!--                               :labelExtraClass="form.TriajeTemperatura !== null && form.TriajeTemperatura !== '' ? getCalificacionParametro('TriajeTemperatura', form.TriajeTemperatura).color : ''"-->
            <!--                               :disabled="isViewMode" :error="errors.TriajeTemperatura" required type="number"-->
            <!--                               :min="rangosSignosVitales.TriajeTemperatura.min"-->
            <!--                               :max="rangosSignosVitales.TriajeTemperatura.max"-->
            <!--                               @input="validarRango('TriajeTemperatura', $event.target.value)"/>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--            <div class="row">-->
            <!--                <BaseDivider title="III. PRIORIDAD"/>-->
            <!--                <div class="col-xl-6 col-lg-6 col-md-12">-->
            <!--                    <BaseCombo v-model="form.IdMotivoIngreso" :options="appStore.configuracionEmergenciaMotivoIngreso"-->
            <!--                               label="Motivo de Ingreso" option-label="label" option-value="id" :filter="true"-->
            <!--                               :disabled="isViewMode"-->
            <!--                               placeholder="Seleccione un Motivo de Ingreso"-->
            <!--                               @update:modelValue="onChangeMotivoIngreso"/>-->
            <!--                </div>-->
            <!--                <div class="col-xl-6 col-lg-6 col-md-12">-->
            <!--                    <BaseCombo v-model="form.IdTipoGravedad" :options="appStore.configuracionEmergenciaPrioridad"-->
            <!--                               label="Prioridad" option-label="label" option-value="id" :filter="true"-->
            <!--                               :disabled="isViewMode"-->
            <!--                               placeholder="Seleccione una Prioridad"/>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--            <div class="row">-->
            <!--                <BaseDivider title="IV. DESTINO"/>-->
            <!--                <div class="col-xl-6 col-lg-6 col-md-12">-->
            <!--                    <BaseCombo v-model="form.IdServicio" :options="serviciosFiltrados" optionLabel="Detalle"-->
            <!--                               label="Enviado a Tópico" option-label="label" option-value="id" :filter="true"-->
            <!--                               :disabled="isViewMode"-->
            <!--                               placeholder="Seleccione un Servicio" @update:modelValue="onChangeServicio"/>-->
            <!--                </div>-->
            <!--                <div class="col-xl-6 col-lg-6 col-md-12">-->
            <!--                    <BaseCombo v-model="form.IdMedicoTopico" :options="medicoEspecialidadFiltrados"-->
            <!--                               optionLabel="Detalle"-->
            <!--                               label="Médico / Especialidad" option-label="label" option-value="id" :filter="true"-->
            <!--                               :disabled="isViewMode"-->
            <!--                               placeholder="Seleccione un Servicio"/>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--            <div class="row">-->
            <!--                <BaseDivider title="V. OBSERVACIÓN"/>-->
            <!--                <div class="col-xl-12 col-lg-12 col-md-12">-->
            <!--                    <div class="d-flex justify-content-end align-items-center">-->
            <!--                        <label class="mb-0 me-2">REACCIÓN ALÉRGICA?</label>-->
            <!--                        <ToggleSwitch v-model="alergia" @change="onToggle('ALERGIA', $event)" :disabled="isViewMode"/>-->
            <!--                        <br>-->
            <!--                    </div>-->
            <!--                    <Textarea v-model="form.TriajeObservacion" autoResize rows="5" cols="30" :disabled="isViewMode"/>-->
            <!--                </div>-->
            <!--            </div>-->
            <div v-if="!isViewMode" class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary" :disabled="loadingSubmit">
                    {{ loadingSubmit ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </BaseModal>
</template>
