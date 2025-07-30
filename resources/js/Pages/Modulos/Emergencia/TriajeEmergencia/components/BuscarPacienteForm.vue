<script setup>

import {ref, watch} from "vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import WInput from "@/components/WInput/WInput.vue";
import {useAppStore} from '@/stores/useAppStore';
import axios from "axios";
import ModalFormulario from "../../AdmisionEmergencia/AdmisionEmergenciaNN.vue";
import BaseModalFull from "../../../../../components/BaseModalFull.vue";
import BaseModal from "../../../../../components/BaseModal.vue";

const props = defineProps({
    pacienteId: [Number, null],
    accion: String,
    viewRecord: Boolean,
})
const emit = defineEmits(['success', 'nueva-busqueda']);

let appStore = useAppStore();
let loading = ref(false);
let loadingFoto = ref(false);
let pacienteSeleccionado = ref();
let tiposDocumento = ref([]);
let inputDocumentoRef = ref(null);
let tipoDocumento = ref(1);
let nroDocumento = ref();
let busquedaInterna = ref(false); // Nueva bandera
let isUpdate = ref(false);
let isDialogOpen = ref(false);
let title = ref('');
let errors = ref(null);
let form = ref({});

tiposDocumento.value = [];


const initForm = () => {
    loading.value = false;
    form.value = {
        id: null,
        nombre: '',
        dni: '',
        hc: '',
        edad: '',
        sexo: '',
        IdTipoSexo: null,
        correo: '',
        telefono: '',
        foto: null,
        years: null,
        months: null,
        days: null,
        IdDocIdentidad: null,
        NroDocumento: null,
        ApellidoPaterno: null,
        ApellidoMaterno: null,
        PrimerNombre: null,
        SegundoNombre: null,
        TercerNombre: null,
        FechaNacimiento: null,
        IdFuenteFinanciamiento: null,
        IdPaciente: null,
    }
    title.value = 'Buscar paciente';
    tipoDocumento.value = 1;
    nroDocumento.value = null;
}

const buscarPacienteTriajeEmergencia = async (consultarSis = true) => {
    if (!tipoDocumento.value) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'EL CAMPO DE TIPO DE DOCUMENTO ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.', 'error');
        return;
    }
    nroDocumento.value = (nroDocumento.value || '').replace(/\s+/g, '');
    if (tipoDocumento.value === 1) {
        if (!nroDocumento.value || nroDocumento.value.length !== 8) {
            showAlert(
                'VALIDACIÓN DE CAMPO REALIZADO',
                'EL NÚMERO DE DOCUMENTO DEBE TENER EXACTAMENTE 8 CARACTERES.',
                'error'
            );
            return;
        }
    }

    let {data} = await axios.post(`/emergencia/triaje-emergencia/validar_triaje_emergencia_paciente`, {
        IdDocIdentidad: tipoDocumento.value,
        NroDocumento: nroDocumento.value,
    })
    if (!data.success) {
        return showAlert("VERIFICANDO DATOS ...", data.mensaje, "error");
        // console.log(data.mensaje)
        // return ;
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

        // console.log('response.data')
        // console.log(response.data)
        // console.log('response.data')
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
        busquedaInterna.value = true; // Setea bandera antes de emitir
        emit('success', form.value);
    } catch (error) {
        if (error.response) {
            showAlert('ERROR', error.response.data.message || 'Error al buscar paciente.', 'error');
        } else {
            showAlert('ERROR', error.message || 'Error de conexión.', 'error');
        }
    }
}

async function actualizarDatosPaciente(datos) {
    try {
        loadingFoto.value = true
        loading.value = true
        form.value.foto = null
        const defaultFoto = datos.IdTipoSexo == 1
            ? '../../../assets/img/sexo1.gif'
            : '../../../assets/img/sexo2.gif'
        form.value.foto = defaultFoto ?? null
        form.value.IdTipoSexo = datos.IdTipoSexo

        const edadCompleta = calcularEdadCompleta(datos.FechaNacimiento)

        form.value.nombre = `${datos.ApellidoPaterno || ''} ${datos.ApellidoMaterno || ''} ${datos.PrimerNombre || ''} ${datos.SegundoNombre || ''} ${datos.TercerNombre || ''}`
        form.value.dni = `DNI: ${datos.NroDocumento}`
        form.value.hc = `HC: ${datos.NroHistoriaClinica ?? 'SIN DATOS'}`
        form.value.edad = `EDAD: ${edadCompleta.years} A, ${edadCompleta.months} M, ${edadCompleta.days} D`
        form.value.sexo = `SEXO: ${datos.IdTipoSexo === '1' ? 'MASCULINO' : 'FEMENINO'}`
        form.value.years = edadCompleta.years
        form.value.months = edadCompleta.months
        form.value.days = edadCompleta.days
        form.value.correo = datos.Email ?? ''
        form.value.telefono = datos.Telefono ?? ''
        form.value.id = datos.IdPaciente ?? null

        form.value.IdDocIdentidad = datos.IdDocIdentidad
        form.value.NroDocumento = datos.NroDocumento
        form.value.ApellidoPaterno = datos.ApellidoPaterno
        form.value.ApellidoMaterno = datos.ApellidoMaterno
        form.value.PrimerNombre = datos.PrimerNombre
        form.value.SegundoNombre = datos.SegundoNombre ?? null
        form.value.TercerNombre = datos.TercerNombre ?? null
        form.value.FechaNacimiento = datos.FechaNacimiento ?? null

    } catch (e) {
        console.error('Error cargando imagen:', e)
    } finally {
        loadingFoto.value = false
        loading.value = false
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

        if (response?.data.respuesta === 1) {
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
                if (form.value.id) {
                    form.value.IdFuenteFinanciamiento = 5
                }
            }
        }
    } catch (error) {
        console.error('Error al obtener datos del SIS:', error);
        return null;
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
    return {years: anios, months: meses, days: dias}
}

const handleOpen = () => {
    initForm();
    tiposDocumento.value = [...appStore.personaTipoDocumentosIdentidad];
    tiposDocumento.value.push({
        id: 11,
        value: -1,
        label: 'HISTORIA CLÍNICA',
        CodigoSUNASA: null,
        CodigoHIS: null,
        CodigoSIS: null
    })
}

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    isDialogOpen.value = false;
}

defineExpose({openDialog, closeDialog})

</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :header="title"
               :loading="loading"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-xs">
        <div class="row">
            <div class="col-12">
                <BaseCombo v-model="tipoDocumento"
                           :options="tiposDocumento"
                           optionLabel="label"
                           optionValue="value"
                           label="BUSCAR POR:" :disabled="viewRecord"/>
                <div class="d-flex align-items-center mt-2">
                    <div class="input-group">
                        <input type="text"
                               class="form-control"
                               ref="inputDocumentoRef"
                               placeholder="NÚMERO DE BÚSQUEDA"
                               aria-label="Text input"
                               :disabled="viewRecord"
                               v-model="nroDocumento"
                               autocomplete="off"
                               autofocus
                               @keydown.enter="buscarPacienteTriajeEmergencia">
                        <button class="btn btn-primary waves-effect" type="button" id="btnConsultarPacienteCita"
                                :disabled="viewRecord" name="btnConsultarPacienteCita"
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
    </BaseModal>
</template>
