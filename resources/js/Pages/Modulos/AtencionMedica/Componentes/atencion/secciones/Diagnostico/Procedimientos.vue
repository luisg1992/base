<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';
import Select from "primevue/select";
import InputNumber from 'primevue/inputnumber';
import BaseCombo from "@/components/WSelect/WSelect.vue";
import BaseInput from "@/components/WInput/WInput.vue";
import { useAppStore } from '@/stores/useAppStore';
import { useDiagnosticoStore } from '@/stores/useDiagnosticoStore';
import { useProcedimientoStore } from '@/stores/useProcedimientoStore';

const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    diagnosticos: {
        type: Array,
        required: true
    },
    IdTipoServicio: [
        Number,
        String
    ],
    procedimientos_cpt: {
        type: Array,
        required: true
    },
    viewRecord: Boolean
});

const appStore = useAppStore();
const diagnosticoStore = useDiagnosticoStore();
const procedimientoStore = useProcedimientoStore();
const procedimientoSelectRef = ref(null);
const isViewMode = computed(() => props.viewRecord);
const diagnosticoOptions = computed(() => diagnosticoStore.diagnosticoOptions);
const listaProcedimientosAtencion = computed(() => procedimientoStore.procedimiento);

let IdDiagnostico = ref(null);
let IdProducto = ref(null);
let cantidadDebounceMap = new Map();
let cpts = ref([]);
let loadingCPT = ref(false);
let debounceTimeout = null;

const toIntOrNull = (value) => {
    return value !== null && value !== undefined ? parseInt(value) : null;
};

// Cargar datos iniciales desde procedimientos_cpt
const cargarProcedimientosCPT = () => {
    const lista = Array.isArray(props.procedimientos_cpt) ? props.procedimientos_cpt : [];

    const listaClonada = lista.map(item => ({
        IdOrden: toIntOrNull(item.IdOrden),
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),

        IdPuntoCarga: 1,
        Cantidad: parseFloat(item.Cantidad ?? 0),

        IdDiagnostico: toIntOrNull(item.IdDiagnostico),
        DiagnosticoNombre: (item.DiagnosticoNombre ?? '').toUpperCase().trim(),
        CodigoDx: (item.CodigoDx ?? '').toUpperCase().trim(),

        IdProducto: toIntOrNull(item.IdProducto),
        Precio: item.Precio !== null && item.Precio !== undefined ? parseFloat(item.Precio) : 0,
        ProcedimientoNombre: item.ProcedimientoNombre.trim(),
        Lab1: toIntOrNull(item.Lab1),
        Lab2: toIntOrNull(item.Lab2),
        Lab3: toIntOrNull(item.Lab3),
        Insertar: 0
    }));

    procedimientoStore.setProcedimiento(listaClonada);
};

watch(() => props.procedimientos_cpt, cargarProcedimientosCPT, { immediate: true, deep: true });

const fetchOptionsCPT = async (query) => {
    if (!query) {
        cpts.value = [];
        return;
    }
    loadingCPT.value = true;
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
        loadingCPT.value = false;
    }
};

const onFiltercpts = (event) => {
    const filtro = event?.value || '';
    if (filtro.length > 3) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsCPT(filtro);
        }, 500);
    }
};

const agregarProcedimientoOperaciones = async (payload) => {
    try {
        const url = payload.Insertar === 0
            ? '/atencion-medica/WebS_ModificarOrdenServicio'
            : '/atencion-medica/WebS_InsertarOrdenServicio';

        const { data } = await axios.post(url, payload);

        if (data.success) {
            procedimientoStore.agregarOActualizarProcedimiento({ ...payload });
            IdDiagnostico.value = null;
            IdProducto.value = null;
            cpts.value = [];
            if (procedimientoSelectRef.value) procedimientoSelectRef.value.filterValue = '';
            loadingCPT.value = false;
        } else {
            loadingCPT.value = false;
            showAlert("LA SOLICITUD NO PUDO SER PROCESADA", data.mensaje, "warning");
        }
    } catch (error) {
        loadingCPT.value = false;
        console.error('Error:', error);
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", error, "warning");
    }
};

