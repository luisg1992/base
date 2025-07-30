<script setup>

import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import BaseDivider from '@/components/BaseDivider.vue'
import BaseCombo from "@/components/WSelect/WSelect.vue";
import BaseInput from "@/components/WInput/WInput.vue";
import WButton from "@/components/WButton/WButton.vue";
import {useAppStore} from '@/stores/useAppStore'
import {computed, ref, watch} from "vue";
import Select from "primevue/select";
import axios from "axios";

const props = defineProps(['data', 'isViewMode', 'moduloActual']);
const emit = defineEmits(['onChangeMotivoIngreso', 'onChangeMedicoTopico']);

let appStore = useAppStore();

let medicoEspecialidadFiltrados = ref([]);
let serviciosFiltrados = ref([]);
let estadosFiltrados = ref([]);
let fuentesFinancimiento = ref([]);
let tipoGravedadAtenciones = ref([]);
let tiposFinancimiento = ref([]);
let errors = ref({});
let form = computed(() => props.data)
let alergia = ref(false);
let idDiagnostico = ref(null);
let diagnosticos = ref([]);
let loadingDiagnosticos = ref(false);
let debounceTimeout = null;

function getCalificacionParametro(parametro, valor) {
    const parametros = getTriajeParametros();
    const config = parametros[parametro];

    if (!config || valor == null) return {label: '', color: '', unidad: ''};

    // Solo incluye la unidad si tiene valor
    const unidad = config.unidad ? config.unidad : '';

    if (valor < config.min)
        return {label: 'Valor muy bajo', color: 'text-primary', unidad};
    if (valor > config.max)
        return {label: 'Valor muy alto', color: 'text-danger', unidad};

    if (Array.isArray(config.niveles)) {
        for (const nivel of config.niveles) {
            if (valor <= nivel.max) {
                return {label: nivel.label, color: nivel.color, unidad};
            }
        }
    }

    return {label: '', color: '', unidad};
}

const onChangeMotivoIngreso = (newValue) => {
    const seleccionado = appStore.configuracionEmergenciaMotivoIngreso.find(
        (item) => item.id === newValue
    );

    if (seleccionado) {
        medicoEspecialidadFiltrados = null;
        form.value.IdTipoGravedad = seleccionado.IdPrioridad;
        form.value.IdServicio = seleccionado.IdServicio;
        onChangeServicio(seleccionado.IdServicio);
    } else {
        form.value.IdTipoGravedad = null;
        form.value.IdServicio = null;
    }
    emit('onChangeMotivoIngreso', {
        IdTipoGravedad: form.value.IdTipoGravedad,
        IdServicio: form.value.IdServicio,
    });
};

const onChangeMedicoTopico = () => {
    emit('onChangeMedicoTopico', {
        IdMedicoTopico: form.value.IdMedicoTopico
    });
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

const validarRango = (campo, valor) => {
    const rango = rangosSignosVitales[campo]
    const numero = parseFloat(valor)
    if (valor === '' || valor === null || valor === undefined || isNaN(numero)) {
        errors[campo] = 'Este campo es requerido o debe ser un número válido'
    } else if (numero < rango.min || numero > rango.max) {
        errors[campo] = `El valor debe estar entre ${rango.min} y ${rango.max}`
    } else {
        errors[campo] = ''
    }
    if (campo === 'TriajePeso' || campo === 'TriajeTalla') {
        form.value.TriajeIMC = calcularIMC(Number(form.value.TriajePeso), Number(form.value.TriajeTalla));
    }
}

const onChangeFormaIngreso = () => {
    form.value.IdEstadoIngreso = null
    cargarDatosEstadosIngreso();
}

const cargarDatosEstadosIngreso = () => {
    estadosFiltrados.value = appStore.configuracionEmergenciaEstadosIngreso.filter(row => row.IdFormaIngreso === form.value.IdFormaIngreso);
    if(estadosFiltrados.value.length > 0) {
        form.value.IdEstadoIngreso = estadosFiltrados.value[0].id;
    }
    // form.value.IdEstadoIngreso = null
}

const onChangeFuenteFinanciamiento = () => {
    form.value.IdTipoFinanciamiento = null;
    reloadTipoFinanciamiento();
}

const reloadTipoFinanciamiento = () => {
    let fuenteFinancimiento = appStore.tablasCache.fuenteFinanciamientoCache.find(row => row.id === form.value.IdFuenteFinanciamiento);
    tiposFinancimiento.value = [];
    if (fuenteFinancimiento) {
        tiposFinancimiento.value = fuenteFinancimiento.tipoFinanciamientos;
        if(tiposFinancimiento.value.length > 0) {
            form.value.IdTipoFinanciamiento = tiposFinancimiento.value[0].id;
        }
    }
}

// Función genérica que observa un ref y lanza alerta si se activa
async function onToggle(name, event) {
    const value = event.target.checked;
    if (value) {
        if (value === true) {
            if (name == 'ALERGIA') {
                form.value.TriajeObservacion = 'TIENE REACCIÓN ALÉRGICA A MEDICAMENTOS';
            }
        }
    }
}

const rangosSignosVitales = getTriajeParametros();
serviciosFiltrados.value = appStore.configuracionServicio.filter(
    row => row.idEstado === 1 && row.IdTipoServicio === 2
);

const fetchOptionsDiagnosticos = async (query) => {
    if (!query) {
        diagnosticos.value = [];
        return;
    }
    loadingDiagnosticos.value = true;
    try {
        const response = await axios.post('/filtrar_diagnosticos', {buscar: query});
        diagnosticos.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingDiagnosticos.value = false;
    }
}

const onFilterDiagnosticos = (event) => {
    if (event.value.length > 3) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsDiagnosticos(event.value);
        }, 500);
    }
}

