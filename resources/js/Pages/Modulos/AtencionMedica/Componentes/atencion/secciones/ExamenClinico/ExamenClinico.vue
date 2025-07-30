<script setup>
import { ref, computed } from 'vue';
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";

const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

const isViewMode = computed(() => props.viewRecord);

let respuesta = [
    { label: 'SI', value: 1 },
    { label: 'NO', value: 2 },
];
</script>

<template>
    <div class="row g-3 align-items-end">

        <div class="col-md-3">
            <BaseInput value="PIEL Y TEJIDO CELULAR SUBCUTÁNEO" :disabled="isViewMode" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" />
        </div>
        <div class="col-md-7">
            <BaseInput value="DETALLE DE ALTERACIONES" :disabled="isViewMode" />
        </div>


        <div class="col-md-3">
            <BaseInput value="CABEZA, CUELLO Y CAVIDAD ORAL/ GANGLIOS LINFÁTICOS" :disabled="isViewMode" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" />
        </div>
        <div class="col-md-7">
            <BaseInput value="DETALLE DE ALTERACIONES" :disabled="isViewMode" />
        </div>


        <div class="col-md-3">
            <BaseInput value="TÓRAX Y PULMONES" :disabled="isViewMode" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" />
        </div>
        <div class="col-md-7">
            <BaseInput value="DETALLE DE ALTERACIONES" :disabled="isViewMode" />
        </div>


        <div class="col-md-3">
            <BaseInput value="CARDIOVASCULAR" :disabled="isViewMode" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" />
        </div>
        <div class="col-md-7">
            <BaseInput value="DETALLE DE ALTERACIONES" :disabled="isViewMode" />
        </div>


        <div class="col-md-3">
            <BaseInput value="ABDOMEN" :disabled="isViewMode" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" />
        </div>
        <div class="col-md-7">
            <BaseInput value="DETALLE DE ALTERACIONES" :disabled="isViewMode" />
        </div>


        <div class="col-md-3">
            <BaseInput value="URINARIO" :disabled="isViewMode" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" />
        </div>
        <div class="col-md-7">
            <BaseInput value="DETALLE DE ALTERACIONES" :disabled="isViewMode" />
        </div>


        <div class="col-md-3">
            <BaseInput value="GENIAL, -MASCULINO (   ), -FEMENINO (   )" :disabled="isViewMode" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" />
        </div>
        <div class="col-md-7">
            <BaseInput value="DETALLE DE ALTERACIONES" :disabled="isViewMode" />
        </div>


        <div class="col-md-3">
            <BaseInput value="MÚSCULO ESQUELÉTICO" :disabled="isViewMode" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" />
        </div>
        <div class="col-md-7">
            <BaseInput value="DETALLE DE ALTERACIONES" :disabled="isViewMode" />
        </div>


        <div class="col-md-3">
            <BaseInput value="SISTEMA NERVIOSO" :disabled="isViewMode" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" />
        </div>
        <div class="col-md-7">
            <BaseInput value="DETALLE DE ALTERACIONES" :disabled="isViewMode" />
        </div>
    </div>
</template>