const agregarCPT = async () => {
    loadingCPT.value = true;
    if (!IdDiagnostico.value || !IdProducto.value) {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", "Debe seleccionar diagnóstico y procedimiento.", "warning");
        loadingCPT.value = false;
        return;
    }

    const diagnosticoSeleccionado = diagnosticoOptions.value.find(d => d.value === IdDiagnostico.value);
    const procedimientoSeleccionado = cpts.value.find(p => p.value === IdProducto.value);

    if (!diagnosticoSeleccionado || !procedimientoSeleccionado) {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", "Diagnóstico o procedimiento no válido.", "warning");
        return;
    }

    const payload = {
        IdOrden: null,
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),

        IdProducto: procedimientoSeleccionado.IdProducto,
        IdPuntoCarga: 1,
        Cantidad: 1,

        IdDiagnostico: parseInt(diagnosticoSeleccionado.value),
        DiagnosticoNombre: diagnosticoSeleccionado.label.trim(),
        CodigoDx: diagnosticoSeleccionado.CodigoCIE10.trim(),

        IdProducto: procedimientoSeleccionado.value,
        Precio: procedimientoSeleccionado.PrecioUnitario,
        ProcedimientoNombre: procedimientoSeleccionado.label.trim(),
        Lab1: null,
        Lab2: null,
        Lab3: null,
        Insertar: 1
    };

    await agregarProcedimientoOperaciones(payload);
};

const actualizarProcedimiento = async (item) => {
    loadingCPT.value = true;
    const diagnosticoSeleccionado = diagnosticoOptions.value.find(d => parseInt(d.value) === parseInt(item.IdDiagnostico));

    const payload = {
        IdOrden: item.IdOrden,
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),

        IdPuntoCarga: 1,
        Cantidad: parseInt(item.Cantidad),

        IdDiagnostico: parseInt(diagnosticoSeleccionado.value),
        DiagnosticoNombre: diagnosticoSeleccionado.label.trim(),
        CodigoDx: diagnosticoSeleccionado.CodigoCIE10.trim(),

        IdProducto: parseInt(item.IdProducto),
        Precio: item.Precio,
        ProcedimientoNombre: item.ProcedimientoNombre,

        Lab1: item.Lab1,
        Lab2: item.Lab2,
        Lab3: item.Lab3,

        Insertar: 0
    };

    await agregarProcedimientoOperaciones(payload);
};

const eliminarProcedimiento = async (item) => {
    loadingCPT.value = true;
    try {
        const payload = {
            IdOrden: parseInt(item.IdOrden),
            IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
            IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        };

        const { data } = await axios.post('/atencion-medica/WebS_EliminarOrdenServicio', payload);
        if (data.success) {
            procedimientoStore.eliminarProcedimiento(item.IdProducto, item.IdDiagnostico);
            if (procedimientoSelectRef.value) procedimientoSelectRef.value.filterValue = '';
            loadingCPT.value = false;
        } else {
            loadingCPT.value = false;
            alert(data.message || 'Error al eliminar el procedimiento.');
        }
    } catch (error) {
        loadingCPT.value = false;
        console.error('Error al eliminar:', error);
        alert('Error al eliminar el procedimiento.');
    }
};

const onCantidadChange = (item) => {
    loadingCPT.value = false;
    const key = `${item.IdProducto}-${item.IdDiagnostico}`;

    if (cantidadDebounceMap.has(key)) {
        cantidadDebounceMap.get(key).cancel();
    }

    const debouncedFn = debounce(() => {
        actualizarProcedimiento(item);
    }, 500);

    cantidadDebounceMap.set(key, debouncedFn);
    debouncedFn();
};

</script>


