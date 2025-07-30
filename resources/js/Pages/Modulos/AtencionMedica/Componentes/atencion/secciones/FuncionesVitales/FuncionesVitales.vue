<script setup>
import { ref, computed, watch  } from 'vue';
import BaseInput from "@/components/WInput/WInput.vue";
import Textarea from 'primevue/textarea';

const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

let errors = ref({});
let rangosSignosVitales = getTriajeParametros();
let isViewMode = computed(() => props.viewRecord);
let form = ref({
    TriajePresionSis: null,
    TriajePresionDia: null,
    TriajeFrecCardiaca: null,
    TriajeFrecRespiratoria: null,
    TriajeSaturacionOxigeno: null,
    TriajeTemperatura: null,
    Peso: null,
    Talla: null,
    IMC: null,


    hidratacion: null,
    nutricion: null,
    conciencia: null,
});

let validarRango = (campo, valor) => {
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

function calcularIMC(pesoKg, tallaCm) {
    if (!pesoKg || !tallaCm || tallaCm <= 0) return null;
    const tallaM = tallaCm / 100;
    return (pesoKg / (tallaM * tallaM)).toFixed(2);
}

// Calcula IMC automáticamente cuando cambia peso o talla
watch([() => form.value.Peso, () => form.value.Talla], ([peso, talla]) => {
    form.value.IMC = calcularIMC(peso, talla)
})
</script>

<template>
    <div class="row g-3 align-items-end">

        <!-- TriajeFrecRespiratoria -->
        <div class="col-xl-2 col-lg-3 col-md-12">
            <BaseInput v-model="form.TriajeFrecRespiratoria" label="FRE.RESPIRATORIA" step="0.1"
                :labelBottom="form.TriajeFrecRespiratoria !== null && form.TriajeFrecRespiratoria !== '' ? getCalificacionParametro('TriajeFrecRespiratoria', form.TriajeFrecRespiratoria).label : ''"
                :labelBottomClass="form.TriajeFrecRespiratoria !== null && form.TriajeFrecRespiratoria !== '' ? getCalificacionParametro('TriajeFrecRespiratoria', form.TriajeFrecRespiratoria).color : ''"
                :disabled="isViewMode" :error="errors.TriajeFrecRespiratoria" required type="number"
                :min="rangosSignosVitales.TriajeFrecRespiratoria.min"
                :max="rangosSignosVitales.TriajeFrecRespiratoria.max"
                @input="validarRango('TriajeFrecRespiratoria', $event.target.value)" />
        </div>

        <!-- TriajeTemperatura -->
        <div class="col-xl-2 col-lg-3 col-md-12">
            <BaseInput v-model="form.TriajeTemperatura" label="TEMPERATURA" step="0.1"
                :labelBottom="form.TriajeTemperatura !== null && form.TriajeTemperatura !== '' ? getCalificacionParametro('TriajeTemperatura', form.TriajeTemperatura).label : ''"
                :labelBottomClass="form.TriajeTemperatura !== null && form.TriajeTemperatura !== '' ? getCalificacionParametro('TriajeTemperatura', form.TriajeTemperatura).color : ''"
                :disabled="isViewMode" :error="errors.TriajeTemperatura" required type="number"
                :min="rangosSignosVitales.TriajeTemperatura.min" :max="rangosSignosVitales.TriajeTemperatura.max"
                @input="validarRango('TriajeTemperatura', $event.target.value)" />
        </div>

        <!-- TriajeFrecCardiaca -->
        <div class="col-xl-2 col-lg-3 col-md-12">
            <BaseInput v-model="form.TriajeFrecCardiaca" label="FRE.CARDIACA" step="0.1"
                :labelBottom="form.TriajeFrecCardiaca !== null && form.TriajeFrecCardiaca !== '' ? getCalificacionParametro('TriajeFrecCardiaca', form.TriajeFrecCardiaca).label : ''"
                :labelBottomClass="form.TriajeFrecCardiaca !== null && form.TriajeFrecCardiaca !== '' ? getCalificacionParametro('TriajeFrecCardiaca', form.TriajeFrecCardiaca).color : ''"
                :disabled="isViewMode" :error="errors.TriajeFrecCardiaca" required type="number"
                :min="rangosSignosVitales.TriajeFrecCardiaca.min" :max="rangosSignosVitales.TriajeFrecCardiaca.max"
                @input="validarRango('TriajeFrecCardiaca', $event.target.value)" />
        </div>

        <!-- TriajePresionSis -->
        <div class="col-xl-2 col-lg-3 col-md-12">
            <BaseInput v-model="form.TriajePresionSis" :label="'P.A SIS'" step="0.1"
                :labelBottom="form.TriajePresionSis !== null && form.TriajePresionSis !== '' ? getCalificacionParametro('TriajePresionSis', form.TriajePresionSis).label : ''"
                :labelBottomClass="form.TriajePresionSis !== null && form.TriajePresionSis !== '' ? getCalificacionParametro('TriajePresionSis', form.TriajePresionSis).color : ''"
                :disabled="isViewMode" :error="errors.TriajePresionSis" type="number"
                :min="rangosSignosVitales.TriajePresionSis.min" :max="rangosSignosVitales.TriajePresionSis.max"
                @input="validarRango('TriajePresionSis', $event.target.value)" />
        </div>

        <!-- TriajePresionDia -->
        <div class="col-xl-2 col-lg-3 col-md-12">
            <BaseInput v-model="form.TriajePresionDia" :label="'P.A DIA'" step="0.1"
                :labelBottom="form.TriajePresionDia !== null && form.TriajePresionDia !== '' ? getCalificacionParametro('TriajePresionDia', form.TriajePresionDia).label : ''"
                :labelBottomClass="form.TriajePresionDia !== null && form.TriajePresionDia !== '' ? getCalificacionParametro('TriajePresionDia', form.TriajePresionDia).color : ''"
                :disabled="isViewMode" :error="errors.TriajePresionDia" required type="number"
                :min="rangosSignosVitales.TriajePresionDia.min" :max="rangosSignosVitales.TriajePresionDia.max"
                @input="validarRango('TriajePresionDia', $event.target.value)" />
        </div>

        <!-- TriajeSaturacionOxigeno -->
        <div class="col-xl-2 col-lg-3 col-md-12">
            <BaseInput v-model="form.TriajeSaturacionOxigeno" label="SATURACIÓN" step="0.1"
                :labelBottom="form.TriajeSaturacionOxigeno !== null && form.TriajeSaturacionOxigeno !== '' ? getCalificacionParametro('TriajeSaturacionOxigeno', form.TriajeSaturacionOxigeno).label : ''"
                :labelBottomClass="form.TriajeSaturacionOxigeno !== null && form.TriajeSaturacionOxigeno !== '' ? getCalificacionParametro('TriajeSaturacionOxigeno', form.TriajeSaturacionOxigeno).color : ''"
                :disabled="isViewMode" :error="errors.TriajeSaturacionOxigeno" required type="number"
                :min="rangosSignosVitales.TriajeSaturacionOxigeno.min"
                :max="rangosSignosVitales.TriajeSaturacionOxigeno.max"
                @input="validarRango('TriajeSaturacionOxigeno', $event.target.value)" />
        </div>

        <!-- Peso -->
        <div class="col-xl-3 col-lg-3 col-md-12">
            <BaseInput v-model="form.Peso" label="PESO (kg)" step="0.1" :disabled="isViewMode" type="number"
                :min="rangosSignosVitales.TriajePeso.min" :max="rangosSignosVitales.TriajePeso.max" />
        </div>

        <!-- Talla -->
        <div class="col-xl-3 col-lg-3 col-md-12">
            <BaseInput v-model="form.Talla" label="TALLA (cm)" step="0.1" :disabled="isViewMode" type="number"
                :min="rangosSignosVitales.TriajeTalla.min" :max="rangosSignosVitales.TriajeTalla.max" />
        </div>

        <!-- IMC -->
        <div class="col-xl-3 col-lg-3 col-md-12">
            <BaseInput v-model="form.IMC" label="IMC" step="0.1" :disabled="true"
                :labelBottom="form.IMC ? getCalificacionParametro('TriajeIMC', form.IMC).label : ''"
                :labelBottomClass="form.IMC ? getCalificacionParametro('TriajeIMC', form.IMC).color : ''" />
        </div>


        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Hidratación</label>
            <Textarea v-model="form.hidratacion" rows="2" autoResize class="w-100" :disabled="isViewMode" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Nutrición</label>
            <Textarea v-model="form.nutricion" rows="2" autoResize class="w-100" :disabled="isViewMode" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Conciencia</label>
            <Textarea v-model="form.conciencia" rows="2" autoResize class="w-100" :disabled="isViewMode" />
        </div>

    </div>
</template>