const agregarDiagnostico = () => {
    let diagnostico = diagnosticos.value.find(row => row.id === idDiagnostico.value);
    if (diagnostico) {
        form.value.diagnosticos.push({
            IdDiagnostico: diagnostico.id,
            Descripcion: diagnostico.label
        });
    }
    idDiagnostico.value = null;
}

const removerDiagnostico = (index) => {
    form.value.diagnosticos.splice(index, 1);
}

function calcularIMC(pesoKg, alturaM) {
    if (!pesoKg || !alturaM) return 0;
    return +(pesoKg / ((alturaM / 100) * (alturaM / 100))).toFixed(2);
}

fuentesFinancimiento.value = appStore.tablasCache.fuenteFinanciamientoCache.filter(row => (row.UtilizadoEn === 2 || row.UtilizadoEn === 3));
tipoGravedadAtenciones.value = appStore.tablasCache.tipoGravedadAtencionCache.filter(row => row.id < 5);

watch(
    () => form.value.IdServicio,
    (newVal) => {
        if (newVal) onChangeServicio(newVal);
    },
    {immediate: true}
);

watch(
    () => form.value.IdFormaIngreso,
    (newVal) => {
        if (newVal) onChangeFormaIngreso();
    },
    {immediate: true}
);

watch(
    () => form.value.IdFuenteFinanciamiento,
    (newVal) => {
        // console.log('onChangeTipoFinanciamiento');
        if (newVal) onChangeFuenteFinanciamiento();
    },
    {immediate: true}
);
</script>

<template>
    <div class="row mt-1">
        <div class="col-12">
            <BaseDivider title="DETALLE DE INGRESO" style="margin-bottom: 0px;"/>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-12">
            <BaseCombo v-model="form.IdFormaIngreso"
                       :options="appStore.configuracionEmergenciaFormasIngreso"
                       label="Forma de Ingreso"
                       :disabled="isViewMode"
                       :showClear="false"
                       placeholder="Seleccione una Forma de Ingreso"/>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-12">
            <BaseCombo v-model="form.IdEstadoIngreso"
                       :options="estadosFiltrados"
                       label="Estado de Ingreso"
                       :disabled="isViewMode"
                       :showClear="false"
                       placeholder="Seleccione un Estado de Ingreso"/>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-12">
            <BaseCombo v-model="form.IdFuenteFinanciamiento"
                       :options="fuentesFinancimiento"
                       label="F.Financiamiento"
                       :disabled="isViewMode"
                       :showClear="false"
                       placeholder="Seleccione una Fuente de Financiamiento"/>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-12">
            <BaseCombo v-model="form.IdTipoFinanciamiento"
                       :options="tiposFinancimiento"
                       :disabled="isViewMode"
                       :showClear="false"
                       label="Producto/Plan"/>
        </div>
    </div>
    <div class="row mt-1">
        <div class="col-12">
            <BaseDivider title="PRIORIDAD / DESTINO" style="margin-bottom: 0px;"/>
        </div>
        <div class="col-4">
            <BaseCombo v-model="form.IdMotivoIngreso"
                       :options="appStore.configuracionEmergenciaMotivoIngreso"
                       label="Motivo de Ingreso"
                       :filter="true"
                       :disabled="isViewMode"
                       placeholder="Seleccione un Motivo de Ingreso"
                       @update:modelValue="onChangeMotivoIngreso"/>
        </div>
        <div class="col-4">
            <BaseCombo v-model="form.IdTipoGravedad"
                       :options="tipoGravedadAtenciones"
                       label="Prioridad"
                       :filter="true"
                       :disabled="isViewMode || !form.IdMotivoIngreso"
                       placeholder="Seleccione una Prioridad"/>
        </div>
        <div class="col-4">
            <BaseCombo v-model="form.IdServicio"
                       :options="serviciosFiltrados"
                       label="Enviado a Tópico"
                       :filter="true"
                       :disabled="isViewMode || !form.IdMotivoIngreso"
                       placeholder="Seleccione un Servicio"/>
        </div>
        <div class="col-4" v-if="$can(`${moduloActual}.medico`)">
            <BaseCombo v-model="form.IdMedicoTopico"
                       :options="medicoEspecialidadFiltrados"
                       label="Médico / Especialidad"
                       option-value="id"
                       :filter="true"
                       :disabled="isViewMode"
                       placeholder="Seleccione un Servicio"
                       @update:modelValue="onChangeMedicoTopico"/>
        </div>
    </div>
    <div class="row mt-1">
