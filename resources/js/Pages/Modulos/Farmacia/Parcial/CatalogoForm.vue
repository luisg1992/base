<script setup>

import {ref} from 'vue';
import axios from 'axios';
import BaseModal from '@/components/BaseModal.vue';
import BaseInput from "@/components/WInput/WInput.vue";
import Select from "primevue/select";
import InputNumber from "primevue/inputnumber";

const emit = defineEmits(['close', 'success'])

let loadingCatalogo = ref(false);
let catalogo = ref([]);
let isDialogOpen = ref(false);
let debounceTimeout = null;
let inputLote = ref();
let form = ref({});

const initForm = () => {
    form.value = {
        id: null,
        Nombre: '',
        Cantidad: 1,
        Precio: 0,
        Lote: '',
        RegistroSanitario: '',
        Total: 0,
        acciones: ''
    }
}

const handleOpen = async () => {
    initForm();
}

const changeCatalogo = () => {
    let item = catalogo.value.find(row => row.IdProducto === form.value.id)
    if (item) {
        form.value.Nombre = item.Nombre
        form.value.Precio = Number(item.PrecioUnitario);
    }
}

const agregarCatalogo = () => {
    form.value.Total = Number((Number(form.value.Cantidad) * Number(form.value.Precio)).toFixed(2));
    emit('success', form.value)
    initForm();
}

const fetchOptionsCatalogo = async (query) => {
    if (!query) {
        catalogo.value = [];
        return;
    }
    loadingCatalogo.value = true;
    try {
        const response = await axios.post('/filtrar_catalogo_bienes_insumos', {buscar: query});
        catalogo.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingCatalogo.value = false;
    }
}

const onFilterCatalogo = (event) => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        fetchOptionsCatalogo(event.value);
    }, 500);
}

const focusNextInput = (nameInput) => {
    console.log('focusPrecioUnitario');
    const input = document.querySelector(`[name="${nameInput}"]`);
    if (input) input.focus();
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

const openDialog = () => {
    isDialogOpen.value = true;
}

defineExpose({openDialog})
</script>

<template>
    <base-modal :isVisible="isDialogOpen"
                @close="closeDialog"
                @open="handleOpen"
                header="Buscar bien o insumo"
                size="modal-sm">
        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Seleccionar el bien o insumo</label>
                <Select v-model="form.id"
                        :options="catalogo"
                        optionLabel="label"
                        optionValue="value"
                        filter
                        filterPlaceholder="Buscar..."
                        @filter="onFilterCatalogo"
                        :loading="loadingCatalogo"
                        placeholder="Seleccione una opción"
                        :showClear="true"
                        class="w-full"
                        size="small"
                        style="width: 100%;"
                        :autoFilterFocus="true"
                        @keydown.enter="() => focusNextInput('inputCantidad')"
                        @update:model-value="changeCatalogo"></Select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cantidad</label>
                <InputNumber v-model="form.Cantidad"
                             show-buttons
                             buttonLayout="horizontal"
                             name="inputCantidad"
                             @keydown.enter="() => focusNextInput('inputLote')"
                             fluid></InputNumber>
            </div>
            <div class="col-md-4">
                <base-input label="Precio"
                            v-model="form.Precio"
                            :readonly="true"></base-input>
            </div>
            <div class="col-md-4">
                <base-input label="Lote"
                            name="inputLote"
                            @keydown.enter="() => focusNextInput('inputRegistroSanitario')"
                            v-model="form.Lote"></base-input>
            </div>
            <div class="col-md-4">
                <base-input label="Registro sanitario"
                            name="inputRegistroSanitario"
                            @keydown.enter="() => agregarCatalogo()"
                            v-model="form.RegistroSanitario"></base-input>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <button @click="closeDialog"
                    class="btn btn-secondary">Cerrar
            </button>
            <button @click="agregarCatalogo"
                    class="btn btn-primary">Agregar</button>
        </div>
    </base-modal>
</template>
