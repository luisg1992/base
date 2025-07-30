<script setup>
import { ref, computed } from 'vue';
import axios from 'axios'
import Select from "primevue/select";
import BaseCombo from "@/components/WSelect/WSelect.vue";

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

let idDiagnostico = ref(null);
let idProductoFarmacia = ref(null);
let productosFarmcia = ref([]);
let loadingFarmacia = ref(false);

const fetchOptionsProductoFarmacia = async (query) => {
    if (!query) {
        productosFarmcia.value = [];
        return;
    }

    loadingFarmacia.value = true;

    try {
        const response = await axios.post('/filtrar_producto_farmacia', {
            Filtro: query
        });

        productosFarmcia.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones CPT:', error);
    } finally {
        loadingFarmacia.value = false;
    }
};


const onFilterProductosFarmacia = (event) => {
    if (event.value.length >= 4) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsProductoFarmacia(event.value);
        }, 500);
    }
}

function agregarProductosFarmacia() {
    // toast.add({ severity: 'success', summary: 'Agregado', detail: 'Diagnóstico agregado correctamente', life: 2000 });
}

let respuesta = [
    { label: 'SI', value: 1 },
    { label: 'NO', value: 2 },
];

</script>

<template>
    <div class="row g-3 align-items-end"> 

        <!-- Medicamentos e Insumos -->
        <div class="col-xl-12 col-lg-12 col-md-12">
            <label class="form-label">Medicamentos e Insumos</label>
            <Select v-model="idProductoFarmacia" :options="productosFarmcia" option-label="label" option-value="value"
                filter filterPlaceholder="Ingrese como mínimo 3 caracteres" @filter="onFilterProductosFarmacia"
                :loading="loadingFarmacia" placeholder="Seleccione una opción" :showClear="true" class="w-full"
                size="small" style="width: 100%;" :autoFilterFocus="true" />
        </div>
        <div class="col-md-2">
            <BaseCombo :options="[]" optionLabel="label" optionValue="value" label="Presentación"/>
        </div>
        <div class="col-md-2">
            <BaseCombo :options="[]" optionLabel="label" optionValue="value" label="Dosis"/>
        </div>
        <div class="col-md-2">
            <BaseCombo :options="[]" optionLabel="label" optionValue="value" label="Via"/>
        </div>
        <div class="col-md-2">
            <BaseCombo :options="[]" optionLabel="label" optionValue="value" label="Frecuencia"/>
        </div>
        <div class="col-md-2">
            <BaseCombo :options="respuesta" optionLabel="label" optionValue="value" label="Administración" />
        </div>

        <!-- Botón AGREGAR -->
        <div class="col-xl-2 col-lg-12 col-md-12 col-sm-12 col-12">
            <button @click="agregarProductosFarmacia" type="button" class="btn btn-primary w-100">
                <i class="bi bi-plus-circle me-1"></i> Agregar
            </button>
        </div>
    </div>
</template>
