<script setup>
import {  computed } from 'vue'
import Tag from 'primevue/tag';

const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

let isViewMode = computed(() => props.viewRecord)
const camposTriaje = computed(() => {
    return [
        { label: 'Presión', value: props.cabecera.TriajePresion ?? 10, icon: 'pi pi-heart' },
        { label: 'Temperatura', value: props.cabecera.TriajeTemperatura ?? 10, icon: 'pi pi-sun' },
        { label: 'Peso', value: props.cabecera.TriajePeso ?? 10, icon: 'pi pi-weight' },
        { label: 'Talla', value: props.cabecera.TriajeTalla ?? 10, icon: 'pi pi-arrows-v' },
        { label: 'Pulso', value: props.cabecera.TriajePulso ?? 10, icon: 'pi pi-stopwatch' },
        { label: 'Frec. Resp.', value: props.cabecera.TriajeFrecRespiratoria ?? 10, icon: 'pi pi-wind' },
        { label: 'Frec. Cardiaca', value: props.cabecera.TriajeFrecCardiaca ?? 10, icon: 'pi pi-heart-fill' },
    ];
});
</script>

<template> 
        <div class="d-flex flex-wrap justify-content-center">
            <div v-for="(item, index) in camposTriaje" :key="index">
                <Tag :value="(item.label+': '+item.value)" class="text-sm px-3 py-2" severity="info" value="Info"/>
            </div>
        </div> 
</template>