<!--        <div class="col-6">-->
<!--            <div class="row">-->
                <div class="col-12">
                    <BaseDivider title="SIGNOS VITALES" style="margin-bottom: 0px;"/>
                </div>
                <div class="col-2">
                    <BaseInput v-model="form.TriajePresionSis"
                               :label="'P.A SIS'"
                               step="0.1"
                               :labelBottom="form.TriajePresionSis !== null && form.TriajePresionSis !== '' ? getCalificacionParametro('TriajePresionSis', form.TriajePresionSis).label : ''"
                               :labelBottomClass="form.TriajePresionSis !== null && form.TriajePresionSis !== '' ? getCalificacionParametro('TriajePresionSis', form.TriajePresionSis).color : ''"
                               :disabled="isViewMode"
                               :error="errors.TriajePresionSis"
                               type="number"
                               :min="rangosSignosVitales.TriajePresionSis.min"
                               :max="rangosSignosVitales.TriajePresionSis.max"
                               @input="validarRango('TriajePresionSis', $event.target.value)"/>
                </div>
                <div class="col-2">
                    <BaseInput v-model="form.TriajePresionDia"
                               :label="'P.A DIA'"
                               step="0.1"
                               :labelBottom="form.TriajePresionDia !== null && form.TriajePresionDia !== '' ? getCalificacionParametro('TriajePresionDia', form.TriajePresionDia).label : ''"
                               :labelBottomClass="form.TriajePresionDia !== null && form.TriajePresionDia !== '' ? getCalificacionParametro('TriajePresionDia', form.TriajePresionDia).color : ''"
                               :disabled="isViewMode"
                               :error="errors.TriajePresionDia"
                               required
                               type="number"
                               :min="rangosSignosVitales.TriajePresionDia.min"
                               :max="rangosSignosVitales.TriajePresionDia.max"
                               @input="validarRango('TriajePresionDia', $event.target.value)"/>
                </div>
                <div class="col-2">
                    <BaseInput v-model="form.TriajeFrecCardiaca"
                               label="FRE.CARDIACA"
                               step="0.1"
                               :labelBottom="form.TriajeFrecCardiaca !== null && form.TriajeFrecCardiaca !== '' ? getCalificacionParametro('TriajeFrecCardiaca', form.TriajeFrecCardiaca).label : ''"
                               :labelBottomClass="form.TriajeFrecCardiaca !== null && form.TriajeFrecCardiaca !== '' ? getCalificacionParametro('TriajeFrecCardiaca', form.TriajeFrecCardiaca).color : ''"
                               :disabled="isViewMode" :error="errors.TriajeFrecCardiaca"
                               required
                               type="number"
                               :min="rangosSignosVitales.TriajeFrecCardiaca.min"
                               :max="rangosSignosVitales.TriajeFrecCardiaca.max"
                               @input="validarRango('TriajeFrecCardiaca', $event.target.value)"/>
                </div>
                <div class="col-2">
                    <BaseInput v-model="form.TriajeFrecRespiratoria" label="FRE.RESPIRATORIA"
                               step="0.1"
                               :labelBottom="form.TriajeFrecRespiratoria !== null && form.TriajeFrecRespiratoria !== '' ? getCalificacionParametro('TriajeFrecRespiratoria', form.TriajeFrecRespiratoria).label : ''"
                               :labelBottomClass="form.TriajeFrecRespiratoria !== null && form.TriajeFrecRespiratoria !== '' ? getCalificacionParametro('TriajeFrecRespiratoria', form.TriajeFrecRespiratoria).color : ''"
                               :disabled="isViewMode" :error="errors.TriajeFrecRespiratoria" required type="number"
                               :min="rangosSignosVitales.TriajeFrecRespiratoria.min"
                               :max="rangosSignosVitales.TriajeFrecRespiratoria.max"
                               @input="validarRango('TriajeFrecRespiratoria', $event.target.value)"/>
                </div>
                <div class="col-2">
                    <BaseInput v-model="form.TriajeSaturacionOxigeno" label="SATURACIÓN" step="0.1"
                               :labelBottom="form.TriajeSaturacionOxigeno !== null && form.TriajeSaturacionOxigeno !== '' ? getCalificacionParametro('TriajeSaturacionOxigeno', form.TriajeSaturacionOxigeno).label : ''"
                               :labelBottomClass="form.TriajeSaturacionOxigeno !== null && form.TriajeSaturacionOxigeno !== '' ? getCalificacionParametro('TriajeSaturacionOxigeno', form.TriajeSaturacionOxigeno).color : ''"
                               :disabled="isViewMode" :error="errors.TriajeSaturacionOxigeno" required type="number"
                               :min="rangosSignosVitales.TriajeSaturacionOxigeno.min"
                               :max="rangosSignosVitales.TriajeSaturacionOxigeno.max"
                               @input="validarRango('TriajeSaturacionOxigeno', $event.target.value)"/>
                </div>
                <div class="col-2">
                    <BaseInput v-model="form.TriajeTemperatura"
                               label="TEMPERATURA"
                               step="0.1"
                               :labelBottom="form.TriajeTemperatura !== null && form.TriajeTemperatura !== '' ? getCalificacionParametro('TriajeTemperatura', form.TriajeTemperatura).label : ''"
                               :labelBottomClass="form.TriajeTemperatura !== null && form.TriajeTemperatura !== '' ? getCalificacionParametro('TriajeTemperatura', form.TriajeTemperatura).color : ''"
                               :disabled="isViewMode"
                               :error="errors.TriajeTemperatura"
                               required type="number"
                               :min="rangosSignosVitales.TriajeTemperatura.min"
                               :max="rangosSignosVitales.TriajeTemperatura.max"
                               @input="validarRango('TriajeTemperatura', $event.target.value)"/>
                </div>
                <div class="col-2">
                    <BaseInput v-model="form.TriajePeso"
                               :label="`PESO (${getCalificacionParametro('TriajePeso', form.TriajePeso).unidad})`"
                               step="0.1"
                               :disabled="isViewMode"
                               :error="errors.TriajePeso"
                               required
                               type="number"
                               :min="rangosSignosVitales.TriajePeso.min"
                               :max="rangosSignosVitales.TriajePeso.max"
                               @input="validarRango('TriajePeso', $event.target.value)"/>
                </div>
                <div class="col-2">
                    <BaseInput v-model="form.TriajeTalla"
                               :label="`TALLA (${getCalificacionParametro('TriajeTalla', form.TriajeTalla).unidad})`"
                               step="0.1"
                               :disabled="isViewMode"
                               :error="errors.TriajeTalla"
                               required
                               type="number"
                               :min="rangosSignosVitales.TriajeTalla.min"
                               :max="rangosSignosVitales.TriajeTalla.max"
                               @input="validarRango('TriajeTalla', $event.target.value)"/>
                </div>
                <div class="col-2">
                    <BaseInput v-model="form.TriajeIMC"
                               label="IMC"
                               step="0.1"
                               :labelBottom="form.TriajeIMC !== null && form.TriajeIMC !== '' ? getCalificacionParametro('TriajeIMC', form.TriajeIMC).label : ''"
                               :labelBottomClass="form.TriajeIMC !== null && form.TriajeIMC !== '' ? getCalificacionParametro('TriajeIMC', form.TriajeIMC).color : ''"
                               disabled
                               :error="errors.TriajeIMC"
                               :min="rangosSignosVitales.TriajeIMC.min"
                               :max="rangosSignosVitales.TriajeIMC.max"
                               @input="validarRango('TriajeIMC', $event.target.value)"/>
                </div>
