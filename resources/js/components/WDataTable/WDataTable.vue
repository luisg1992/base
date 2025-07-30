<script setup>
import axios from 'axios';
import { ref, onMounted, watch, computed } from 'vue';
import { useHead } from '@vueuse/head';
import Paginator from '../BasePaginator.vue';

import BaseModalDelete from '@/components/WModalDelete/WModalDelete.vue'
import BaseModalEstado from '@/components/WModalActive/WModalActive.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import BaseDatePicker from '@/components/WDatePicker/WDatePicker.vue';
import BaseCombo from "@/components/WSelect/WSelect.vue";
import WCellRenderer from "@/components/WDataTable/WCellRenderer.vue";
import WRowActions from "@/components/WDataTable/WRowActions.vue";
import MultiSelect from "primevue/multiselect";
import WButton from "@/components/WButton/WButton.vue";

const props = defineProps({
    resource: String
});

const emit = defineEmits(['actions', 'successDelete', 'successActive']);

const titulo_pagina = ref('');
const titulo_tabla = ref('');
const records = ref([]);
const columns = ref([]);
const visibleColumns = ref([]);
const selectVisibleColumns = ref([]);
const headerButtons = ref([])
const selectColumns = ref([]);
const isModalLoading = ref(false);
const filters = ref({});
const nombre_tabla = ref(null);

const refDialogDeleteForm = ref()
const refDialogActiveForm = ref()
const recordDeleteId = ref(null)
const recordActiveId = ref(null)

const opcionesOriginales = ref({});

useHead(() => {
    return {
        title: titulo_pagina.value
    };
});

const pagination = ref({
    sortBy: null,
    descending: null,
    page: 0,
    rowsPerPage: 10,
    rowsNumber: 1,
    pageSizes: [5, 10, 20, 50],
    from: 0,
    to: 0,
    last_page: 1
});

const inicializarTabla = async () => {
    isModalLoading.value = true;
    try {
        const response = await axios.get(`/${props.resource}/inicializar_tabla`);
        titulo_pagina.value = response.data.titulo_pagina;
        titulo_tabla.value = response.data.titulo_tabla;
        nombre_tabla.value = response.data.nombre_tabla;
        columns.value = response.data.columns;
        filters.value = response.data.filters.map(f => {
            if (f.type === 'date') {
                let year, month, day;

                if (f.value && /^\d{4}-\d{2}-\d{2}$/.test(f.value)) {
                    [year, month, day] = f.value.split('-');
                } else {
                    const now = new Date();
                    day = String(now.getDate()).padStart(2, '0');
                    month = String(now.getMonth() + 1).padStart(2, '0');
                    year = now.getFullYear();
                }
                f.value = `${day}/${month}/${year}`;
            }



            if (f.type === 'select') {
                opcionesOriginales.value[f.name] = f.options || [];
                if (f.parent) {
                    f.options = [];
                }
            }
            return f;
        });


        const visibleAll = response.data.visible_columns || [];
        selectColumns.value = columns.value
            .filter(col => !col.locked)
            .map(col => ({ label: col.label, value: col.name }));
        selectVisibleColumns.value = visibleAll;

        pagination.value = {
            sortBy: response.data.pagination.sort_by,
            descending: response.data.pagination.descending,
            page: 1,
            rowsPerPage: response.data.pagination.limit,
            pageSizes: response.data.pagination.page_sizes,
            rowsNumber: 1,
            from: 0,
            to: 0,
            last_page: 1
        };
        headerButtons.value = response.data.header_buttons || []

        await actualizarColumnasVisibles(false);
        await listarRegistros();
    } catch (err) {
        console.error("Error al inicializar la tabla", err);
    }
    isModalLoading.value = false;
};

const listarRegistros = async () => {
    isModalLoading.value = true;
    try {
        const filtersForRequest = filters.value.map(filter => {
            return {
                ...filter,
                options: undefined,
                default: undefined,
                includeAllOption: undefined,
            };
        });

        const response = await axios.post(`/${props.resource}/records`, {
            nombre_tabla: nombre_tabla.value,
            page: pagination.value.page,
            limit: pagination.value.rowsPerPage,
            sortBy: pagination.value.sortBy,
            descending: pagination.value.descending,
            filters: filtersForRequest,
        });

        records.value = response.data.data;
        const meta = response.data.meta;

        pagination.value.page = meta.current_page;
        pagination.value.from = meta.from;
        pagination.value.to = meta.to;
        pagination.value.last_page = meta.last_page;
        pagination.value.rowsPerPage = meta.per_page;
        pagination.value.rowsNumber = meta.total;
        pagination.value.sortBy = meta.sort_by;
        pagination.value.descending = meta.descending;
    } catch (err) {
        console.error("Error al obtener los registros", err);
    }
    isModalLoading.value = false;
};

