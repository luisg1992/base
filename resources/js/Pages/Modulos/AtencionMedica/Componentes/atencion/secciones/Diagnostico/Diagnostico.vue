<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import Select from "primevue/select";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import { useAppStore } from '@/stores/useAppStore';
import BaseInput from "@/components/WInput/WInput.vue";
import { useDiagnosticoStore } from '@/stores/useDiagnosticoStore';

const props = defineProps({
    cabecera: Object,
    diagnosticos: {
        type: Object,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

const isViewMode = computed(() => props.viewRecord);
const appStore = useAppStore();
const diagnosticoStore = useDiagnosticoStore();

const isModalLoading = ref(false);
const diagnosticoSelectRef = ref(null);
const diagnosticoSeleccionado = ref(null);
const diagnosticosGlobal = ref([]);
const loadingDiagnosticoGlobal = ref(false);
let debounceTimeout = null;

const cargarDiagnosticosAtencion = () => {
    const lista = Array.isArray(props.diagnosticos) ? props.diagnosticos : [];

    const listaClonada = lista.map(item => ({
        IdAtencion: parseInt(item.IdAtencion),
        IdDiagnostico: parseInt(item.IdDiagnostico),
        DiagnosticoNombre: (item.DiagnosticoNombre ?? 'DESCRIPCIÓN DE DIAGNÓSTICO NO ASIGNADO').toUpperCase().trim(),
        IdClasificacionDx: item.IdClasificacionDx ?? 1,
        IdSubclasificacionDx: item.IdSubclasificacionDx ?? 101,
        labConfHIS: item.labConfHIS ?? null,
        labConfHIS_2: item.labConfHIS_2 ?? null,
        labConfHIS_3: item.labConfHIS_3 ?? null,
        GrupoHIS: item.GrupoHIS ?? 0,
        SubGrupoHIS: item.SubGrupoHIS ?? 0
    }));

    // ⚠️ Solo carga el store si no ha sido inicializado
    if (!diagnosticoStore.isInitialized) {
        diagnosticoStore.setDiagnosticos(listaClonada);
    }
};

watch(
    () => props.diagnosticos,
    () => {
        cargarDiagnosticosAtencion();
    },
    { immediate: true, deep: true }
);

// Buscar diagnósticos globalmente
const fetchDiagnosticosGlobal = async (query) => {
    if (!query) {
        diagnosticosGlobal.value = [];
        return;
    }

    loadingDiagnosticoGlobal.value = true;
    try {
        const { data } = await axios.post('/filtrar_diagnosticos', { buscar: query });
        diagnosticosGlobal.value = data;
    } catch (error) {
        console.error(error);
    } finally {
        loadingDiagnosticoGlobal.value = false;
    }
};

const onFilterDiagnosticos = (event) => {
    if (event.value.length > 2) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchDiagnosticosGlobal(event.value);
        }, 500);
    }
};

const agregarDiagnostico = async () => {
    if (!props.cabecera.IdAtencion) {
        showAlert("ERROR", "No se encontró el identificador de atención.", "error");
        return;
    }

    if (!diagnosticoSeleccionado.value) {
        showAlert("VALIDACIÓN", "Debe seleccionar un diagnóstico.", "warning");
        return;
    }
    isModalLoading.value = true;

    const payload = {
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdDiagnostico: diagnosticoSeleccionado.value.value,
        CodigoCIE10: diagnosticoSeleccionado.value.CodigoCIE10,
        IdClasificacionDx: 1,
        IdSubclasificacionDx: 101,
        labConfHIS: null,
        labConfHIS_2: null,
        labConfHIS_3: null,
        GrupoHIS: 0,
        SubGrupoHIS: 0,
        DiagnosticoNombre: diagnosticoSeleccionado.value.label.trim()
    };

    await agregarDiagnosticoOperaciones(payload);
};

const actualizarDiagnostico = async (fila) => {
    isModalLoading.value = true;

    const payload = {
        ...fila,
        DiagnosticoNombre: fila.DiagnosticoNombre.trim()
    };

    await agregarDiagnosticoOperaciones(payload);
};

const agregarDiagnosticoOperaciones = async (payload) => {
    try {
        const { data } = await axios.post('/atencion-medica/WebS_InsertarDiagnosticoAtencion', payload);
        isModalLoading.value = false;

        if (data.success) {
            diagnosticoStore.agregarOActualizarDiagnostico(data.data);
            diagnosticoSeleccionado.value = null;
            if (diagnosticoSelectRef.value) diagnosticoSelectRef.value.filterValue = '';
        } else {
            showAlert("ERROR", data?.mensaje || "Error desconocido.", "warning");
        }

    } catch (error) {
        isModalLoading.value = false;
        console.error(error);
        showAlert("ERROR", error.message || "Error desconocido.", "warning");
    } finally {
        isModalLoading.value = false;
    }
};


