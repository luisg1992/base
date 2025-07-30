<script setup>
import { ref, computed } from 'vue';
import Textarea from 'primevue/textarea';

const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

const isViewMode = computed(() => props.viewRecord);
const planTrabajo = ref('');

</script>

<template>
    <div class="row g-3 align-items-end">

        <!-- resultados de laboratorio -->
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <label class="form-label">Comentario Médico de los resultados de laboratorio</label>
            <Textarea rows="4" autoResize class="w-100" />
        </div>

        <!-- resultados de laboratorio -->
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <label class="form-label">Comentario Médico y/o reporte de los procedimientos</label>
            <Textarea rows="4" autoResize class="w-100" />
        </div>

        <!-- resultados de laboratorio -->
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <label class="form-label">Comentario Médico de los resultados de rediodiagnósticos</label>
            <Textarea rows="4" autoResize class="w-100" />
        </div>

        <!-- resultados de laboratorio -->
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <label class="form-label">Comentario Médico de los resultados de interconsultas</label>
            <Textarea rows="4" autoResize class="w-100" />
        </div>

    </div>
</template>
