<script setup>
import {reactive, ref, computed} from 'vue'
import axios from 'axios'
import {useForm} from '@inertiajs/vue3'

import InputText from 'primevue/inputtext';
import BaseInput from "@/components/WInput/WInput.vue";
import BaseModal from '@/components/BaseModal.vue'
import BaseDivider from '@/components/BaseDivider.vue'

import BaseCombo from "@/components/WSelect/WSelect.vue";
import {useAppStore} from '@/stores/useAppStore'
import {showToastSuccess} from "../../../../utils/alert.js";

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

const rangosSignosVitales = getTriajeParametros();

let isViewMode = computed(() => props.viewRecord)
let errors = reactive({})
let form = useForm({
    /* CodigoTriaje: null, */
    IdCartaGarantia: null,
    IdPaciente: null,
    IdTipoFinanciamiento: null,
    IdFuenteFinanciamiento: null,
    IdTipoServicio: null,
    IdEspecialidad: null,
    IdProgramaInstitucion: null,
    IdTipoDocumento: null,

    FechaDocumento: null,
    FechaVencimiento: null,
    NumeroDocumento: null,
    NumeroExpedienteTramiteDocumentario: null,

    Estado: 1,
    FechaRegistro: null,
    IdEmpleadoRegistra: null,
})


// Variables reactivas
let tipoDocumento = ref(1);
let nroDocumento = ref('');
let pacienteSeleccionado = ref(null)
let estadosFiltrados = ref([])
let serviciosFiltrados = ref([])
let fuentesFinancimiento = ref([])
let medicoEspecialidadFiltrados = ref([])
let estados = ref([{'label': 'Activo', 'id': 1}, {'label': 'Inactivo', 'id': 0}]);

// Datos del paciente
let idPaciente = ref(null)
let nombrePaciente = ref('')
let dniPaciente = ref('')
let hcPaciente = ref('')
let edadPaciente = ref('')
let sexoPaciente = ref('')
let IdTipoSexo = ref(null)
let correoPaciente = ref('')
let telefonoPaciente = ref('')
let fotoPaciente = ref(null)
let loadingFoto = ref(true)
let loadingDatosPaciente = ref(false)
let alergia = ref(false);

let inputDocumentoRef = ref(null);
let tiposDocumento = ref([]);


function limpiarPaciente() {
    pacienteSeleccionado.value = null
    idPaciente.value = null
    nombrePaciente.value = ''
    dniPaciente.value = ''
    hcPaciente.value = ''
    edadPaciente.value = ''
    sexoPaciente.value = ''
    IdTipoSexo.value = null
    correoPaciente.value = ''
    telefonoPaciente.value = ''
    fotoPaciente.value = null
    loadingFoto.value = true
    loadingDatosPaciente.value = false
    nroDocumento.value = null
}

const handleOpen = async () => {
    tiposDocumento.value = [];
    tiposDocumento.value = [...appStore.personaTipoDocumentosIdentidad];
    // Agregar un nuevo tipo de documento al arreglo
    tiposDocumento.value.push({
        id: 11,
        value: -1,
        label: 'HISTORIA CLÍNICA',
        CodigoSUNASA: null,
        CodigoHIS: null,
        CodigoSIS: null
    })
    limpiarPaciente();
    loadingDatosPaciente.value = false
    loadingFoto.value = false

    alergia.value = false
    nroDocumento.value = null

    errors.value = {};
    form.reset()
    form.clearErrors()

    /*CARGAR LISTA DE SERVICIOS FILTRADA*/
    serviciosFiltrados.value = appStore.configuracionServicio.filter(
        row => row.idEstado === 1 && row.IdTipoServicio === 2
    );

    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form, data.data)


            if (form.IdEstadoIngreso) {
                estadosFiltrados.value = appStore.configuracionEmergenciaEstadosIngreso.filter(row => row.IdFormaIngreso === form.IdFormaIngreso);
                form.IdEstadoIngreso = form.IdEstadoIngreso || null;
            }

            tipoDocumento.value = parseInt(form.Paciente.IdDocIdentidad),
                nroDocumento.value = form.Paciente.NroDocumento,
                await buscarPacienteTriajeEmergencia(false);
            onChangeTipoFinancimiento();
            onChangeServicio(form.IdServicio);
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
        showModal.value = true;
    }
}