const eliminarFilaDiagnostico = async (filaItem) => {
    if (!filaItem.IdAtencion || !filaItem.IdDiagnostico) {
        showAlert("VALIDACIÓN", "Datos incompletos para eliminar.", "warning");
        return;
    }

    isModalLoading.value = true;

    try {
        const { data } = await axios.post('/atencion-medica/WebS_EliminarDiagnosticoAtencion', {
            IdAtencion: filaItem.IdAtencion,
            IdDiagnostico: filaItem.IdDiagnostico
        });

        isModalLoading.value = false;

        if (data.success) {
            diagnosticoStore.eliminarDiagnostico(filaItem.IdDiagnostico);
        } else {
            showAlert("ERROR", data?.mensaje || "No se pudo eliminar.", "warning");
        }

    } catch (error) {
        isModalLoading.value = false;
        console.error(error);
        showAlert("ERROR", error.message || "No se pudo eliminar.", "warning");
    }
};

const listaDiagnosticosAtencion = computed(() => diagnosticoStore.diagnosticos);
const diagnosticoOptions = computed(() => diagnosticosGlobal.value);

</script>

<template>
    <!-- Combo Global de Diagnóstico -->
    <div class="row g-3 align-items-end">
        <div class="col-xl-10 col-lg-9 col-md-12 col-sm-12">
            <label class="form-label">DIAGNÓSTICOS</label>
            <Select ref="diagnosticoSelectRef" v-model="diagnosticoSeleccionado" :options="diagnosticoOptions"
                option-label="label" :option-value="(option) => option" filter style="width: 100%;"
                filterPlaceholder="Ingrese al menos 3 caracteres" @filter="onFilterDiagnosticos" :autoFilterFocus="true"
                :loading="loadingDiagnosticoGlobal" placeholder="DIAGNÓSTICO" :showClear="true" class="w-full"
                size="small" />
        </div>
        <div class="col-xl-2 col-lg-3 col-md-12 col-sm-12 col-12">
            <button @click="agregarDiagnostico" class="btn btn-primary w-100">
                <i class="fas fa-plus-circle me-1"></i> Agregar
            </button>
        </div>
    </div>
    <!-- Lista de Diagnósticos agregados -->
    <div style="overflow-x: auto;" v-if="listaDiagnosticosAtencion.length > 0">
        <table class="table table-sm table-bordered-bottom table-bordered-top table-hover mt-3"
            style="min-width: 1200px; table-layout: fixed;">
            <thead class="align-middle text-center table-light">
                <tr>
                    <th class="py-1" style="width: 350px;">DIAGNÓSTICO</th>
                    <th class="py-1" style="width: 200px;">CLASIFICACIÓN</th>
                    <th class="py-1" style="width: 150px;">LAB1</th>
                    <th class="py-1" style="width: 150px;">LAB2</th>
                    <th class="py-1" style="width: 150px;">LAB3</th>
                    <th class="py-1" style="width: 40px;"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(fila, index) in listaDiagnosticosAtencion" :key="`${fila.IdAtencion}-${fila.IdDiagnostico}`"
                    :class="[{ 'bg-light-gray': index % 2 != 0 }]">

                    <!-- Diagnóstico -->
                    <td class="py-1 text-start">
                        <BaseInput :value="fila.DiagnosticoNombre" :disabled="isViewMode" class="form-control-sm" />
                    </td>

                    <!-- Clasificación -->
                    <td class="py-1">
                        <BaseCombo v-model="fila.IdSubclasificacionDx" @change="() => actualizarDiagnostico(fila)"
                            :options="appStore.tablasCache.subclasificacionDiagnosticosCache" :showClear="false"
                            placeholder="CLASIFICACIÓN" dense :disabled="isModalLoading" />
                    </td>

                    <!-- LAB1 -->
                    <td class="py-1">
                        <BaseCombo v-model="fila.labConfHIS" @change="() => actualizarDiagnostico(fila)"
                            :disabled="isModalLoading" :options="appStore.tablasCache.hisSituacioCache"
                            placeholder="LAB1" :filter="true" dense />
                    </td>

                    <!-- LAB2 -->
                    <td class="py-1">
                        <BaseCombo v-model="fila.labConfHIS_2" @change="() => actualizarDiagnostico(fila)"
                            :disabled="isModalLoading" :options="appStore.tablasCache.hisSituacioCache"
                            placeholder="LAB2" :filter="true" dense />
                    </td>

                    <!-- LAB3 -->
                    <td class="py-1">
                        <BaseCombo v-model="fila.labConfHIS_3" @change="() => actualizarDiagnostico(fila)"
                            :disabled="isModalLoading" :options="appStore.tablasCache.hisSituacioCache"
                            placeholder="LAB3" :filter="true" dense />
                    </td>

                    <!-- Eliminar -->
                    <td class="py-1">
                        <i class="fas fa-times text-secondary cursor-pointer" style="font-size: 1.2rem;"
                            @click="eliminarFilaDiagnostico(fila)" title="Eliminar diagnóstico"
                            :disabled="isModalLoading" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</template>

<!-- Estilo para el fondo de filas pares -->
<style scoped>
.bg-light-gray {
    background-color: #c9c9c92b;
}
</style>