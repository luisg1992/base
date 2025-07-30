<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios'
import Select from "primevue/select";
import BaseCombo from "@/components/WSelect/WSelect.vue" 
import Textarea from 'primevue/textarea';
import { useDiagnosticoStore } from '@/stores/useDiagnosticoStore';


let debounceTimeout = null;
const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    diagnosticos: {
        type: Array,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

let isViewMode = computed(() => props.viewRecord)
const diagnosticoStore = useDiagnosticoStore();
onMounted(() => {
    if (diagnosticoStore.diagnosticos.length === 0 && props.diagnosticos.length > 0) {
        diagnosticoStore.setDiagnosticos(props.diagnosticos);
    }
}); 

let idDiagnostico = ref(null);
let idProcedimientos = ref(null);
let procedimientos = ref([]);
let motivo = ref('');
let loadingProcedimientos = ref(false);
const diagnosticoOptions = computed(() => diagnosticoStore.diagnosticoOptions);

const fetchOptionsProcedimientos = async (query) => {
    if (!query) {
        procedimientos.value = [];
        return;
    }

    loadingProcedimientos.value = true; 
    try {
        const response = await axios.post('/filtrar_cpt', {
            IdServicio: props.cabecera.IdServicio,
            IdTipoFinanciamiento: props.cabecera.IdTipoFinanciamiento,
            Filtro: query
        }); 
        procedimientos.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones CPT:', error);
    } finally {
        loadingProcedimientos.value = false;
    }
};


const onFilterProcedimientos = (event) => {
    if (event.value.length > 2) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsProcedimientos(event.value);
        }, 500);
    }
}

function agregarProcedimientos() {
    // if (!idDiagnostico.value) {
    //     toast.add({ severity: 'warn', summary: 'Validación', detail: 'Seleccione un diagnóstico', life: 3000 });
    //     return;
    // } 
    // toast.add({ severity: 'success', summary: 'Agregado', detail: 'Diagnóstico agregado correctamente', life: 2000 });
}

</script>

<template>
    <div class="row g-3 align-items-end">
        <!-- Diagnóstico -->
        <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
            <BaseCombo v-model="idDiagnostico" :options="diagnosticoOptions" label="Diagnóstico" />
        </div>

        <!-- Procedimientos -->
        <div class="col-xl-9 col-lg-8 col-md-12">
            <label class="form-label">Procedimientos</label>
            <Select v-model="idProcedimientos" :options="procedimientos" option-label="label" option-value="value"
                filter filterPlaceholder="Ingrese como mínimo 3 caracteres" @filter="onFilterProcedimientos"
                :loading="loadingProcedimientos" placeholder="Seleccione una opción" :showClear="true" class="w-full"
                size="small" style="width: 100%;" :autoFilterFocus="true" />
        </div>

        <!-- Motivo -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <label class="form-label">Motivo</label>
            <Textarea v-model="motivo" rows="1" autoResize class="w-100" />
        </div>

        <!-- Botón AGREGAR -->
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <button @click="agregarProcedimientos" type="button" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Agregar
                </button>
            </div>
        </div>
    </div>
</template>