async function actualizarDatosPaciente(datos) {
    try {
        loadingFoto.value = true
        loadingDatosPaciente.value = false
        fotoPaciente.value = null

        const defaultFoto = datos.IdTipoSexo == 1
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
        correoPaciente.value = datos.Email ?? ''
        telefonoPaciente.value = datos.Telefono ?? ''
        idPaciente.value = datos.IdPaciente ?? null

        /*DATOS DE FORMULARIO*/
        form.IdPaciente = datos.IdPaciente ?? null
        form.IdTipoDocTriaje = datos.IdDocIdentidad ?? null
        form.NroDocTriaje = datos.NroDocumento ?? null
        form.ApellidoPaternoTriaje = datos.ApellidoPaterno ?? null
        form.ApellidoMaternoTriaje = datos.ApellidoMaterno ?? null
        form.PrimerNombreTriaje = datos.PrimerNombre ?? null
        form.FechaNacimientoTriaje = datos.FechaNacimiento ?? null

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
    form.TriajeAnios = anios ?? null;
    form.TriajeMeses = meses ?? null;
    form.TriajeDias = dias ?? null;
    return {years: anios, months: meses, days: dias}
}

const buscarPacienteTriajeEmergencia = async (consultarSis = true) => {
    if (!tipoDocumento.value) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'EL CAMPO DE TIPO DE DOCUMENTO ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.', 'error');
        return;
    }

    nroDocumento.value = (nroDocumento.value || '').replace(/\s+/g, '');
    if (tipoDocumento.value == 1) {
        if (!nroDocumento.value || nroDocumento.value.length !== 8) {
            showAlert(
                'VALIDACIÓN DE CAMPO REALIZADO',
                'EL NÚMERO DE DOCUMENTO DEBE TENER EXACTAMENTE 8 CARACTERES.',
                'error'
            );
            return;
        }
    }

    try {
        if (consultarSis) {
            showAlert("VERIFICANDO DATOS EN RENIEC ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        }
        const response = await axios.post('/personas/persona/PacienteBuscarTipoAndDocumento', {
            nombremodulo: 'ADMISIÓN Y CITAS',
            tipodocumento: tipoDocumento.value,
            numerodocumento: nroDocumento.value,
            tipopersona: 0,
        })

        /*VALIDAMOS LA RESPUESTA DE RENIEC SEA EXITOSA O LA CONSULTA A LA BD*/
        if (response.data.success) {
            /*SI ES EXTRANJERO NO PASA POR RENIEC*/
            pacienteSeleccionado.value = response?.data.data
            await actualizarDatosPaciente(response?.data.data)
            // reniec.value = true
            /*SI ES EXTRANJERO NO PASA POR RENIEC*/

            if (consultarSis) {
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
                const parametros = {
                    tipodocumento: tipoDocumento.value,
                    numerodocumento: nroDocumento.value,
                    consultareniec: true,
                    tipopersona: 0,
                }
                await obtenerDatosSis(parametros);
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
            }

        } else {
            // reniec.value = false
            if (consultarSis) {
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
                const parametros = {
                    tipodocumento: tipoDocumento.value,
                    numerodocumento: nroDocumento.value,
                    consultareniec: false,
                    tipopersona: 0,
                }
                await obtenerDatosSis(parametros);
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
        form.EstablecimientoSalud = null
        showAlert("VERIFICANDO AFILIACIÓN SIS ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        const response = await axios.post('/core/servicios/obtenerDatosSisCompletos', {
            numerodocumento: parametros.numerodocumento,
            tipodocumento: parametros.tipodocumento,
            consultareniec: parametros.consultareniec,
            tipopersona: parametros.tipopersona,
            modulo_origen: 'admicion_ce',
        });
        console.log('respuesta de fuente financiamiento: ', response?.data);
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
            form.IdFuenteFinanciamiento = 3
            form.IdTipoFinanciamiento = 2
            onChangeTipoFinancimiento();
            form.EstablecimientoSalud = response?.data?.data?.EESS;

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
                    form.IdFuenteFinanciamiento = 1
                    form.IdTipoFinanciamiento = 1
                    onChangeTipoFinancimiento();
                }
            }
        }
    } catch (error) {
        console.error('Error al obtener datos del SIS:', error);
        return null;
    }
}

