<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'
import {useAppStore} from "../../../../stores/useAppStore.js";
import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import BaseDatePicker from "@/components/WDatePicker/WDatePicker.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import WCheckbox from "@/components/WCheckbox/WCheckbox.vue";
import Select from "primevue/select";
import Tabs from "primevue/tabs";
import Tab from "primevue/tab";
import TabList from "primevue/tablist";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import MyLib from '../../../../mixins/lib';
import ColegioEspecialidad from "./ColegioEspecialidad.vue";
import WButton from "@/components/WButton/WButton.vue";
import {showToastSuccess} from "../../../../utils/alert.js";

const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let appStore = useAppStore();
let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let refDialogColegioEspecialidad = ref()
let isViewMode = computed(() => props.viewRecord);
let loadingCentroPobladoDomicilio = ref(false);
let loadingCentroPobladoProcedencia = ref(false);
let loadingCentroPobladoNacimiento = ref(false);

let title = ref('');
let errors = ref({});
let form = ref({});
let empleados = ref([]);
let especialidades = ref([]);
let colegioEspecialidades = ref([]);
let centroPobladoDomicilio = ref([]);
let centroPobladoProcedencia = ref([]);
let centroPobladoNacimiento = ref([]);

let ubigeoDomicilios = ref([]);
let ubigeoProcedencia = ref([]);
let ubigeoNacimiento = ref([]);


const initForm = () => {
    errors.value = {};
    form.value = {
        ApellidoPaterno: null,
        ApellidoMaterno: null,
        PrimerNombre: null,
        SegundoNombre: null,
        TercerNombre: null,
        FechaNacimiento: null,
        FechaNacimientoString: null,

        NroDocumento: null,
        Telefono: null,
        DireccionDomicilio: null,
        Autogenerado: null,
        IdTipoSexo: null,
        IdProcedencia: null,
        IdGradoInstruccion: null,
        IdEstadoCivil: null,
        IdDocIdentidad: 1,

        IdTipoOcupacion: null,
        IdCentroPobladoNacimiento: null,
        IdCentroPobladoDomicilio: null,
        NombrePadre: null,
        NombreMadre: null,
        NroHistoriaClinica: null,
        IdTipoNumeracion: 2,
        IdCentroPobladoProcedencia: null,
        Observacion: null,

        IdPaisDomicilio: null,
        IdPaisProcedencia: null,
        IdPaisNacimiento: null,
        IdDistritoProcedencia: null,
        IdDistritoDomicilio: null,
        IdDistritoNacimiento: null,

        FichaFamiliar: null,
        IdEtnia: null,
        GrupoSanguineo: null,
        FactorRh: null,
        UsoWebReniec: null,

        IdIdioma: null,
        Email: null,
        madreDocumento: null,
        madreApellidoPaterno: null,
        madreApellidoMaterno: null,
        madrePrimerNombre: null,
        madreSegundoNombre: null,
        NroOrdenHijo: null,

        madreTipoDocumento: 1,
        Sector: null,
        Sectorista: null,
        PacienteCrearNroAutogenerado: false,
        id_etnia: null,
        IdReligion: null,

        IdPAcienteSIGESA: null,
        IdFlagHC: null,
        ImagenFirma: null,
        ImagenFoto: null,
        ImagenFirma64: null,
        ImagenFoto64: null,
        esActivo: true,
        BuscadoPorReniec: false,
    }
}

const handleOpen = async () => {
    initForm();
    title.value = 'Nuevo paciente';

    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form.value, data.data)

            const [year, month, day] = form.value.FechaNacimientoString.split('-');
            form.value.FechaNacimiento = new Date(year, month - 1, day);

            let ubigeoDomicilio = appStore.configuracionUbigeos.find(row => row.id === form.value.IdDistritoDomicilio);
            if(ubigeoDomicilio) {
                ubigeoDomicilios.value.push(ubigeoDomicilio)
            }

            let unac = appStore.configuracionUbigeos.find(row => row.id === form.value.IdDistritoNacimiento);
            if(unac) {
                ubigeoNacimiento.value.push(unac)
            }

            let uproc = appStore.configuracionUbigeos.find(row => row.id === form.value.IdDistritoProcedencia);
            if(uproc) {
                ubigeoProcedencia.value.push(uproc)
            }
            title.value = props.viewRecord?'Ver paciente':'Editar paciente'
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