<template>
    <div class="row g-3 align-items-end">
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <BaseCombo v-model="IdDiagnostico" :options="diagnosticoOptions" label="DIAGNÓSTICOS"
                placeholder="DIAGNÓSTICOS" :showClear="false" />
        </div>
        <div class="col-xl-7 col-lg-5 col-md-6 col-sm-12">
            <label class="form-label">PROCEDIMIENTOS</label>
            <Select ref="procedimientoSelectRef" v-model="IdProducto" :options="cpts" option-label="label"
                option-value="value" filter filterPlaceholder="Ingrese como mínimo 3 caracteres" @filter="onFiltercpts"
                :loading="loadingCPT" placeholder="PROCEDIMIENTO" :showClear="true" class="w-full" size="small"
                style="width: 100%;" :autoFilterFocus="true" />
        </div>
        <div class="col-xl-2 col-lg-3 col-md-12 col-sm-12 col-12">
            <button @click="agregarCPT" type="button" class="btn btn-primary w-100">
                <i class="fas fa-plus-circle me-1"></i> Agregar
            </button>
        </div>
    </div>

    <div style="overflow-x: auto;" v-if="listaProcedimientosAtencion.length > 0">
        <table class="table table-sm table-bordered-bottom table-bordered-top table-hover mt-3"
            style="min-width: 1200px; table-layout: fixed;">
            <thead class="align-middle text-center table-light">
                <tr>
                    <th class="py-1" style="width: 150px;">DIAGNÓSTICO</th>
                    <th class="py-1" style="width: 300px;">PROCEDIMIENTO</th>
                    <th class="py-1" style="width: 150px;">CANTIDAD</th>
                    <th class="py-1" style="width: 150px;">LAB1</th>
                    <th class="py-1" style="width: 150px;">LAB2</th>
                    <th class="py-1" style="width: 150px;">LAB3</th>
                    <th class="py-1" style="width: 40px;"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in listaProcedimientosAtencion"
                    :key="`${item.IdDiagnostico}-${item.IdProducto}-${index}`"
                    :class="[{ 'bg-light-gray': index % 2 != 0 }]">

                    <!-- Diagnóstico -->
                    <td class="py-1 text-start">
                        <BaseCombo v-model="item.IdDiagnostico" :options="diagnosticoOptions" :showClear="false"
                            @change="() => actualizarProcedimiento(item)" placeholder="DIAGNÓSTICO"
                            :disabled="loadingCPT" dense />
                    </td>

                    <!-- Procedimiento + botón para mostrar LABs -->
                    <td class="py-1 text-start">
                        <div class="d-flex align-items-center">
                            <BaseInput :value="item.ProcedimientoNombre" disabled class="form-control-sm flex-grow-1" />
                        </div>
                    </td>

                    <!-- Cantidad -->
                    <td class="py-1">
                        <InputNumber v-model="item.Cantidad" showButtons buttonLayout="horizontal" :step="1" :min="1"
                            :inputStyle="{ width: '60px' }" class="input-cantidad" @input="() => onCantidadChange(item)"
                            :disabled="loadingCPT">
                            <template #incrementbuttonicon><span class="fas fa-plus" /></template>
                            <template #decrementbuttonicon><span class="fas fa-minus" /></template>
                        </InputNumber>
                    </td>

                    <!-- LAB1 -->
                    <td class="py-1">
                        <BaseCombo v-model="item.Lab1" :options="appStore.tablasCache.hisSituacioCache"
                            placeholder="LAB1" @change="() => actualizarProcedimiento(item)" :disabled="loadingCPT"
                            :filter="true" dense />
                    </td>

                    <!-- LAB2 -->
                    <td class="py-1">
                        <BaseCombo v-model="item.Lab2" :options="appStore.tablasCache.hisSituacioCache"
                            placeholder="LAB2" @change="() => actualizarProcedimiento(item)" :disabled="loadingCPT"
                            :filter="true" dense />
                    </td>

                    <!-- LAB3 -->
                    <td class="py-1">
                        <BaseCombo v-model="item.Lab3" :options="appStore.tablasCache.hisSituacioCache"
                            placeholder="LAB3" @change="() => actualizarProcedimiento(item)" :disabled="loadingCPT"
                            :filter="true" dense />
                    </td>

                    <!-- Eliminar -->
                    <td class="py-1">
                        <i class="fas fa-times text-secondary cursor-pointer" style="font-size: 1.2rem;"
                            @click="loadingOrdenes ? null : eliminarProcedimiento(item)" title="Eliminar" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</template>

<style scoped>
.bg-light-gray {
    background-color: #f8f9fa;
}
</style>