const actualizarColumnasVisibles = async (save = true) => {
    visibleColumns.value = columns.value
        .filter(col => col.locked || selectVisibleColumns.value.includes(col.name))
        .map(col => col.name);

    if (save) {
        await axios.post(`/${props.resource}/actualizar_visibilidad_columnas`, {
            nombre_tabla: nombre_tabla.value,
            visible_columns: selectVisibleColumns.value
        })
            .then(response => console.log(response))
            .catch(error => console.error(error));
    }
};

const actualizarPaginacion = async (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        pagination.value.page = page;
        await listarRegistros();
    }
};

const seleccionarNumeroPagina = (newSize) => {
    pagination.value.rowsPerPage = newSize;
    pagination.value.page = 1;
};

const seleccionarColumna = (column) => {
    if (pagination.value.sortBy === column.name) {
        pagination.value.descending = !pagination.value.descending;
    } else {
        pagination.value.sortBy = column.name;
        pagination.value.descending = false;
    }
    listarRegistros();
};

const seleccionarAction = (button, row) => {
    if (button.action === 'delete') {
        recordDeleteId.value = row.id
        refDialogDeleteForm.value.openDialog(recordDeleteId.value)
        return
    }
    if (button.action === 'active') {
        recordActiveId.value = row.id
        refDialogActiveForm.value.openDialog(recordActiveId.value)
        return
    }
    if (button.action === 'refresh') {
        listarRegistros();
        return
    }

    emit('actions', {
        action: button.action,
        id: row?.id ?? null,
        url: button.url ?? null
    });
}

const successDelete = () => {
    emit('successDelete')
}

const successActive = () => {
    emit('successActive');
}

const consultaFilterDate = async (filter) => {
    if (filter.target && filter.ajaxUrl) {
        const filtroTarget = filters.value.find(f => f.name === filter.target);
        if (!filtroTarget) return;
        try {
            const response = await axios.post(filter.ajaxUrl, {
                [filter.name]: filter.value
            });
            filtroTarget.options = response.data || [];
        } catch (error) {
            console.error(`Error cargando opciones para ${filtroTarget.name} desde ${filter.ajaxUrl}`, error);
            filtroTarget.options = [];
        }
    }
}

const cargarOpcionesAjaxParaFiltro = async (filtro, valorPadre) => {
    if (!filtro?.url || !valorPadre) {
        filtro.options = [];
        return;
    }

    try {
        const response = await axios.post(filtro.url, {
            [filtro.parent]: valorPadre
        });
        filtro.options = response.data || [];
    } catch (error) {
        console.error(`Error cargando opciones para ${filtro.name}`, error);
        filtro.options = [];
    }
};

const seleccionarCamposFiltro = async () => {
    pagination.value.page = 1;

    for (const filter of filters.value) {
        if (filter.parent) {
            const filtroPadre = filters.value.find(f => f.name === filter.parent);
            if (!filtroPadre) continue;

            if (filter.ajax) {
                // Filtro dependiente dinámico por AJAX
                await cargarOpcionesAjaxParaFiltro(filter, filtroPadre.value);
            } else {
                // Filtro dependiente con opciones locales
                const opcionesPadre = opcionesOriginales.value[filter.name] || [];

                filter.options = filtroPadre.value
                    ? opcionesPadre.filter(option =>
                        option[filter.parentIdField] === filtroPadre.value
                    )
                    : [];
            }
        }
    }

    await listarRegistros();
};

const seleccionarCamposFiltroDate = async (value, filter) => { 
    const fecha = new Date(value);
    const dia = String(fecha.getDate()).padStart(2, '0');
    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
    const anio = fecha.getFullYear();
    const fechaFormateada = `${dia}/${mes}/${anio}`;
 
    filter.value = fechaFormateada;
    await consultaFilterDate(filter);
    await listarRegistros();
};


onMounted(inicializarTabla);

defineExpose({
    listarRegistros
})
</script>

