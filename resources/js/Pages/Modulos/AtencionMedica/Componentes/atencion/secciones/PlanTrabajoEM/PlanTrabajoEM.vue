<script setup>
import { ref, computed } from 'vue';
import axios from 'axios'
import Select from "primevue/select"; 
import { useAppStore } from '@/stores/useAppStore'
import BaseInput from "@/components/WInput/WInput.vue";


let debounceTimeout = null;
const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});
let isViewMode = computed(() => props.viewRecord)

let appStore = useAppStore();
let idOrdenes = ref(null);
let ordenesMedicas = ref([]);
let loadingOrdenes = ref(false);
let idCPT = ref(null);
let cpts = ref([]);

const fetchOptionsApoyoDiagnostico = async (query) => {
    if (!query) {
        ordenesMedicas.value = [];
        return;
    }

    loadingOrdenes.value = true;

    try {
        const response = await axios.post('/filtrar_apoyo_dx', {
            IdServicio: null,
            IdTipoFinanciamiento: props.cabecera.IdTipoFinanciamiento,
            Filtro: query
        });

        ordenesMedicas.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones CPT:', error);
    } finally {
        loadingOrdenes.value = false;
    }
};


const onFilterApoyoDiagnostico = (event) => {
    if (event.value.length > 3) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsApoyoDiagnostico(event.value);
        }, 500);
    }
}

function agregarApoyoDiagnostico() {
    // toast.add({ severity: 'success', summary: 'Agregado', detail: 'Diagnóstico agregado correctamente', life: 2000 });
}

const fetchOptionsCPT = async (query) => {
    if (!query) {
        cpts.value = [];
        return;
    }

    loadingOrdenes.value = true;

    try {
        const response = await axios.post('/filtrar_cpt', {
            IdServicio: props.cabecera.IdServicio,
            IdTipoFinanciamiento: props.cabecera.IdTipoFinanciamiento,
            Filtro: query
        });

        cpts.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones CPT:', error);
    } finally {
        loadingOrdenes.value = false;
    }
};


const onFiltercpts = (event) => {
    if (event.value.length > 2) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsCPT(event.value);
        }, 500);
    }
}
</script>

<template>
    <div class="row g-3 align-items-end">

        <!-- Axámenes de Apoyo al Diagnóstico -->
        <div class="col-xl-5 col-lg-6 col-md-12">
            <label class="form-label">Axámenes de Apoyo al Diagnóstico</label>
            <Select v-model="idOrdenes" :options="ordenesMedicas" option-label="label" option-value="value" filter
                filterPlaceholder="Ingrese como mínimo 3 caracteres" @filter="onFilterApoyoDiagnostico"
                :loading="loadingOrdenes" placeholder="Seleccione una opción" :showClear="true" class="w-full"
                size="small" style="width: 100%;" :autoFilterFocus="true" />
        </div>
        <div class="col-xl-5 col-lg-6 col-md-12">
            <BaseInput value="RESULTADO" :disabled="isViewMode" />
        </div>

        <!-- Botón AGREGAR -->
        <div class="col-xl-2 col-lg-12 col-md-12 col-sm-12 col-12">
            <button  type="button" class="btn btn-primary w-100">
                <i class="bi bi-plus-circle me-1"></i> Agregar
            </button>
        </div>

        <!-- Procedimientos -->
        <div class="col-xl-5 col-lg-6 col-md-12">
            <label class="form-label">Procedimientos</label>
            <Select v-model="idCPT" :options="cpts" option-label="label" option-value="value" filter
                filterPlaceholder="Ingrese como mínimo 3 caracteres" @filter="onFiltercpts" :loading="loadingCPT"
                placeholder="Seleccione una opción" :showClear="true" class="w-full" size="small" style="width: 100%;"
                :autoFilterFocus="true" />
        </div>
        <div class="col-xl-5 col-lg-6 col-md-12">
            <BaseInput value="RESULTADO" :disabled="isViewMode" />
        </div>

        <!-- Botón AGREGAR -->
        <div class="col-xl-2 col-lg-12 col-md-12 col-sm-12 col-12">
            <button  type="button" class="btn btn-primary w-100">
                <i class="bi bi-plus-circle me-1"></i> Agregar
            </button>
        </div>
    </div>
</template>