<!--            </div>-->
<!--        </div>-->
<!--        <div class="col-6">-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--                    <BaseDivider title="PRIORIDAD"/>-->
<!--                </div>-->
<!--                <div class="col-12">-->
<!--                    <BaseCombo v-model="form.IdMotivoIngreso"-->
<!--                               :options="appStore.configuracionEmergenciaMotivoIngreso"-->
<!--                               label="Motivo de Ingreso"-->
<!--                               :filter="true"-->
<!--                               :disabled="isViewMode"-->
<!--                               placeholder="Seleccione un Motivo de Ingreso"-->
<!--                               @update:modelValue="onChangeMotivoIngreso"/>-->
<!--                </div>-->
<!--                <div class="col-12">-->
<!--                    <BaseCombo v-model="form.IdTipoGravedad"-->
<!--                               :options="tipoGravedadAtenciones"-->
<!--                               label="Prioridad"-->
<!--                               :filter="true"-->
<!--                               :disabled="isViewMode || !form.IdMotivoIngreso"-->
<!--                               placeholder="Seleccione una Prioridad"/>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--                    <BaseDivider title="DESTINO"/>-->
<!--                </div>-->
<!--                <div class="col-12">-->
<!--                    <BaseCombo v-model="form.IdServicio"-->
<!--                               :options="serviciosFiltrados"-->
<!--                               label="Enviado a Tópico"-->
<!--                               :filter="true"-->
<!--                               :disabled="isViewMode || !form.IdMotivoIngreso"-->
<!--                               placeholder="Seleccione un Servicio"/>-->
<!--                </div>-->
<!--                <div class="col-12" v-if="$can(`${moduloActual}.medico`)">-->
<!--                    <BaseCombo v-model="form.IdMedicoTopico"-->
<!--                               :options="medicoEspecialidadFiltrados"-->
<!--                               label="Médico / Especialidad"-->
<!--                               option-value="id"-->
<!--                               :filter="true"-->
<!--                               :disabled="isViewMode"-->
<!--                               placeholder="Seleccione un Servicio"-->
<!--                               @update:modelValue="onChangeMedicoTopico"/>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>
    <div class="row mt-1" v-if="$can(`${moduloActual}.diagnosticos`)">
        <div class="col-12">
            <BaseDivider title="DIAGNÓSTICOS" style="margin-bottom: 0px;"/>
        </div>
        <div class="col-12" style="margin-bottom: 16px" v-if="!isViewMode">
            <label class="form-label">Agregar diagnóstico</label>
            <div class="d-flex">
                <Select v-model="idDiagnostico"
                        :options="diagnosticos"
                        option-label="label"
                        option-value="value"
                        filter
                        filterPlaceholder="Buscar..."
                        @filter="onFilterDiagnosticos"
                        :loading="loadingDiagnosticos"
                        placeholder="Seleccione una opción"
                        :showClear="true"
                        class="w-full"
                        size="small"
                        style="width: 100%;"
                        :disabled="isViewMode"
                        :autoFilterFocus="true"></Select>
                <w-button icon="plus" size="small" @click="agregarDiagnostico"
                          v-if="!isViewMode"></w-button>
            </div>
        </div>
        <div class="col-12"
             v-if="form && form.diagnosticos && form.diagnosticos.length > 0">
            <table class="table table-sm table-hover">
                <tbody>
                <tr v-for="(c, index) in form.diagnosticos">
                    <td>
                        {{ c.Descripcion }}
                    </td>
                    <td style="text-align: right">
                        <button @click.prevent="removerDiagnostico(index)"
                                class="btn btn-danger btn-sm"
                                v-if="!isViewMode">
                            <i class="ti ti-trash"></i></button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <BaseDivider title="OBSERVACIÓN" style="margin-bottom: 0px;"/>
        </div>
        <div class="col-12">
            <div class="d-flex justify-content-start align-items-center mb-2">
                <label class="me-2">REACCIÓN ALÉRGICA?</label>
                <ToggleSwitch v-model="alergia" @change="onToggle('ALERGIA', $event)" :disabled="isViewMode"/>
            </div>
            <Textarea v-model="form.TriajeObservacion" autoResize rows="5" cols="30" :disabled="isViewMode"/>
        </div>
    </div>
</template>