<template>
    <ModalLoader v-if="isModalLoading" />
    <div class="card w-data-table">
        <div class="card-header header-elements">
            <div class="card-header-title me-2"><b>{{ titulo_tabla }}</b></div>
            <div class="card-header-elements ms-auto d-flex gap-2">
                <template v-for="(btn, index) in headerButtons" :key="index">
                    <div v-if="btn.permission">
                        <div v-if="btn.separator" class="vr mx-2"></div>
                        <w-button :type="btn.color" :label="btn.label" @click="seleccionarAction(btn)" :icon="btn.icon"
                            :disabled="btn.disable" />
                    </div>
                </template>
                <div>
                    <MultiSelect v-model="selectVisibleColumns" :options="selectColumns" option-value="value"
                        option-label="label" class="w-select-column" :show-toggle-all="false"
                        panel-class="w-select-column-panel" v-if="records.length"
                        @update:model-value="actualizarColumnasVisibles">
                        <template #option="slotProps">
                            <div class="flex items-center">
                                <div>{{ slotProps.option.label }}</div>
                            </div>
                        </template>
                        <template #dropdownicon>
                            <i class="ti ti-columns-3" />
                        </template>
                    </MultiSelect>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div v-if="filters && filters.length > 0" class="row mb-4">
                <div v-for="(filter, index) in filters" :key="index" :class="filter.class">
                    <BaseInput v-if="filter.type === 'input'" v-model="filter.value" :label="filter.label"
                        :id="filter.name" :name="filter.name"
                        :placeholder="filter.placeholder || 'Filtrar ' + filter.label"
                        @keyup.enter="seleccionarCamposFiltro" />

                    <input type="hidden" v-if="filter.type == 'hidden'" v-model="filter.value" :id="filter.name"
                        :name="filter.name">

                    <BaseDatePicker v-if="filter.type === 'date'" v-model="filter.value" :label="filter.label"
                        :id="filter.name" :name="filter.name"
                        :placeholder="filter.placeholder || 'Selecciona ' + filter.label"
                        @date-select="(val) => seleccionarCamposFiltroDate(val, filter)" />

                    <BaseDatePicker v-if="filter.type === 'date-month'"
                                    v-model="filter.value"
                                    :label="filter.label"
                                    :id="filter.name"
                                    :name="filter.name"
                                    view="month"
                                    :placeholder="filter.placeholder || 'Selecciona ' + filter.label"
                                    @update:modelValue="seleccionarCamposFiltro"/>


                    <BaseCombo v-if="filter.type === 'select'" v-model="filter.value" :options="filter.options"
                        optionLabel="label" optionValue="id" :label="filter.label" :filter="true"
                        :placeholder="filter.placeholder" @update:modelValue="seleccionarCamposFiltro">
                        <template #option="{ option }">
                            <div class="flex flex-column">
                                <div>{{ option.label.toUpperCase() }}</div>
                            </div>
                        </template>
                    </BaseCombo>
                </div>
            </div>

            <div v-if="records.length" class="col-span-4 flex flex-col overflow-x-auto">
                <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                    <table class="table table-sm table-hover dataTable no-footer dtr-inline"
                        style="table-layout: auto;">
                        <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th v-for="(column, i) in columns" :key="i"
                                    v-show="visibleColumns.includes(column.name) && column.name !== 'actions'" :class="{
                                        'sortable': column.sortable,
                                        'sorted-asc': pagination.sortBy === column.name && !pagination.descending,
                                        'sorted-desc': pagination.sortBy === column.name && pagination.descending
                                    }" @click="seleccionarColumna(column)" style="cursor: pointer;">
                                    {{ column.label }}
                                    <span v-if="column.sortable">
                                        <i v-if="pagination.sortBy === column.name && !pagination.descending"
                                            class="fas fa-arrow-up"></i>
                                        <i v-if="pagination.sortBy === column.name && pagination.descending"
                                            class="fas fa-arrow-down"></i>
                                        <i v-if="pagination.sortBy !== column.name" class="fas fa-sort"></i>
                                    </span>
                                </th>
                                <th v-if="visibleColumns.includes('actions')" style="width: 1%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(record, index) in records" :key="record.id">
                                <td>{{ (pagination.page - 1) * pagination.rowsPerPage + index + 1 }}</td>
                                <td v-for="(column, i) in columns" :key="i"
                                    v-show="visibleColumns.includes(column.name) && column.name !== 'actions'">
                                    <w-cell-renderer :cell="record[column.name]" />
                                </td>
                                <td v-if="visibleColumns.includes('actions')" style="text-align: center;">
                                    <WRowActions :record="record" @action-selected="seleccionarAction" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Paginator :currentPage="pagination.page" :lastPage="pagination.last_page"
                    :total="pagination.rowsNumber" :from="pagination.from" :to="pagination.to"
                    :rowsPerPage="pagination.rowsPerPage" :pageSizes="pagination.pageSizes"
                    @update:page="actualizarPaginacion" @update:rowsPerPage="seleccionarNumeroPagina" />
            </div>
        </div>
    </div>

    <!-- Componente de eliminación -->
    <BaseModalDelete ref="refDialogDeleteForm" @success="successDelete" :resource="props.resource" />
    <!-- Componente de cambio de estado -->
    <BaseModalEstado ref="refDialogActiveForm" @success="successActive" :resource="props.resource" />
</template>
