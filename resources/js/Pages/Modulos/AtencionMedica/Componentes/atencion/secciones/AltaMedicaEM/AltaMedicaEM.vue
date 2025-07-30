<script setup>
import { ref, computed } from 'vue';
import Textarea from 'primevue/textarea';
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

let formaEgreso = [
    { label: 'ALTA', value: 1 },
    { label: 'RETIRO CON FIRMA', value: 2 },
    { label: 'RETIRO SIN FIRMA', value: 3 },
    { label: 'FUGA', value: 4 }, 
];
let egreso = [
    { label: 'ALIVIADO', value: 1 }, 
    { label: 'FALLECIDO', value: 2 }, 
];
let tranferido = [
    { label: 'AREA 1', value: 1 }, 
    { label: 'AREA 2', value: 2 }, 
];
</script>

<template>
    <div class="row g-3 align-items-end">

        <div class="col-md-4">
            <BaseCombo :options="formaEgreso" optionLabel="label" optionValue="value" label="FORMA DE EGRESO"/>
        </div>
        <div class="col-md-4">
            <BaseCombo :options="egreso" optionLabel="label" optionValue="value" label="CONDICIÓN DE EGRESO"/>
        </div>
        <div class="col-md-4">
            <BaseCombo :options="tranferido" optionLabel="label" optionValue="value" label="TRANSFERIDO A"/>
        </div>
        <!-- resultados de laboratorio -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <label class="form-label">INDICACIONES MÉDICAS</label>
            <Textarea rows="4" autoResize class="w-100" />
        </div> 

    </div>
</template>
