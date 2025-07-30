<script setup>
import axios from 'axios';
import {ref, onMounted, watch} from 'vue';
import {useHead} from '@vueuse/head';
import Paginator from './BasePaginator.vue';

import BaseModalDelete from '@/components/WModalDelete/WModalDelete.vue'
import BaseModalEstado from '@/components/WModalActive/WModalActive.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import BaseDatePicker from '@/components/WDatePicker/WDatePicker.vue';
import BaseCombo from "@/components/WSelect/WSelect.vue";
import WCellRenderer from "@/components/WDataTable/WCellRenderer.vue";

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
                const hasValue = !!f.value;
                const date = hasValue ? new Date(f.value) : new Date();

                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();

                f.value = `${day}/${month}/${year}`;
            }

            // Guardamos todas las opciones originales de los SELECT
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
            .map(col => ({label: col.label, value: col.name}));
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

const seleccionarCamposFiltro = () => {
    pagination.value.page = 1;

    // Iteramos sobre todos los filtros
    filters.value.forEach(filter => {
        if (filter.parent) {

            const filtroPadre = filters.value.find(f => f.name === filter.parent);
            if (filtroPadre && filtroPadre.value) {
                const opcionesFiltradas = opcionesOriginales.value[filter.name].filter(option =>
                    option[filter.parentIdField] === filtroPadre.value
                );

                // Actualizar las opciones del filtro hijo
                filter.options = opcionesFiltradas;
            } else {
                filter.options = [];
            }
        }
    });

    listarRegistros();
};

onMounted(inicializarTabla);

defineExpose({
    listarRegistros
})
</script>

<template>
    <ModalLoader v-if="isModalLoading"/>
    <div class="card">
        <div class="card-header header-elements">
            <div class="card-header-title me-2"><b>{{ titulo_tabla }}</b></div>
            <div class="card-header-elements ms-auto d-flex gap-2">
                <template v-for="(btn, index) in headerButtons" :key="index">
                    <div v-if="btn.permission">
                        <div v-if="btn.separator" class="vr mx-2"></div>
                        <!-- Botón -->
                        <button class="btn" :class="'btn-' + (btn.color || 'secondary')" :disabled="btn.disable"
                                @click="seleccionarAction(btn)">
                            <i v-if="btn.icon" :class="btn.icon"></i>
                            {{ btn.label }}
                        </button>
                    </div>
                </template>
            </div>

        </div>

        <div class="card-body">
            <div v-if="filters && filters.length > 0" class="row mb-4">
                <div v-for="(filter, index) in filters" :key="index" :class="filter.class">
                    <!-- BaseInput -->
                    <BaseInput v-if="filter.type === 'input'" v-model="filter.value" :label="filter.label"
                               :id="filter.name" :name="filter.name"
                               :placeholder="filter.placeholder || 'Filtrar ' + filter.label"
                               @keyup.enter="seleccionarCamposFiltro"/>

                    <BaseDatePicker v-if="filter.type === 'date'" v-model="filter.value" :label="filter.label"
                                    :id="filter.name" :name="filter.name"
                                    :placeholder="filter.placeholder || 'Selecciona ' + filter.label"
                                    @update:modelValue="seleccionarCamposFiltro"/>

                    <BaseCombo v-if="filter.type === 'select'"
                               v-model="filter.value"
                               :options="filter.options"
                               optionLabel="label"
                               optionValue="id"
                               :label="filter.label"
                               :filter="true"
                               :placeholder="filter.placeholder" @update:modelValue="seleccionarCamposFiltro">
                        <template #option="{ option }">
                            <div class="flex flex-column">
                                <div>{{ option.label.toUpperCase() }}</div>
                            </div>
                        </template>
                    </BaseCombo>
                </div>
            </div>

            <div v-if="records.length" class="mb-2 d-flex justify-end justify-content-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle hide-arrow"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Mostrar columnas
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end px-3 py-2">
                        <li v-for="(item, index) in selectColumns" :key="index">
                            <div class="form-check d-flex align-items-center gap-2 mb-1 ps-0">
                                <input class="form-check-input m-0" type="checkbox" :id="item.value"
                                       v-model="selectVisibleColumns" :value="item.value"
                                       @change="actualizarColumnasVisibles"/>
                                <label class="form-check-label" :for="item.value">{{ item.label }}</label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div v-if="records.length" class="col-span-4 flex flex-col overflow-x-auto">
                <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                    <table class="table table-sm table-hover dataTable no-footer dtr-inline" style="table-layout: auto;">
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

                                <w-cell-renderer :cell="record[column.name]"/>

<!--                                <template v-if="column.type === 'indicador'">-->
<!--                                        <span :class="'badge bg-label-' + (record[column.name]?.color || 'info')">-->
<!--                                            {{ record[column.name]?.label || 'Sin estado' }}-->
<!--                                        </span>-->
<!--                                </template>-->

<!--                                <template v-else="column.type === 'text'">-->
<!--                                    {{ record[column.name] }}-->
<!--                                </template>-->
                            </td>
                            <td v-if="visibleColumns.includes('actions')" style="text-align: center;">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a v-for="action in record.actions" :key="action.action"
                                           href="javascript:void(0);" class="dropdown-item"
                                           @click="seleccionarAction(action, record)">
                                            <i :class="action.icon + ' me-1'"></i> {{ action.label }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <Paginator :currentPage="pagination.page" :lastPage="pagination.last_page"
                           :total="pagination.rowsNumber" :from="pagination.from" :to="pagination.to"
                           :rowsPerPage="pagination.rowsPerPage" :pageSizes="pagination.pageSizes"
                           @update:page="actualizarPaginacion" @update:rowsPerPage="seleccionarNumeroPagina"/>
            </div>
        </div>
    </div>

    <!-- Componente de eliminación -->
    <BaseModalDelete ref="refDialogDeleteForm" @success="successDelete" :resource="props.resource"/>
    <!-- Componente de cambio de estado -->
    <BaseModalEstado ref="refDialogActiveForm" @success="successActive" :resource="props.resource"/>
</template>
