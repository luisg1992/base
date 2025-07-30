<script setup>

import Textarea from "primevue/textarea";
import InputGroup from "primevue/inputgroup";
import ToggleSwitch from "primevue/toggleswitch";
import BaseDivider from '@/components/BaseDivider.vue'
import BaseCombo from "@/components/WSelect/WSelect.vue";
import BaseInput from "@/components/WInput/WInput.vue";
import { useAppStore } from '@/stores/useAppStore'
import { computed, ref, watch } from "vue";

const props = defineProps(['data', 'isViewMode']);
const emit = defineEmits(['onChangeMotivoIngreso', 'onChangeMedicoTopico']);

let appStore = useAppStore();

let medicoEspecialidadFiltrados = ref([]);
let serviciosFiltrados = ref([]);
let estadosFiltrados = ref([]);
let fuentesFinancimiento = ref([]);
let tipoGravedadAtenciones = ref([]);
let errors = ref({});
let form = computed(() => props.data)
let alergia = ref(false);



function getCalificacionParametro(parametro, valor) {
    const parametros = getTriajeParametros();
    const config = parametros[parametro];

    if (!config || valor == null) return { label: '', color: '' };
    if (valor < config.min) return { label: 'Valor muy bajo', color: 'text-primary' };
    if (valor > config.max) return { label: 'Valor muy alto', color: 'text-danger' };

    for (const nivel of config.niveles) {
        if (valor <= nivel.max) {
            return { label: nivel.label, color: nivel.color };
        }
    }
    return { label: '', color: '' };
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
}

const onChangeFormaIngreso = () => {
    form.value.IdEstadoIngreso = null
    cargarDatosEstadosIngreso();
}

