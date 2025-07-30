<script setup>
import {ref, computed} from 'vue';
import axios from 'axios';
import BaseModal from '@/components/BaseModal.vue';
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import Panel from 'primevue/panel';
import {useAppStore} from "../../../../stores/useAppStore.js";
import WCheckbox from "@/components/WCheckbox/WCheckbox.vue";
import Select from "primevue/select";
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

let loadingCatalogoEmergencia = ref(false);
let debounceTimeout = null;

let isViewMode = computed(() => props.viewRecord);
let title = ref('');
let errors = ref({});
let form = ref({});
let especialidades = ref([]);
let especialidadesPrimarias = ref([]);
let catalogoEmergencia = ref([]);
let parametros = ref([]);

const initForm = () => {
    errors.value = {};
    form.value = {
        id: null,
        IdServicio: null,
        IdTipoServicio: 1,
        IdDepartamento: null,
        IdEspecialidadPrimaria: null,
        IdEspecialidad: null,
        Codigo: null,
        Nombre: null,
        SVG: null,
        IdProducto: null,
        soloTipoSexo: 3,
        maximaEdad: 0,
        codigoServicioSEM: null,
        ubicacionSEM: null,
        codigoServicioHIS: null,
        CostoCeroCE: false,
        MinimaEdad: 0,
        idEstado: true,
        Triaje: false,
        EsObservacionEmergencia: false,
        UsaModuloNinoSano: false,
        UsaModuloMaterno: false,
        UsaGalenHos: false,
        UsaGalenHosEmergencia: false,
        TipoEdad: 1,
        UsaFUA: false,
        codigoServicioSuSalud: null,
        codigoServicioFUA: null,
        FuaTipoAnexo2015: null,
        MaxCuposCitasAdelantadas: 0,
        MaxCuposAdicionales: 0,
        codigoServicioRenaes: null,
        TiempoPromProcedimiento: null,
        terapiaTipo: 0,
        terapiaGhoraInicio: null,
        terapiaGduracion: 0,
        terapiaNpacientes: 0,
        IdEspecialidadGroup: null,
        CodigoPrestacionSIS: null,
        IdTipoUsoServicio: null,
        CuposRefCon: null,
        CodigoCE: null,
        TienePuntoDeCarga: false,
        IdTipoConsultorio: null,
        TieneEspecialidadRelacionada: false
    };
}

const handleOpen = async () => {
    isModalLoading.value = true
    initForm();
    title.value = 'Nuevo servicio';
    let par = appStore.coreParametros.find(row => row.IdParametro === 359);
    if (par) {
        const descripcion = par.Descripcion;
        const opciones = descripcion.split(',')
            .map(item => {
                const [value, label] = item.split('=');
                return {
                    value: parseInt(value.trim()),
                    label: value.trim() + ' - ' + label.trim()
                };
            });
        parametros.value = opciones;
    }
    if (props.recordId) {
        try {
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form.value, data.data)
            let especialidad = appStore.configuracionEspecialidades.find(row => row.value === form.value.IdEspecialidad);
            if (especialidad) {
                form.value.IdDepartamento = especialidad.IdDepartamento;
                form.value.IdEspecialidadPrimaria = especialidad.IdEspecialidadPrimaria;
                especialidadesPrimarias.value = appStore.configuracionEspecialidadesPrimarias.filter(row => row.IdDepartamento === form.value.IdDepartamento);
                especialidades.value = appStore.configuracionEspecialidades.filter(row => row.IdEspecialidadPrimaria === form.value.IdEspecialidadPrimaria);
            }
            if (form.value.IdTipoServicio !== 1) {
                await fetchOptionsCatalogoEmergenciaPorCodigo(form.value.IdProducto);
            }
            title.value = 'Editar servicio'
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

const changeTipoServicio = () => {
    form.value.IdDepartamento = null;
    form.value.IdEspecialidadPrimaria = null;
    form.value.IdEspecialidad = null;
    especialidadesPrimarias.value = [];
    especialidades.value = [];
    form.value.UsaGalenHosEmergencia = false;
    form.value.UsaGalenHos = false;
}

const changeDepartamento = () => {
    especialidadesPrimarias.value = appStore.configuracionEspecialidadesPrimarias.filter(row => row.IdDepartamento === form.value.IdDepartamento);
    form.value.IdEspecialidadPrimaria = null;
    form.value.IdEspecialidad = null;
    especialidades.value = [];
}

const changeEspecialidadPrimaria = () => {
    form.value.IdEspecialidad = null;
    especialidades.value = [];
    if (form.value.IdEspecialidadPrimaria !== null) {
        especialidades.value = appStore.configuracionEspecialidades.filter(row => row.IdEspecialidadPrimaria === form.value.IdEspecialidadPrimaria);
    }
}

const changeUsaFua = () => {
    form.value.FuaTipoAnexo2015 = !form.value.UsaFUA ? null : 1
    form.value.UsaGalenHos = false;
    form.value.UsaGalenHosEmergencia = false;
}

const fetchOptionsCatalogoEmergenciaPorCodigo = async (codigo) => {
    loadingCatalogoEmergencia.value = true;
    try {
        const response = await axios.get(`/configuracion/servicios/filtrar_catalogo_emergencia_por_codigo/${codigo}`);
        catalogoEmergencia.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingCatalogoEmergencia.value = false;
    }
}

const fetchOptionsCatalogoEmergencia = async (query) => {
    if (!query) {
        catalogoEmergencia.value = [];
        return;
    }
    loadingCatalogoEmergencia.value = true;
    try {
        const response = await axios.post('/configuracion/servicios/filtrar_catalogo_emergencias', {buscar: query});
        catalogoEmergencia.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingCatalogoEmergencia.value = false;
    }
}

const onFilterCatalogoEmergencia = (event) => {
    if (event.value.length > 3) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsCatalogoEmergencia(event.value);
        }, 500);
    }
}

