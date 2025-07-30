<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';
import Select from "primevue/select";
import InputNumber from 'primevue/inputnumber';
import BaseCombo from "@/components/WSelect/WSelect.vue";
import BaseInput from "@/components/WInput/WInput.vue";
import Textarea from 'primevue/textarea';
import { useDiagnosticoStore } from '@/stores/useDiagnosticoStore';
import { useOrdenStore } from '@/stores/useOrdenStore';

const props = defineProps({
    cabecera: { type: Object, required: true },
    diagnosticos: { type: Array, required: true },
    productos_apoyo_dx: { type: Array, required: true },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

const diagnosticoStore = useDiagnosticoStore();
const ordenesStore = useOrdenStore();
const diagnosticoOptions = computed(() => diagnosticoStore.diagnosticoOptions);
const isViewMode = computed(() => props.viewRecord);

const ordenSelectRef = ref(null);
const idDiagnostico = ref(null);
const idOrdenes = ref(null);
const ordenesMedicas = ref([]);
const loadingOrdenes = ref(false);
const cantidadDebounceMap = new Map();

const listaOrdenesMedicas = computed(() => ordenesStore.orden);
const debounceTimeout = ref(null);

// Cargar datos iniciales desde productos_apoyo_dx
const cargarApoyoDiagnostico = () => {
    const lista = Array.isArray(props.productos_apoyo_dx) ? props.productos_apoyo_dx : [];

    const listaClonada = lista.map(item => ({
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
        IdMedicoIngreso: parseInt(props.cabecera.IdMedicoIngreso),

        ...item,
        IdReceta: parseInt(item.IdReceta),
        IdProducto: parseInt(item.IdProducto),
        IdDiagnostico: parseInt(item.IdDiagnostico),
        IdPuntoCarga: parseInt(item.IdPuntoCarga),
        CantidadPedida: item.CantidadPedida ?? 1,
        Precio: (item.Precio || 0),
        Observaciones: (item.Observaciones ?? '').toUpperCase().trim()
    }));

    ordenesStore.setOrden(listaClonada);
};

watch(() => props.productos_apoyo_dx, cargarApoyoDiagnostico, { immediate: true, deep: true });

// Buscar órdenes médicas (CPT)
const fetchOptionsCPT = async (query) => {
    if (!query) {
        ordenesMedicas.value = [];
        return;
    }

    loadingOrdenes.value = true;
    try {
        const { data } = await axios.post('/filtrar_apoyo_dx', {
            IdServicio: null,
            IdTipoFinanciamiento: props.cabecera.IdTipoFinanciamiento,
            Filtro: query
        });
        ordenesMedicas.value = data;
    } catch (error) {
        console.error('Error al cargar opciones CPT:', error);
    } finally {
        loadingOrdenes.value = false;
    }
};

const onFilterApoyoDiagnostico = (event) => {
    if (event.value.length >= 3) {
        clearTimeout(debounceTimeout.value);
        debounceTimeout.value = setTimeout(() => {
            fetchOptionsCPT(event.value);
        }, 500);
    }
};

const agregarOrdenMedicaOperaciones = async (payload) => {
    try {
        const url = payload.Insertar === 0
            ? '/atencion-medica/WebS_RecetaDetalleAgregar'
            : '/atencion-medica/WebS_RecetaCabeceraAgregar';

        const { data } = await axios.post(url, payload);

        if (data.success) {
            ordenesStore.agregarOActualizarOrden({ ...payload });
            if (ordenSelectRef.value) ordenSelectRef.value.filterValue = '';
        } else {
            alert(data.message || 'Error al registrar examen de apoyo.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al registrar examen de apoyo.');
    } finally {
        loadingOrdenes.value = false;
    }
};

const agregarApoyoDiagnostico = async () => {
    loadingOrdenes.value = true;

    if (!idDiagnostico.value || !idOrdenes.value) { 
        showAlert("VALIDACIÓN", "Debe seleccionar un diagnóstico y un examen de apoyo.", "warning"); 
        loadingOrdenes.value = false;
        return;
    }

    const diagnostico = diagnosticoOptions.value.find(d => d.value === idDiagnostico.value);
    const orden = ordenesMedicas.value.find(o => o.value === idOrdenes.value);

    const payload = {
        IdReceta: null,
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdPuntoCarga: parseInt(orden.IdPuntoCarga),
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
        IdMedicoIngreso: parseInt(props.cabecera.IdMedicoIngreso),

        IdDiagnostico: parseInt(idDiagnostico.value),
        DiagnosticoNombre: diagnostico.label,
        CodigoDx: diagnostico.CodigoCIE10,

        IdProducto: parseInt(orden.IdProducto),
        NombreProducto: orden.label,
        CantidadPedida: 1,
        Precio: orden.PrecioUnitario || 0,
        SaldoEnRegistroReceta: 0,
        Observaciones: (orden.Observaciones ?? '').toUpperCase().trim(),

        Insertar: 1
    };

    await agregarOrdenMedicaOperaciones(payload);

    idDiagnostico.value = null;
    idOrdenes.value = null;
    ordenesMedicas.value = [];

    if (ordenSelectRef.value) {
        ordenSelectRef.value.filterValue = '';
    }
};

const actualizarApoyoDiagnostico = async (item) => {
    loadingOrdenes.value = true;
    const diagnostico = diagnosticoOptions.value.find(d => d.value === parseInt(item.IdDiagnostico));

    const payload = {
        IdReceta: parseInt(item.IdReceta),
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdPuntoCarga: parseInt(item.IdPuntoCarga),
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
        IdMedicoIngreso: parseInt(props.cabecera.IdMedicoIngreso),

        IdDiagnostico: parseInt(item.IdDiagnostico),
        DiagnosticoNombre: diagnostico.label,
        CodigoDx: diagnostico.CodigoCIE10,

        IdProducto: parseInt(item.IdProducto),
        NombreProducto: item.NombreProducto,
        CantidadPedida: item.CantidadPedida ?? 1,
        Precio: (item.Precio || 0),
        SaldoEnRegistroReceta: item.SaldoEnRegistroReceta || 0,
        Observaciones: (item.Observaciones ?? '').toUpperCase().trim(),

        Insertar: 0
    };

    await agregarOrdenMedicaOperaciones(payload);
};

const eliminarApoyoDiagnostico = async (item) => {
    try {
        const payload = {
            IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
            IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
            IdPuntoCarga: parseInt(item.IdPuntoCarga),
            IdProducto: parseInt(item.IdProducto),
            IdDiagnostico: parseInt(item.IdDiagnostico),
            IdReceta: parseInt(item.IdReceta)
        };

        const { data } = await axios.post('/atencion-medica/WebS_RecetaDetalle_Eliminar', payload);
        if (data.success) {
            ordenesStore.eliminarOrden(item.IdProducto, item.IdDiagnostico);
            if (ordenSelectRef.value) ordenSelectRef.value.filterValue = '';
        } else {
            alert(data.message || 'Error al eliminar el examen de apoyo.');
        }
    } catch (error) {
        console.error('Error al eliminar:', error);
        alert('Error al eliminar el examen de apoyo.');
    }
};

const onCantidadChange = (item) => {
    const key = `${item.IdProducto}-cantidad`;
    if (cantidadDebounceMap.has(key)) cantidadDebounceMap.get(key).cancel();

    const debouncedFn = debounce(() => actualizarApoyoDiagnostico(item), 500);
    cantidadDebounceMap.set(key, debouncedFn);
    debouncedFn();
};

const onObservacionChange = (item) => {
    const key = `${item.IdProducto}-obs`;
    if (cantidadDebounceMap.has(key)) cantidadDebounceMap.get(key).cancel();

    const debouncedFn = debounce(() => actualizarApoyoDiagnostico(item), 2000);
    cantidadDebounceMap.set(key, debouncedFn);
    debouncedFn();
};
</script>


<template>
    <div class="row g-3 align-items-end">
        <!-- Diagnóstico -->
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <BaseCombo v-model="idDiagnostico" :options="diagnosticoOptions" label="Diagnóstico"
                :disabled="loadingOrdenes" :showClear="false" />
        </div>

        <!-- Select de Exámenes -->
        <div class="col-xl-7 col-lg-5 col-md-6 col-sm-12">
            <label class="form-label">Exámenes de Apoyo al Diagnóstico</label>
            <Select ref="ordenSelectRef" v-model="idOrdenes" :options="ordenesMedicas" option-label="label"
                option-value="value" filter filterPlaceholder="Ingrese como mínimo 3 caracteres"
                @filter="onFilterApoyoDiagnostico" :loading="loadingOrdenes" placeholder="Seleccione una opción"
                :showClear="true" class="w-full" size="small" style="width: 100%;" :autoFilterFocus="true" />
        </div>

        <!-- Botón Agregar -->
        <div class="col-xl-2 col-lg-3 col-md-12 col-sm-12">
            <button @click="agregarApoyoDiagnostico" type="button" class="btn btn-primary w-100">
                <i class="fas fa-plus-circle me-1"></i> Agregar
            </button>
        </div>
    </div>


    <div style="overflow-x: auto;" v-if="listaOrdenesMedicas.length > 0">
        <table class="table table-sm table-bordered-bottom table-bordered-top table-hover mt-3"
            style="min-width: 1200px; table-layout: fixed;">
            <thead class="align-middle text-center table-light">
                <tr>
                    <th class="py-1" style="width: 100px;">DIAGNÓSTICO</th>
                    <th class="py-1" style="width: 350px;">PRODUCTO</th>
                    <th class="py-1" style="width: 150px;">CANTIDAD</th>
                    <th class="py-1" style="width: 400px;">OBSERVACIÓN</th>
                    <th class="py-1" style="width: 40px;"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in listaOrdenesMedicas"
                    :key="`${item.IdProducto}-${item.IdDiagnostico}-${index}`"
                    :class="[{ 'bg-light-gray': index % 2 != 0 }]">

                    <!-- Diagnóstico -->
                    <td class="py-1">
                        <BaseCombo v-model="item.IdDiagnostico" :options="diagnosticoOptions" :showClear="false"
                            @change="() => actualizarApoyoDiagnostico(item)" placeholder="DIAGNÓSTICO"
                            :disabled="loadingOrdenes" dense />
                    </td>

                    <!-- Producto y botón observación -->
                    <td class="py-1 text-start">
                        <div class="d-flex align-items-center">
                            <BaseInput :value="item.NombreProducto" disabled class="form-control-sm flex-grow-1" />
                        </div>
                    </td>

                    <!-- Cantidad -->
                    <td class="py-1">
                        <InputNumber v-model="item.CantidadPedida" showButtons buttonLayout="horizontal" :step="1"
                            :min="1" :inputStyle="{ width: '60px' }" class="input-cantidad" :disabled="loadingOrdenes"
                            @input="() => onCantidadChange(item)">
                            <template #incrementbuttonicon><span class="fas fa-plus" /></template>
                            <template #decrementbuttonicon><span class="fas fa-minus" /></template>
                        </InputNumber>
                    </td>

                    <!-- Observación -->
                    <td class="py-1">
                        <div class="d-flex align-items-center">
                            <Textarea v-model="item.Observaciones" rows="1" autoResize :disabled="loadingOrdenes"
                                @input="onObservacionChange(item)" class="form-control" />
                        </div>
                    </td>

                    <!-- Eliminar -->
                    <td class="py-1">
                        <i class="fas fa-times text-secondary cursor-pointer" style="font-size: 1.2rem;"
                            @click="loadingOrdenes ? null : eliminarApoyoDiagnostico(item)" title="Eliminar" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</template>