const successColegioEspecialidad = (data) => {
    form.value.Colegiatura = data.colegiatura;
    form.value.rne = data.codigo;
}

const onSubmit = async () => {

    if (form.value.NroDocumento === '' || form.value.NroDocumento === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo número de documento es requerido', 'error');
    }

    if (form.value.ApellidoPaterno === '' || form.value.ApellidoPaterno === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Apellido Paterno es requerido', 'error');
    }

    if (form.value.ApellidoMaterno === '' || form.value.ApellidoMaterno === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Apellido Materno es requerido', 'error');
    }

    if (form.value.PrimerNombre === '' || form.value.PrimerNombre === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Nombres es requerido', 'error');
    }

    if (form.value.SegundoNombre === '' || form.value.SegundoNombre === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Nombres es requerido', 'error');
    }

    if (form.value.FechaNacimiento === '' || form.value.FechaNacimiento === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Fecha de Nacimiento es requerido', 'error');
    }

    if (form.value.Telefono === '' || form.value.Telefono === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo teléfono es requerido', 'error');
    }

    if (form.value.IdGradoInstruccion === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo grado de instrucción es requerido', 'error');
    }

    if (form.value.IdReligion === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo religión es requerido', 'error');
    }

    if (form.value.IdIdioma === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo idioma es requerido', 'error');
    }

    if (form.value.IdEtnia === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo etnia es requerido', 'error');
    }

    if (form.value.IdDistritoDomicilio === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo ubigeo de domicilio es requerido', 'error');
    }

    if (form.value.DireccionDomicilio === null || form.value.DireccionDomicilio === '') {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo dirección de domicilio es requerido', 'error');
    }

    if (form.value.IdDistritoProcedencia === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo ubigeo de procedencia es requerido', 'error');
    }

    const accionConfirmacion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PAR EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensajeConfirmacion = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accionConfirmacion}?`;
    const confirmado = await showAlertConfirmacion(
        'VALIDACIÓN DE DATOS REALIZADOS', mensajeConfirmacion,
        'warning'
    );

    form.value.FechaNacimientoString = MyLib.formatDateToYMD(form.value.FechaNacimiento);

    if (confirmado) {
        loadingSubmit.value = true;

        await axios.post(`/${props.resource}`, form.value)
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
                }
            })
            .finally(() => {
                loadingSubmit.value = false;
            })
    }
};

const buscarUbigeoDomicilio = (valor) => {
    ubigeoDomicilios.value = appStore.configuracionUbigeos.filter(row =>
        row.label.toLowerCase().includes(valor.toLowerCase())
    )
}

const buscarUbigeoProcedencia = (valor) => {
    ubigeoProcedencia.value = appStore.configuracionUbigeos.filter(row =>
        row.label.toLowerCase().includes(valor.toLowerCase())
    )
}

const buscarUbigeoNacimiento = (valor) => {
    ubigeoNacimiento.value = appStore.configuracionUbigeos.filter(row =>
        row.label.toLowerCase().includes(valor.toLowerCase())
    )
}

const changeDistritoDomicilio = async () => {
    await fetchOptionsCentroPobladoDomicilio();
}

const fetchOptionsCentroPobladoDomicilio = async () => {
    loadingCentroPobladoDomicilio.value = true;
    try {
        const response = await axios.post('/filtrar_configuracion_centros_poblados', {buscar: form.value.IdDistritoDomicilio});
        centroPobladoDomicilio.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingCentroPobladoDomicilio.value = false;
    }
}

const changeDistritoProcedencia = async () => {
    await fetchOptionsCentroPobladoProcedencia();
}

const fetchOptionsCentroPobladoProcedencia = async () => {
    loadingCentroPobladoProcedencia.value = true;
    try {
        const response = await axios.post('/filtrar_configuracion_centros_poblados', {buscar: form.value.IdDistritoProcedencia});
        centroPobladoProcedencia.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingCentroPobladoProcedencia.value = false;
    }
}

const changeDistritoNacimiento = async () => {
    await fetchOptionsCentroPobladoNacimiento();
}

const fetchOptionsCentroPobladoNacimiento = async () => {
    loadingCentroPobladoNacimiento.value = true;
    try {
        const response = await axios.post('/filtrar_configuracion_centros_poblados', {buscar: form.value.IdDistritoNacimiento});
        centroPobladoNacimiento.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingCentroPobladoNacimiento.value = false;
    }
}

const buscarDni = async () => {
    isModalLoading.value = true;
    try {
        const {data} = await axios.post('/reniec/avanzado/consultar', {dni: form.value.NroDocumento});
        if (data.success) {
            form.value.ApellidoPaterno = data.data.ApellidoPaterno;
            form.value.ApellidoMaterno = data.data.ApellidoMaterno;
            const nombres = (data.data.Nombres || '').split(' ');
            form.value.PrimerNombre = nombres[0] || '';
            form.value.SegundoNombre = nombres[1] || '';
            form.value.TercerNombre = nombres[2] || '';
            form.value.IdTipoSexo = Number(data.data.Sexo);
            let estadoCivil = appStore.personaTipoEstadosCivil.find(row => row.IdReniec === Number(data.data.EstadoCivil));
            if(estadoCivil) {
                form.value.IdEstadoCivil = estadoCivil.id;
            }
            form.value.DireccionDomicilio = data.data.Direccion;

            let ubigeoDomicilioCodigo = Number(data.data.UbigeoDepartamentoDomicilio + data.data.UbigeoProvinciaDomicilio + data.data.UbigeoDistritoDomicilio);
            let ubigeoDomicilio = appStore.configuracionUbigeos.find(row => row.IdReniec === ubigeoDomicilioCodigo);
            if(ubigeoDomicilio) {
                ubigeoDomicilios.value.push(ubigeoDomicilio)
                form.value.IdDistritoDomicilio = ubigeoDomicilio.id;
            }

            let ubigeoNacimientoCodigo = Number(data.data.UbigeoDepartamentoNacimiento + data.data.UbigeoProvinciaNacimiento + data.data.UbigeoDistritoNacimiento);
            let unac = appStore.configuracionUbigeos.find(row => row.IdReniec === ubigeoNacimientoCodigo);
            if(unac) {
                ubigeoNacimiento.value.push(unac)
                form.value.IdDistritoNacimiento = unac.id;
            }

            const [day, month, year] = data.data.FechaNacimiento.split('/');
            form.value.FechaNacimiento = new Date(year, month - 1, day);

            form.value.ImagenFoto64 = data.data.ImagenFoto;
            form.value.ImagenFirma64 = data.data.ImagenFirma;
            form.value.BuscadoPorReniec = true;
        }
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        isModalLoading.value = false;
    }
}

const buscarDniMadre = async () => {
    isModalLoading.value = true;
    try {
        const {data} = await axios.post('/reniec/avanzado/consultar', {dni: form.value.madreDocumento});
        if (data.success) {
            form.value.madreApellidoPaterno = data.data.ApellidoPaterno;
            form.value.madreApellidoMaterno = data.data.ApellidoMaterno;
            const nombres = (data.data.Nombres || '').split(' ');
            form.value.madrePrimerNombre = nombres[0] || '';
            form.value.madreSegundoNombre = nombres[1] || '';
        }
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        isModalLoading.value = false;
    }
}

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :recordId="form.id"
               :viewRecord="isViewMode"
               :loading="isModalLoading"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-md">
        <Tabs value="0">
            <TabList>
                <Tab value="0">Datos del paciente</Tab>
                <Tab value="1">Datos de la madre o tutor</Tab>
                <Tab value="2">Dirección</Tab>
            </TabList>
            <TabPanels style="padding: 16px 0">
                <TabPanel value="0">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <base-combo label="Tipo de documento"
                                        v-model="form.IdDocIdentidad"
                                        :options="appStore.personaTipoDocumentosIdentidad"
                                        :show-clear="false"
                                        :disabled="isViewMode"></base-combo>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.NroDocumento"
                                       label="Número de documento"
                                       :disabled="isViewMode"
                                       :show-button-icon="form.IdDocIdentidad === 1 && !isViewMode"
                                       @click-button="buscarDni"
                                       :maxlength="8"
                                       :error="errors.NroDocumento"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.ApellidoPaterno"
                                       label="Apellido paterno"
                                       :disabled="isViewMode"
                                       :error="errors.ApellidoPaterno"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.ApellidoMaterno"
                                       label="Apellido materno"
                                       :disabled="isViewMode"
                                       :error="errors.ApellidoMaterno"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.PrimerNombre"
                                       label="Primer nombre"
                                       :disabled="isViewMode"
                                       :error="errors.PrimerNombre"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.SegundoNombre"
                                       label="Segundo nombre"
                                       :disabled="isViewMode"
                                       :error="errors.SegundoNombre"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.TercerNombre"
                                       label="Tercer nombre"
                                       :disabled="isViewMode"
                                       :error="errors.TercerNombre"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseDatePicker v-model="form.FechaNacimiento"
                                            label="Fecha de nacimiento"
                                            :disabled="isViewMode"
                                            :error="errors.FechaNacimiento"/>
                        </div>
                        <div class="col-12 col-md-3">
                            <base-combo label="Estado civil"
                                        v-model="form.IdEstadoCivil"
                                        :options="appStore.personaTipoEstadosCivil"
                                        :disabled="isViewMode"
                                        :show-clear="false"></base-combo>
                        </div>
                        <div class="col-12 col-md-6">
                            <base-combo label="Grado de instrucción"
                                        v-model="form.IdGradoInstruccion"
                                        :options="appStore.personaTipoGradosInstruccion"
                                        :disabled="isViewMode"
                                        :show-clear="false"
                                        filter></base-combo>
                        </div>
                        <div class="col-12 col-md-3">
                            <base-combo label="Etnia"
                                        v-model="form.IdEtnia"
                                        :options="appStore.personaTipoEtnias"
                                        :disabled="isViewMode"
                                        :show-clear="false"
                                        filter></base-combo>
                        </div>
                        <div class="col-12 col-md-3">
                            <base-combo label="Idioma materno"
                                        v-model="form.IdIdioma"
                                        :options="appStore.personaTipoIdiomas"
                                        :disabled="isViewMode"
                                        :show-clear="false"
                                        filter></base-combo>
                        </div>
                        <div class="col-12 col-md-3">
                            <base-combo label="Religión"
                                        v-model="form.IdReligion"
                                        :options="appStore.personaTipoReligiones"
                                        :disabled="isViewMode"
                                        :show-clear="false"
                                        filter></base-combo>
                        </div>
                        <div class="col-12 col-md-6">
                            <base-combo label="Ocupación"
                                        v-model="form.IdTipoOcupacion"
                                        :options="appStore.personaTipoOcupaciones"
                                        :disabled="isViewMode"
                                        :show-clear="false"
                                        filter></base-combo>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.Telefono"
                                       label="Teléfono"
                                       :disabled="isViewMode"
                                       :error="errors.Telefono"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <base-combo label="Sexo"
                                        v-model="form.IdTipoSexo"
                                        :options="appStore.personaTipoSexos"
                                        :disabled="isViewMode"
                                        :show-clear="false"></base-combo>
                        </div>
                        <div class="col-12 col-sm-6">
                            <BaseInput v-model="form.Observacion"
                                       label="Observaciones"
                                       :disabled="isViewMode"
                                       :error="errors.Observacion"/>
                        </div>
                        <div class="col-12">
                            <w-checkbox v-model="form.esActivo"
                                           :disabled="isViewMode"
                                           label="Activo">
                            </w-checkbox>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel value="1">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <base-combo label="Tipo de documento"
                                        v-model="form.madreTipoDocumento"
                                        :options="appStore.personaTipoDocumentosIdentidad"
                                        :show-clear="false"
                                        :disabled="isViewMode"></base-combo>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.madreDocumento"
                                       label="Número de documento"
                                       :show-button-icon="form.madreTipoDocumento === 1 && !isViewMode"
                                       @click-button="buscarDniMadre"
                                       :disabled="isViewMode"
                                       :maxlength="8"
                                       :error="errors.madreDocumento"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.madreApellidoMaterno"
                                       label="Apellido materno"
                                       :disabled="isViewMode"
                                       :error="errors.madreApellidoMaterno"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.madreApellidoPaterno"
                                       label="Apellido paterno"
                                       :disabled="isViewMode"
                                       :error="errors.madreApellidoPaterno"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.madrePrimerNombre"
                                       label="Primer nombre"
                                       :disabled="isViewMode"
                                       :error="errors.madrePrimerNombre"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.madreSegundoNombre"
                                       label="Segundo nombre"
                                       :disabled="isViewMode"
                                       :error="errors.madreSegundoNombre"/>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel value="2">
                    <div class="row">
                        <div class="col-8">
                            <base-combo label="Ubigeo nacimiento"
                                        v-model="form.IdDistritoNacimiento"
                                        :options="ubigeoNacimiento"
                                        :show-clear="false"
                                        :disabled="isViewMode"
                                        filter
                                        lazy
                                        @filter="buscarUbigeoNacimiento"
                                        @update:model-value="changeDistritoNacimiento"></base-combo>
                        </div>
                        <div class="col-4">
                            <base-combo label="Centro poblado"
                                        v-model="form.IdCentroPobladoNacimiento"
                                        :options="centroPobladoNacimiento"
                                        :show-clear="false"
                                        :disabled="isViewMode"
                                        filter></base-combo>
                        </div>
                        <div class="col-8">
                            <base-combo label="Ubigeo domicilio"
                                        v-model="form.IdDistritoDomicilio"
                                        :options="ubigeoDomicilios"
                                        :show-clear="false"
                                        :disabled="isViewMode"
                                        filter
                                        lazy
                                        @filter="buscarUbigeoDomicilio"
                                        @update:model-value="changeDistritoDomicilio"></base-combo>
                        </div>
                        <div class="col-4">
                            <base-combo label="Centro poblado"
                                        v-model="form.IdCentroPobladoDomicilio"
                                        :options="centroPobladoDomicilio"
                                        :show-clear="false"
                                        :disabled="isViewMode"
                                        filter></base-combo>
                        </div>
                        <div class="col-12">
                            <BaseInput v-model="form.DireccionDomicilio"
                                       label="Dirección"
                                       :disabled="isViewMode"
                                       :error="errors.DireccionDomicilio"/>
                        </div>
                        <div class="col-8">
                            <base-combo label="Ubigeo procedencia"
                                        v-model="form.IdDistritoProcedencia"
                                        :options="ubigeoProcedencia"
                                        :show-clear="false"
                                        :disabled="isViewMode"
                                        filter
                                        lazy
                                        @filter="buscarUbigeoProcedencia"
                                        @update:model-value="changeDistritoProcedencia"></base-combo>
                        </div>
                        <div class="col-4">
                            <base-combo label="Centro poblado"
                                        v-model="form.IdCentroPobladoProcedencia"
                                        :options="centroPobladoProcedencia"
                                        :show-clear="false"
                                        filter></base-combo>
                        </div>
                    </div>
                </TabPanel>
            </TabPanels>
        </Tabs>
        <div class="d-flex justify-content-end mt-4 gap-2">
            <w-button type="secondary"
                     label="Cerrar"
                     text
                     @click="closeDialog"
                     :disabled="loadingSubmit"/>
            <w-button type="primary"
                     :label="`${loadingSubmit?'Guardando...' : 'Guardar'}`"
                     @click="onSubmit"
                     v-if="!isViewMode"
                     :disabled="loadingSubmit"/>

            <colegio-especialidad ref="refDialogColegioEspecialidad"
                                  :especialidades="colegioEspecialidades"
                                  @success="successColegioEspecialidad"/>
        </div>
    </BaseModal>
</template>