const cargarDatosEstadosIngreso = () => {
    estadosFiltrados.value = appStore.configuracionEmergenciaEstadosIngreso.filter(row => row.IdFormaIngreso === form.value.IdFormaIngreso);
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

fuentesFinancimiento.value = appStore.tablasCache.fuenteFinanciamientoCache.filter(row => (row.UtilizadoEn === 2 || row.UtilizadoEn === 3));
tipoGravedadAtenciones.value = appStore.tablasCache.tipoGravedadAtencionCache.filter(row => row.id < 5);
// onChangeServicio();

watch(
    () => form.value.IdServicio,
    (newVal) => {
        if (newVal) onChangeServicio(newVal);
    },
    { immediate: true }
);

watch(
    () => form.value.IdFormaIngreso,
    (newVal) => {
        if (newVal) cargarDatosEstadosIngreso();
    },
    { immediate: true }
);
</script>

<template>
    <div class="row mt-2">
        <BaseDivider title="I. SIGNOS VITALES" />
        <div class="col-xl-3 col-lg-6 col-md-12">
            <BaseInput v-model="form.TriajePulso" :label="'Pulso'" step="0.1"
                :labelBottom="form.TriajePulso !== null && form.TriajePulso !== '' ? getCalificacionParametro('TriajePulso', form.TriajePulso).label : ''"
                :labelBottomClass="form.TriajePulso !== null && form.TriajePulso !== '' ? getCalificacionParametro('TriajePulso', form.TriajePulso).color : ''"
                :disabled="isViewMode" :error="errors.TriajePulso" type="number"
                @input="validarRango('TriajePulso', $event.target.value)" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-12">
            <BaseInput v-model="form.TriajeTemperatura" :label="'Temperatura'" step="0.1"
                :labelBottom="form.TriajeTemperatura !== null && form.TriajeTemperatura !== '' ? getCalificacionParametro('TriajeTemperatura', form.TriajeTemperatura).label : ''"
                :labelBottomClass="form.TriajeTemperatura !== null && form.TriajeTemperatura !== '' ? getCalificacionParametro('TriajeTemperatura', form.TriajeTemperatura).color : ''"
                :disabled="isViewMode" :error="errors.TriajeTemperatura" required type="number"
                :min="rangosSignosVitales.TriajeTemperatura.min" :max="rangosSignosVitales.TriajeTemperatura.max"
                @input="validarRango('TriajeTemperatura', $event.target.value)" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-12">
            <BaseInput v-model="form.TriajePresion" :label="'Presión Arterial'" step="0.1"
                :labelBottom="form.TriajePresion !== null && form.TriajePresion !== '' ? getCalificacionParametro('TriajePresion', form.TriajePresion).label : ''"
                :labelBottomClass="form.TriajePresion !== null && form.TriajePresion !== '' ? getCalificacionParametro('TriajePresion', form.TriajePresion).color : ''"
                :disabled="isViewMode" :error="errors.TriajePresion" required type="text"
                @input="validarRango('TriajePresion', $event.target.value)" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-12">
            <BaseInput v-model="form.TriajeFrecCardiaca" label="FRE.CARDIACA" step="0.1"
                :labelBottom="form.TriajeFrecCardiaca !== null && form.TriajeFrecCardiaca !== '' ? getCalificacionParametro('TriajeFrecCardiaca', form.TriajeFrecCardiaca).label : ''"
                :labelBottomClass="form.TriajeFrecCardiaca !== null && form.TriajeFrecCardiaca !== '' ? getCalificacionParametro('TriajeFrecCardiaca', form.TriajeFrecCardiaca).color : ''"
                :disabled="isViewMode" :error="errors.TriajeFrecCardiaca" required type="number"
                :min="rangosSignosVitales.TriajeFrecCardiaca.min" :max="rangosSignosVitales.TriajeFrecCardiaca.max"
                @input="validarRango('TriajeFrecCardiaca', $event.target.value)" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-12">
            <BaseInput v-model="form.TriajeSaturacion" label="SAT O2" step="0.1"
                :labelBottom="form.TriajeSaturacion !== null && form.TriajeSaturacion !== '' ? getCalificacionParametro('TriajeSaturacion', form.TriajeSaturacion).label : ''"
                :labelBottomClass="form.TriajeSaturacion !== null && form.TriajeSaturacion !== '' ? getCalificacionParametro('TriajeSaturacion', form.TriajeSaturacion).color : ''"
                :disabled="isViewMode" :error="errors.TriajeSaturacion" required type="number"
                @input="validarRango('TriajeSaturacion', $event.target.value)" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-12">
            <BaseInput v-model="form.TriajeFrecRespiratoria" label="FRE.RESPIRATORIA" step="0.1"
                :labelBottom="form.TriajeFrecRespiratoria !== null && form.TriajeFrecRespiratoria !== '' ? getCalificacionParametro('TriajeFrecRespiratoria', form.TriajeFrecRespiratoria).label : ''"
                :labelBottomClass="form.TriajeFrecRespiratoria !== null && form.TriajeFrecRespiratoria !== '' ? getCalificacionParametro('TriajeFrecRespiratoria', form.TriajeFrecRespiratoria).color : ''"
                :disabled="isViewMode" :error="errors.TriajeFrecRespiratoria" required type="number"
                :min="rangosSignosVitales.TriajeFrecRespiratoria.min"
                :max="rangosSignosVitales.TriajeFrecRespiratoria.max"
                @input="validarRango('TriajeFrecRespiratoria', $event.target.value)" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-12">
            <BaseInput v-model="form.TriajePeso" label="PESO" step="0.1" :disabled="isViewMode"
                :error="errors.TriajePeso" required type="number" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-12">
            <BaseInput v-model="form.TriajeTalla" label="TALLA" step="0.1" :disabled="isViewMode"
                :error="errors.TriajeTalla" required type="number" />
        </div>
    </div>
    <!-- <div class="row mt-2">
        <BaseDivider title="III. PRIORIDAD"/>
        <div class="col-xl-6 col-lg-6 col-md-12">
            <BaseCombo v-model="form.IdMotivoIngreso"
                       :options="appStore.configuracionEmergenciaMotivoIngreso"
                       label="Motivo de Ingreso"
                       :filter="true"
                       :disabled="isViewMode"
                       placeholder="Seleccione un Motivo de Ingreso"
                       @update:modelValue="onChangeMotivoIngreso"/>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12">
            <BaseCombo v-model="form.IdTipoGravedad"
                       :options="tipoGravedadAtenciones"
                       label="Prioridad"
                       :filter="true"
                       :disabled="isViewMode || !form.IdMotivoIngreso"
                       placeholder="Seleccione una Prioridad"/>
        </div>
    </div>
    <div class="row mt-2">
        <BaseDivider title="IV. DESTINO"/>
        <div class="col-xl-6 col-lg-6 col-md-12">
            <BaseCombo v-model="form.IdServicio"
                       :options="serviciosFiltrados"
                       label="Enviado a Tópico"
                       :filter="true"
                       :disabled="isViewMode || !form.IdMotivoIngreso"
                       placeholder="Seleccione un Servicio"/>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12">
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
    <div class="row mt-2">
        <BaseDivider title="V. OBSERVACIÓN"/>
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="d-flex justify-content-end align-items-center">
                <label class="mb-0 me-2">REACCIÓN ALÉRGICA?</label>
                <ToggleSwitch v-model="alergia" @change="onToggle('ALERGIA', $event)" :disabled="isViewMode"/>
                <br>
            </div>
            <Textarea v-model="form.TriajeObservacion" autoResize rows="5" cols="30" :disabled="isViewMode"/>
        </div>
    </div> -->
</template>
