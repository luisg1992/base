<script setup>
import { ref, computed, watch } from 'vue';
import { debounce } from 'lodash';
import axios from 'axios';
import Select from "primevue/select";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import InputNumber from 'primevue/inputnumber';
import BaseInput from '@/components/WInput/WInput.vue';

import { useAppStore } from '@/stores/useAppStore';
import { useDiagnosticoStore } from '@/stores/useDiagnosticoStore';
import { useFarmaciaStore } from '@/stores/useFarmaciaStore';

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
    productos_farmacia: {
        type: Array,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

const cantidadDebounceMap = new Map();
const isViewMode = computed(() => props.viewRecord);
const appStore = useAppStore();
const diagnosticoStore = useDiagnosticoStore();
const farmaciaStore = useFarmaciaStore();
const listaFarmaciaAtencion = computed(() => farmaciaStore.farmacia); // ✅ Agregado

const farmaciaSelectRef = ref(null);
const diagnosticoOptions = computed(() => diagnosticoStore.diagnosticoOptions);


let IdDiagnostico = ref(null);
let cantidad = ref(1);
let idProductoFarmacia = ref(null);
let productosFarmacia = ref([]);
let loadingFarmacia = ref(false);
const isModalLoading = ref(false);

const toIntOrNull = (value) => {
    return value !== null && value !== undefined ? parseInt(value) : null;
};

const cargarFarmaciaAtencion = () => {
    const lista = Array.isArray(props.productos_farmacia) ? props.productos_farmacia : [];

    const listaClonada = lista.map(item => ({
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdPuntoCarga: 5,
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
        IdMedicoIngreso: parseInt(props.cabecera.IdMedicoIngreso),

        IdReceta: toIntOrNull(item.IdReceta),
        IdDiagnostico: toIntOrNull(item.IdDiagnostico),
        DiagnosticoNombre: (item.DiagnosticoNombre ?? '').toUpperCase().trim(),
        CodigoDx: (item.CodigoDx ?? '').toUpperCase().trim(),

        IdDosis: toIntOrNull(item.IdDosis),
        DosisNombre: (item.DosisNombre ?? '').toUpperCase().trim(),
        IdRecetaFrecuencia: toIntOrNull(item.IdRecetaFrecuencia),
        FrecuenciaNombre: (item.FrecuenciaNombre ?? '').toUpperCase().trim(),
        IdViaAdministracion: toIntOrNull(item.IdViaAdministracion),
        ViaNombre: (item.ViaNombre ?? '').toUpperCase().trim(),
        IdRecetaDosisUnidadMedida: toIntOrNull(item.IdRecetaDosisUnidadMedida),
        UnidadNombre: (item.UnidadNombre ?? '').toUpperCase().trim(),

        IdProducto: toIntOrNull(item.IdProducto),
        CantidadPedida: parseFloat(item.CantidadPedida ?? 0),
        Precio: item.Precio !== null && item.Precio !== undefined ? parseFloat(item.Precio) : 0,
        SaldoEnRegistroReceta: parseFloat(item.SaldoEnRegistroReceta ?? 0),
        Observaciones: (item.Observaciones ?? '').toUpperCase().trim(),
        NombreProducto: (item.NombreProducto ?? '').toUpperCase().trim(),
        TipoProducto: toIntOrNull(item.TipoProducto),

        Duracion: toIntOrNull(item.Duracion ?? 1),
        Tratamiento: (item.Tratamiento ?? '').toUpperCase().trim(),
    }));

    farmaciaStore.setFarmacia(listaClonada);
};

// Cargar al iniciar o al cambiar productos_farmacia
watch(
    () => props.productos_farmacia,
    () => {
        cargarFarmaciaAtencion();
    },
    { immediate: true, deep: true }
);

const fetchOptionsProductoFarmacia = async (query) => {
    if (!query) {
        productosFarmacia.value = [];
        return;
    }

    loadingFarmacia.value = true;
    try {
        const response = await axios.post('/filtrar_producto_farmacia', {
            Filtro: query
        });
        productosFarmacia.value = response.data;
    } catch (error) {
        console.error('Error al cargar medicamentos:', error);
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
};

const agregarProductosFarmacia = async () => {
    if (!props.cabecera.IdAtencion) {
        showAlert("ERROR", "No se encontró el identificador de atención.", "error");
        return;
    }

    if (!IdDiagnostico.value || !idProductoFarmacia.value) {
        showAlert("VALIDACIÓN", "Debe seleccionar diagnóstico y producto.", "warning");
        return;
    }

    const diagnostico = diagnosticoOptions.value.find(d => d.value === IdDiagnostico.value);
    const producto = productosFarmacia.value.find(p => p.value === idProductoFarmacia.value);

    if (!diagnostico || !producto) {
        showAlert("VALIDACIÓN", "Diagnóstico o producto no válido.", "warning");
        return;
    }

    isModalLoading.value = true;

    const payload = {
        IdReceta: null,
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdPuntoCarga: 5,
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
        IdMedicoIngreso: parseInt(props.cabecera.IdMedicoIngreso),

        IdDiagnostico: parseInt(IdDiagnostico.value),
        DiagnosticoNombre: diagnostico.label,
        CodigoDx: diagnostico.CodigoCIE10,

        IdProducto: parseInt(producto.IdProducto),
        CantidadPedida: cantidad.value,
        Precio: producto.Precio !== null && producto.Precio !== undefined ? parseFloat(producto.Precio) : 0,
        SaldoEnRegistroReceta: parseFloat(producto.Stock),

        IdDosis: null,
        DosisNombre: null,

        Observaciones: '',

        IdViaAdministracion: null,
        ViaNombre: null,

        IdRecetaDosisUnidadMedida: null,
        UnidadNombre: null,

        IdRecetaFrecuencia: null,
        FrecuenciaNombre: null,

        Duracion: 1,
        NombreProducto: producto.Nombre,
        TipoProducto: parseInt(producto.TipoProducto),

        Insertar: 1
    };

    await agregarFarmaciaOperaciones(payload);
};

const actualizarProductosFarmacia = async (fila) => {
    isModalLoading.value = true;

    const diagnostico = diagnosticoOptions.value.find(d => d.value === fila.IdDiagnostico); 
    fila.IdDiagnostico = parseInt(fila.IdDiagnostico);
    fila.DiagnosticoNombre = diagnostico.label;
    fila.CodigoDx = diagnostico.CodigoCIE10;

    const dosis = appStore.tablasCache.recetaDosisCache.find(d => d.value === fila.IdDosis);
    fila.IdDosis = dosis?.value;
    fila.DosisNombre = dosis?.label || '';

    const unidad = appStore.tablasCache.recetaDosisUnidadMedidaCache.find(u => u.value === fila.IdRecetaDosisUnidadMedida);
    fila.IdRecetaDosisUnidadMedida = unidad?.value;
    fila.UnidadNombre = unidad?.label || '';

    const frecuencia = appStore.tablasCache.recetaFrecuenciaCache.find(f => f.value === fila.IdRecetaFrecuencia);
    fila.IdRecetaFrecuencia = frecuencia?.value;
    fila.FrecuenciaNombre = frecuencia?.label || '';

    const via = appStore.tablasCache.recetaViaAdministracionCache.find(v => v.value === fila.IdViaAdministracion);
    fila.IdViaAdministracion = via?.value;
    fila.ViaNombre = via?.label || '';
 
    if (fila.TipoProducto === 0) {
        fila.Tratamiento = `CANTIDAD: ${fila.CantidadPedida ?? ''} => ${fila.NombreProducto} => ${fila.DosisNombre} ${fila.UnidadNombre} ${fila.FrecuenciaNombre}, POR ${fila.Duracion || 0} DÍA(S), VÍA: ${fila.ViaNombre}`.toUpperCase();
    } else {
        fila.Tratamiento = `${fila.NombreProducto} => CANTIDAD: ${fila.CantidadPedida}`.toUpperCase();
    }

    const payload = {
        IdReceta: fila.IdReceta,
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdPuntoCarga: 5,
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
        IdMedicoIngreso: parseInt(props.cabecera.IdMedicoIngreso),

        ...fila,
        Insertar: 0
    };

    await agregarFarmaciaOperaciones(payload);
};

const onCantidadChange = (fila) => {
    if (cantidadDebounceMap.has(fila.IdProducto)) {
        cantidadDebounceMap.get(fila.IdProducto).cancel();
    }

    const debouncedFn = debounce(() => {
        actualizarProductosFarmacia(fila);
    }, 500);

    cantidadDebounceMap.set(fila.IdProducto, debouncedFn);
    debouncedFn();
};

const onDuracionChange = (fila) => {
    if (cantidadDebounceMap.has(fila.IdProducto)) {
        cantidadDebounceMap.get(fila.IdProducto).cancel();
    }

    const debouncedFn = debounce(() => {
        actualizarProductosFarmacia(fila);
    }, 500);

    cantidadDebounceMap.set(fila.IdProducto, debouncedFn);
    debouncedFn();
};

const agregarFarmaciaOperaciones = async (payload) => {
    try {
        isModalLoading.value = true;
        const url = payload.Insertar === 0
            ? '/atencion-medica/WebS_RecetaDetalleAgregar'
            : '/atencion-medica/WebS_RecetaCabeceraAgregar';

        const { data } = await axios.post(url, payload);

        if (data.success) {
            farmaciaStore.agregarOActualizarFarmacia(payload);
            if (farmaciaSelectRef.value) farmaciaSelectRef.value.filterValue = '';
        } else {
            showAlert("LA SOLICITUD NO PUDO SER PROCESADA", data.message || "Error al procesar receta.", "error");
        }
    } catch (error) {
        console.error("Error en receta:", error);
        showAlert("ERROR", "Ocurrió un error en la operación de farmacia.", "error");
    } finally {
        isModalLoading.value = false;
        IdDiagnostico.value = null;
        idProductoFarmacia.value = null;
        productosFarmacia.value = [];
    }
};

const eliminarFilaFarmacia = async (filaItem) => {
    if (!filaItem.IdProducto) {
        showAlert("VALIDACIÓN", "Datos incompletos para eliminar el producto.", "warning");
        return;
    }

    isModalLoading.value = true;
    try {
        const { data } = await axios.post('/atencion-medica/WebS_RecetaDetalle_Eliminar', {
            IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
            IdPuntoCarga: 5,
            IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
            IdProducto: parseInt(filaItem.IdProducto),
            IdReceta: parseInt(filaItem.IdReceta)
        });

        if (data.success) {
            farmaciaStore.eliminarFarmacia(filaItem.IdProducto, filaItem.IdDiagnostico);
            if (farmaciaSelectRef.value) farmaciaSelectRef.value.filterValue = '';
        } else {
            showAlert("ERROR", data?.mensaje || "No se pudo eliminar el producto.", "warning");
        }

    } catch (error) {
        console.error(error);
        showAlert("ERROR", error.message || "No se pudo eliminar el producto.", "warning");
    } finally {
        isModalLoading.value = false;
    }
};

</script>

<template>
    <div class="row g-3 align-items-end">
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <BaseCombo v-model="IdDiagnostico" :options="diagnosticoOptions" label="Diagnóstico"
                placeholder="DIAGNÓSTICOS" :showClear="false" />
        </div>

        <div class="col-xl-7 col-lg-5 col-md-6 col-sm-12 col-12">
            <label class="form-label">MEDICAMENTOS O INSUMOS</label>
            <Select ref="farmaciaSelectRef" v-model="idProductoFarmacia" :options="productosFarmacia"
                option-label="label" option-value="value" filter filterPlaceholder="Ingrese como mínimo 4 caracteres"
                @filter="onFilterProductosFarmacia" :loading="loadingFarmacia" placeholder="MEDICAMENTOS O INSUMOS"
                :showClear="true" class="w-full" size="small" style="width: 100%;" :autoFilterFocus="true" />
        </div>

        <div class="col-xl-2 col-lg-3 col-md-12 col-sm-12 col-12">
            <button @click="agregarProductosFarmacia" type="button" class="btn btn-primary w-100">
                <i class="fas fa-plus-circle me-1"></i> Agregar
            </button>
        </div>
    </div>

    <div style="overflow-x: auto;" v-if="listaFarmaciaAtencion.length > 0">
        <table class="table table-sm table-bordered-bottom table-bordered-top table-hover mt-2"
            style="min-width: 1200px; table-layout: fixed;">
            <thead class=" align-middle text-center table-light">
                <tr>
                    <th class="py-1" style="width: 150px;">DIAGNÓSTICO</th>
                    <th class="py-1" style="width: 300px;">MEDICAMENTO / INSUMO</th>
                    <th class="py-1" style="width: 150px;">CANT.</th>
                    <th class="py-1" style="width: 180px;">DOSIS</th>
                    <th class="py-1" style="width: 180px;">UNID</th>
                    <th class="py-1" style="width: 180px;">FREC</th>
                    <th class="py-1" style="width: 180px;">VIA ADM</th>
                    <th class="py-1" style="width: 150px;">DÍAS</th>
                    <th class="py-1" style="width: 40px;"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(fila, index) in listaFarmaciaAtencion"
                    :key="`${fila.IdDiagnostico}-${fila.IdProducto}-${index}`"
                    :class="[{ 'bg-light-gray': index % 2 != 0 }]">
                    <td class="py-1">
                        <BaseCombo v-model="fila.IdDiagnostico" :options="diagnosticoOptions" :showClear="false"
                            @change="() => actualizarProductosFarmacia(fila)" placeholder="DIAGNÓSTICO"
                            :disabled="isModalLoading" dense />
                    </td>

                    <td class="py-1">
                        <BaseInput :value="fila.NombreProducto" :disabled="true" class="form-control-sm" />
                    </td>

                    <td class="py-1">
                        <InputNumber v-model="fila.CantidadPedida" inputId="cantidad" showButtons
                            buttonLayout="horizontal" :step="1" :min="1" :inputStyle="{ width: '60px' }"
                            class="input-cantidad" :disabled="isModalLoading" @input="() => onCantidadChange(fila)">
                            <template #incrementbuttonicon><span class="fas fa-plus" /></template>
                            <template #decrementbuttonicon><span class="fas fa-minus" /></template>
                        </InputNumber>
                    </td>

                    <td class="py-1">
                        <BaseCombo v-model="fila.IdDosis" :options="appStore.tablasCache.recetaDosisCache"
                            v-if="fila.TipoProducto == 0" @change="() => actualizarProductosFarmacia(fila)"
                            placeholder="NO ASIGNADO" :disabled="isModalLoading" :filter="true" dense />
                    </td>

                    <td class="py-1">
                        <BaseCombo v-model="fila.IdRecetaDosisUnidadMedida" v-if="fila.TipoProducto == 0"
                            :options="appStore.tablasCache.recetaDosisUnidadMedidaCache" :filter="true"
                            @change="() => actualizarProductosFarmacia(fila)" placeholder="NO ASIGNADO"
                            :disabled="isModalLoading" dense />
                    </td>

                    <td class="py-1">
                        <BaseCombo v-model="fila.IdRecetaFrecuencia" v-if="fila.TipoProducto == 0"
                            :options="appStore.tablasCache.recetaFrecuenciaCache" :filter="true"
                            @change="() => actualizarProductosFarmacia(fila)" placeholder="NO ASIGNADO"
                            :disabled="isModalLoading" dense />
                    </td>

                    <td class="py-1">
                        <BaseCombo v-model="fila.IdViaAdministracion" v-if="fila.TipoProducto == 0"
                            :options="appStore.tablasCache.recetaViaAdministracionCache" :filter="true"
                            @change="() => actualizarProductosFarmacia(fila)" placeholder="NO ASIGNADO"
                            :disabled="isModalLoading" dense />
                    </td>

                    <td class="py-1">
                        <InputNumber v-model="fila.Duracion" inputId="duracion" showButtons buttonLayout="horizontal"
                            :step="0.5" :min="0.5" :inputStyle="{ width: '60px' }" class="input-cantidad"
                            v-if="fila.TipoProducto == 0" :disabled="isModalLoading"
                            @input="() => onDuracionChange(fila)">
                            <template #incrementbuttonicon><span class="fas fa-plus" /></template>
                            <template #decrementbuttonicon><span class="fas fa-minus" /></template>
                        </InputNumber>
                    </td>

                    <td class="py-1">
                        <i class="fas fa-times text-secondary cursor-pointer" style="font-size: 1.2rem;"
                            @click="isModalLoading ? null : eliminarFilaFarmacia(fila)" title="Eliminar" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style>
.input-cantidad .p-inputtext {
    text-align: center;
    font-weight: bold;
    font-size: 0.7rem !important;
}

.bg-light-gray {
    background-color: #c9c9c92b;
}
</style>