const onChangeTipoFinancimiento = () => {
    fuentesFinancimiento.value = appStore.configuracionFuenteFinanciamiento.filter(row => row.IdTipoFinanciamiento === form.IdTipoFinanciamiento);
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

const onSubmit = async () => {
    if (form.TriajePresionSis === '' || form.TriajePresionSis === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo PRESIÓN ARTERIAL es requerido', 'error');
    }
    if (form.TriajePresionDia === '' || form.TriajePresionDia === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo PRESIÓN ARTERIAL es requerido', 'error');
    }
    if (form.TriajeFrecCardiaca === '' || form.TriajeFrecCardiaca === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo FRECUENCIA CARDIACA es requerido', 'error');
    }
    if (form.TriajeFrecRespiratoria === '' || form.TriajeFrecRespiratoria === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo FRECUENCIA RESPIRATORIA es requerido', 'error');
    }
    if (form.TriajeSaturacionOxigeno === '' || form.TriajeSaturacionOxigeno === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo SATURACIÓN DE OXÍGENO es requerido', 'error');
    }
    if (form.TriajeTemperatura === '' || form.TriajeTemperatura === null) {
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
        form.TriajePresion = form.TriajePresionSis + '/' + form.TriajePresionDia;
        if (props.recordId) {
            form.EstadoTriaje = 'Modificado';
        } else {
            form.EstadoTriaje = 'Registrado';
        }

        const formData = {
            id: props.recordId ?? null,

            IdTipoFinanciamiento: form.IdTipoFinanciamiento,
            IdFuenteFinanciamiento: form.IdFuenteFinanciamiento,
            IdPaciente: form.IdPaciente,
            IdTipoServicio: form.IdTipoServicio,
            IdEspecialidad: form.IdEspecialidad,
            IdProgramaInstitucion: form.IdProgramaInstitucion,
            IdTipoDocumento: form.IdTipoDocumento,

            FechaDocumento: form.FechaDocumento,
            FechaVencimiento: form.FechaVencimiento,
            NumeroDocumento: form.NumeroDocumento,
            NumeroExpedienteTramiteDocumentario: form.NumeroExpedienteTramiteDocumentario,
            Estado: form.Estado,
            FechaRegistro: form.FechaRegistro,
            IdEmpleadoRegistra: form.IdEmpleadoRegistra,

        };

        axios.post(`/${props.resource}`, formData)
            .then(response => {
                const data = response.data;
                if(data.success) {
                    emit('success');
                    closeDialog();
                    showToastSuccess(data.mensaje)
                } else {
                    showToastError(data.mensaje)
                }
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
    tiposDocumento.value = [];
}

const clearSearchDocumento = () => {
    nroDocumento.value = '';
    inputDocumentoRef.value?.focus();
};

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :recordId="form.id"
               :viewRecord="isViewMode"
               :loading="isModalLoading"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-lg">

        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-12">
                <div class="p-3 border rounded" style="border: 1px solid #7367f0!important; height: 125px;">
                    <BaseCombo v-model="tipoDocumento" :options="tiposDocumento" optionLabel="label" optionValue="value"
                               label="BUSCAR POR:" :disabled="isViewMode"/>
                    <div class="d-flex align-items-center mt-2">

                        <div class="input-group">
                            <input type="text" class="form-control" ref="inputDocumentoRef"
                                   placeholder="NÚMERO DE BÚSQUEDA" aria-label="Text input" :disabled="isViewMode"
                                   v-model="nroDocumento" autocomplete="off" autofocus
                                   @keydown.enter="buscarPacienteTriajeEmergencia">
                            <button class="btn btn-primary waves-effect" type="button" id="btnCosultarPacienteCita"
                                    :disabled="isViewMode" name="btnCosultarPacienteCita"
                                    style=" height: 2rem !important;"
                                    @click="buscarPacienteTriajeEmergencia">
                                <span class="ti ti-search" style="font-size: 17px;"></span>
                            </button>
                            <button class="btn btn-danger" @click="clearSearchDocumento">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Perfil paciente -->
            <div v-if="loadingDatosPaciente" class="col-xl-5 col-lg-4 col-md-12">
                <div class="p-3 border rounded" style="border: 1px solid #7367f0!important;">
                    <div class="d-flex align-items-start">
                        <div class="me-3 position-relative">
                            <img :src="fotoPaciente" :key="fotoPaciente" alt="Foto paciente" class="img-fluid rounded"
                                 style="width: 98px; height: 98px;"/>
                            <div v-if="loadingFoto"
                                 class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-light rounded">
                                <div class="spinner-border text-primary" role="status"
                                     style="width: 1.5rem; height: 1.5rem;"></div>
                            </div>
                        </div>
                        <div style="font-size: 0.7rem; text-transform: uppercase;">
                            <label><b>INFORMACIÓN GENERAL:</b></label><br/>
                            <strong class="text-success">{{ nombrePaciente }}</strong><br/>
                            <strong>{{ dniPaciente }}</strong><br/>
                            <strong>{{ hcPaciente }}</strong><br/>
                            <strong>{{ edadPaciente }}</strong><br/>
                            <strong>{{ sexoPaciente }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacto -->
            <div v-if="loadingDatosPaciente" class="col-xl-3 col-lg-6 col-md-12">
                <div class="p-3 border rounded" style="border: 1px solid #7367f0!important;">
                    <label><b>DATOS DE CONTACTO:</b></label>
                    <div class="mb-2">
                        <InputText v-model="correoPaciente" placeholder="CORREO ELECTRÓNICO" class="w-full"
                                   :disabled="isViewMode"/>
                    </div>
                    <div class="flex gap-2">
                        <InputText v-model="telefonoPaciente" placeholder="NÚMERO DE CELULAR" class="flex-1"
                                   :disabled="isViewMode"/>
                    </div>
                </div>
            </div>
        </div>

        <form @submit.prevent="guardar">


            <div class="row">
                <BaseDivider title="I. DETALLE DE FINANCIMIENTO"/>
                <div class="col-xl-3 col-lg-6 col-md-12">
                    <BaseCombo v-model="form.IdTipoFinanciamiento" :options="appStore.configuracionTipoFinanciamiento"
                               label="Tipo Financiamiento" option-label="label" option-value="id" :filter="true"
                               :disabled="isViewMode" placeholder="Seleccione una Forma de Ingreso"
                               @update:modelValue="onChangeTipoFinancimiento"/>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-12">
                    <BaseCombo v-model="form.IdFuenteFinanciamiento" :options="fuentesFinancimiento"
                               label="Fuente Financiamiento" option-label="label" option-value="id" :filter="true"
                               :disabled="isViewMode" placeholder="Seleccione una Fuente de Financiamiento"/>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-12">
                    <BaseCombo v-model="form.IdTipoServicio" :options="appStore.configuracionTipoServicios"
                               label="Tipo Servicio" option-label="label" option-value="id" :filter="true"
                               :disabled="isViewMode" placeholder="Seleccione un Tipo de Servicio"/>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-12">
                    <BaseCombo v-model="form.IdEspecialidad" :options="appStore.configuracionEspecialidades"
                               label="Especialidad" option-label="label" option-value="id" :filter="true"
                               :disabled="isViewMode" placeholder="Seleccione una Especialidad"/>
                </div>

            </div>

            <div class="row">
                <BaseDivider title="II. DATOS DE FINANCIAMIENTO"/>

                <div class="col-xl-3 col-lg-6 col-md-12">
                    <BaseCombo v-model="form.IdProgramaInstitucion" :options="appStore.programasInstituciones"
                               label="Institución" option-label="label" option-value="id" :filter="true"
                               :disabled="isViewMode"
                               placeholder="Seleccione una Institución"/>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-12">
                    <BaseCombo v-model="form.IdTipoDocumento" :options="appStore.programasTiposDocumentos"
                               label="Tipo de Documento" option-label="label" option-value="id" :filter="true"
                               :disabled="isViewMode" placeholder="Seleccione un Tipo de Documento"/>
                </div>

                <!-- Presión Arterial -->
                <div class="col-xl-3 col-lg-6 col-md-12">
                    <BaseInput v-model="form.FechaDocumento" :label="'Fecha Documento'" :disabled="isViewMode" required
                               type="date"/>
                </div>

                <!-- Presión Arterial -->
                <div class="col-xl-3 col-lg-6 col-md-12">
                    <BaseInput v-model="form.FechaVencimiento" :label="'Fecha Vencimiento'" :disabled="isViewMode"
                               required type="date"/>
                </div>

                <!-- Presión Arterial -->
                <div class="col-xl-4 col-lg-6 col-md-12">
                    <BaseInput v-model="form.NumeroDocumento" :label="'Número Documento'" :disabled="isViewMode"
                               required type="text"/>
                </div>

                <!-- Presión Arterial -->
                <div class="col-xl-4 col-lg-6 col-md-12">
                    <BaseInput v-model="form.NumeroExpedienteTramiteDocumentario"
                               :label="'Nro Expediente Tramite Documentario'" :disabled="isViewMode" required
                               type="text"/>
                </div>

                <div class="col-xl-4 col-lg-6 col-md-12">
                    <BaseCombo v-model="form.Estado" :options="estados" label="Estado" option-label="label"
                               option-value="id" :filter="true" :disabled="isViewMode"
                               placeholder="Seleccione el Estado"/>
                </div>


            </div>

            <!-- <div class="row">
                <BaseDivider title="III. Archivo Adjunto"/>


                <div class="col-xl-12 col-lg-12 col-md-12">
                    <BaseInput  :label="'Archivo Adjunto'"
                                       :disabled="isViewMode" required type="file"

                                       />
                </div>


            </div> -->

            <div v-if="!isViewMode" class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary" :disabled="loadingSubmit">
                    {{ loadingSubmit ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </BaseModal>
</template>