const changeCatalogoEmergencia = () => {

}

const onSubmit = async () => {
    if (form.value.IdDepartamento === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo departamento es requerido', 'error');
    }

    if (form.value.IdEspecialidad === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo especialidad es requerido', 'error');
    }

    // if (form.value.Codigo === null) {
    //     return showAlert('ERROR DURANTE EL PROCESO', 'El campo código del servicio es requerido', 'error');
    // }

    if (form.value.Nombre === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo nombre del servicio es requerido', 'error');
    }

    if (form.value.codigoServicioHIS === null && form.value.IdTipoServicio === 1) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo código de servicio HIS es requerido', 'error');
    }

    if (form.value.IdProducto === null && form.value.IdTipoServicio !== 1) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo estancia por día es requerido', 'error');
    }

    if (Number(form.value.maximaEdad) === 0) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo edad máxima no puede ser cero', 'error');
    }

    if (form.value.terapiaTipo === 0) {
        form.value.terapiaGhoraInicio = '';
        form.value.terapiaGduracion = 0;
        form.value.terapiaNpacientes = 0;
    }

    const accionConfirmacion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PAR EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensajeConfirmacion = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accionConfirmacion}?`;
    const confirmado = await showAlertConfirmacion(
        'VALIDACIÓN DE DATOS REALIZADOS', mensajeConfirmacion,
        'warning'
    );

    // Si el usuario confirma, procedemos con el guardado
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
               :header="title"
               size="modal-lg">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <base-combo label="Tipo de servicio"
                                    v-model="form.IdTipoServicio"
                                    :options="appStore.configuracionTipoServicios"
                                    :disabled="isViewMode || !!recordId"
                                    filter
                                    :show-clear="false"
                                    @update:model-value="changeTipoServicio"></base-combo>
                    </div>
                    <div class="col-12 col-md-4">
                        <base-combo label="Departamento"
                                    v-model="form.IdDepartamento"
                                    :options="appStore.configuracionDepartamentosHospital"
                                    :disabled="isViewMode"
                                    filter
                                    @update:model-value="changeDepartamento"></base-combo>
                    </div>
                    <div class="col-12 col-md-4">
                        <base-combo label="Especialidad primaria"
                                    v-model="form.IdEspecialidadPrimaria"
                                    :options="especialidadesPrimarias"
                                    :disabled="isViewMode"
                                    filter
                                    @update:model-value="changeEspecialidadPrimaria"></base-combo>
                    </div>
                    <div class="col-12 col-md-4">
                        <base-combo label="Especialidad"
                                    v-model="form.IdEspecialidad"
                                    :options="especialidades"
                                    :disabled="isViewMode"
                                    filter></base-combo>
                    </div>
                    <div class="col-12 col-sm-2">
                        <BaseInput v-model="form.Codigo"
                                   label="Código"
                                   :maxlength="6"
                                   :disabled="true"
                                   :error="errors.Codigo"/>
                    </div>
                    <div class="col-12 col-sm-6">
                        <BaseInput v-model="form.Nombre"
                                   label="Nombre"
                                   :maxlength="50"
                                   :disabled="isViewMode"
                                   :error="errors.Nombre"/>
                    </div>
                    <div class="col-12 col-md-6">
                        <base-combo label="Código de servicio FUA"
                                    v-model="form.codigoServicioFUA"
                                    :options="appStore.configuracionUpsRenaes"
                                    :disabled="isViewMode"
                                    filter></base-combo>
                    </div>
                    <div class="col-12 col-md-6">
                        <base-combo label="Código de servicio SuSalud"
                                    v-model="form.codigoServicioSuSalud"
                                    :options="appStore.configuracionSuSaludServicios"
                                    :disabled="isViewMode"
                                    filter></base-combo>
                    </div>
                    <div class="col-12 col-md-4">
                        <base-combo label="Tipo de consultorio"
                                    v-model="form.IdTipoConsultorio"
                                    :options="appStore.configuracionTipoConsultorios"
                                    :disabled="isViewMode"></base-combo>
                    </div>
                    <div class="col-12 col-md-4" style="display: flex; align-items: flex-end">
                        <w-checkbox v-model="form.TienePuntoDeCarga"
                                    :readonly="isViewMode"
                                    label="El servicio es un punto de carga">
                        </w-checkbox>
                    </div>
                    <div class="col-12 col-md-4" style="display: flex; align-items: flex-end">
                        <w-checkbox v-model="form.TieneEspecialidadRelacionada"
                                    :readonly="isViewMode"
                                    label="Tiene Especialidad Relacionada">
                        </w-checkbox>
                    </div>
                    <div class="col-12 col-md-4" style="display: flex; align-items: flex-end">
                        <w-checkbox v-model="form.idEstado"
                                    :readonly="isViewMode"
                                    label="Habilitado">
                        </w-checkbox>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <base-combo label="Tipo de sexo"
                                    v-model="form.soloTipoSexo"
                                    :options="appStore.configuracionTipoSexos"
                                    :disabled="isViewMode"
                                    :show-clear="false"></base-combo>
                    </div>
                    <div class="col-12 col-sm-3">
                        <base-combo label="Rango de edad"
                                    v-model="form.TipoEdad"
                                    :options="appStore.configuracionTipoEdades"
                                    :disabled="isViewMode"
                                    :show-clear="false"></base-combo>
                    </div>
                    <div class="col-12 col-sm-3">
                        <BaseInput v-model="form.MinimaEdad"
                                   label="desde"
                                   :disabled="isViewMode"
                                   :error="errors.MinimaEdad"/>
                    </div>
                    <div class="col-12 col-sm-3">
                        <BaseInput v-model="form.maximaEdad"
                                   label="hasta"
                                   :disabled="isViewMode"
                                   :error="errors.maximaEdad"/>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <Panel header="SIS">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <w-checkbox v-model="form.UsaFUA"
                                        :readonly="isViewMode"
                                        label="Se usa formato FUA"
                                        @update:model-value="changeUsaFua">
                            </w-checkbox>
                        </div>
                        <div class="col-12 col-md-9">
                            <base-combo label="Anexo 2015"
                                        v-model="form.FuaTipoAnexo2015"
                                        :options="parametros"
                                        :show-clear="false"
                                        :disabled="isViewMode || !form.UsaFUA"></base-combo>
                        </div>
                    </div>
                </Panel>
            </div>
            <div class="col-12 mt-4" v-if="form.IdTipoServicio === 1">
                <Panel header="Datos para consultorio externo">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <w-checkbox v-model="form.CostoCeroCE"
                                        :readonly="isViewMode"
                                        label="El consultorio no cobra por atención Consultorio a particulares">
                            </w-checkbox>
                        </div>
                        <div class="col-12 col-md-6">
                            <w-checkbox v-model="form.Triaje"
                                        :readonly="isViewMode"
                                        label="El consultorio necesita triaje antes de registrar atención">
                            </w-checkbox>
                        </div>
                        <div class="col-12 col-md-6">
                            <w-checkbox v-model="form.UsaGalenHos"
                                        :readonly="isViewMode || !form.UsaFUA"
                                        label="Se emite Formato FUA desde CITAS">
                            </w-checkbox>
                        </div>
                        <div class="col-12 col-md-6">
                            <w-checkbox v-model="form.UsaModuloNinoSano"
                                        label="El consultorio usa módulo de niño sano">
                            </w-checkbox>
                        </div>
                        <div class="col-12 col-md-6">
                            <w-checkbox v-model="form.UsaModuloMaterno"
                                        label="El consultorio usa módulo materno">
                            </w-checkbox>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <base-combo label="Código servicio HIS"
                                        v-model="form.codigoServicioHIS"
                                        :options="appStore.configuracionUpsServicios"
                                        filter
                                        :disabled="isViewMode"></base-combo>
                        </div>
                        <div class="col-12 col-md-4">
                            <BaseInput v-model="form.MaxCuposCitasAdelantadas"
                                       label="Máximo cupos citas adelantadas"
                                       :disabled="isViewMode"
                                       :error="errors.MaxCuposCitasAdelantadas"/>
                        </div>
                        <div class="col-12 col-md-4">
                            <BaseInput v-model="form.MaxCuposAdicionales"
                                       label="Máximo cupos adicionales"
                                       :disabled="isViewMode"
                                       :error="errors.MaxCuposAdicionales"/>
                        </div>
                    </div>
                </Panel>
            </div>
            <div class="col-12 mt-4" v-if="form.IdTipoServicio === 1">
                <Panel header="Datos si el consultorio es terapia">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <base-combo label="Tipo de terapia"
                                        v-model="form.terapiaTipo"
                                        :options="appStore.configuracionTipoTerapias"
                                        :show-clear="false"
                                        :disabled="isViewMode"></base-combo>
                        </div>
                        <div class="col-12 col-md-3">
                            <BaseInput v-model="form.terapiaGhoraInicio"
                                       label="Hora inicio de Terapia Grupal"
                                       :disabled="isViewMode || form.terapiaTipo === 0"
                                       type="time"
                                       :error="errors.terapiaGhoraInicio"/>
                        </div>
                        <div class="col-12 col-md-3">
                            <BaseInput v-model="form.terapiaGduracion"
                                       label="Duración de la Terapia Grupal"
                                       :disabled="isViewMode || form.terapiaTipo === 0"
                                       :error="errors.terapiaGduracion"/>
                        </div>
                        <div class="col-12 col-md-3">
                            <BaseInput v-model="form.terapiaNpacientes"
                                       label="N° Pacientes por Terapia Grupal"
                                       :disabled="isViewMode || form.terapiaTipo === 0"
                                       :error="errors.terapiaNpacientes"/>
                        </div>
                    </div>
                </Panel>
            </div>
            <div class="col-12 mt-4" v-if="form.IdTipoServicio !== 1">
                <Panel header="Datos para hospitalización/emergencia">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Seleccionar estancia por día</label>
                            <Select v-model="form.IdProducto"
                                    :options="catalogoEmergencia"
                                    optionLabel="Nombre"
                                    optionValue="IdProducto"
                                    filter
                                    filterPlaceholder="Buscar..."
                                    @filter="onFilterCatalogoEmergencia"
                                    :loading="loadingCatalogoEmergencia"
                                    placeholder="Seleccione una opción"
                                    :showClear="true"
                                    class="w-full"
                                    size="small"
                                    style="width: 100%;"
                                    :autoFilterFocus="true"
                                    @update:model-value="changeCatalogoEmergencia"></Select>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.codigoServicioSEM"
                                       label="Código servicio SEM"
                                       :disabled="isViewMode"
                                       :error="errors.codigoServicioSEM"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.ubicacionSEM"
                                       label="Ubicación SEM"
                                       :disabled="isViewMode"
                                       :error="errors.ubicacionSEM"/>
                        </div>
                        <div class="col-12">
                            <w-checkbox v-model="form.EsObservacionEmergencia"
                                        label="El servicio es Observación de Emergencia">
                            </w-checkbox>
                        </div>
                        <div class="col-12">
                            <w-checkbox v-model="form.UsaGalenHosEmergencia"
                                        :readonly="isViewMode || !form.UsaFUA"
                                        label="Se emite formato FUA desde Admisión Emergencia">
                            </w-checkbox>
                        </div>
                    </div>
                </Panel>
            </div>
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.SVG"-->
            <!--                                       label="SVG"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.SVG" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.IdProducto"-->
            <!--                                       label="Producto"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.IdProducto" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.CostoCeroCE"-->
            <!--                                       label="Costo cero CE"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.CostoCeroCE" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.UsaGalenHos"-->
            <!--                                       label="Usa GalenHos"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.UsaGalenHos" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.codigoServicioRenaes"-->
            <!--                                       label="Código servicio Renaes"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.codigoServicioRenaes" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.TiempoPromProcedimiento"-->
            <!--                                       label="Tiempo promedio procedimiento"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.TiempoPromProcedimiento" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.IdEspecialidadGroup"-->
            <!--                                       label="Código grupo de especialidad"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.IdEspecialidadGroup" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.CodigoPrestacionSIS"-->
            <!--                                       label="Código prestación SIS"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.CodigoPrestacionSIS" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.IdTipoUsoServicio"-->
            <!--                                       label="Tipo de uso de servicio"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.IdTipoUsoServicio" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.CuposRefCon"-->
            <!--                                       label="Cupos RefCon"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.CuposRefCon" required/>-->
            <!--                        </div>-->
            <!--                        <div class="col-12 col-sm-6 col-md-4">-->
            <!--                            <BaseInput v-model="form.CodigoCE"-->
            <!--                                       label="Código CE"-->
            <!--                                       :disabled="isViewMode"-->
            <!--                                       :error="errors.CodigoCE" required/>-->
            <!--                        </div>-->
        </div>
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
        </div>
    </BaseModal>
</template>
