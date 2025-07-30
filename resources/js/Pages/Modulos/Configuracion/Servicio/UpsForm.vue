<script setup>
import {ref} from 'vue';
import axios from 'axios';
import BaseModal from '@/components/BaseModal.vue';
import BaseCombo from "@/components/WSelect/WSelect.vue";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import {useAppStore} from "../../../../stores/useAppStore.js";

const props = defineProps({
    recordId: Number,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let appStore = useAppStore();
let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let modalReady = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let loadingUps = ref(false);
let debounceTimeout = null;

let title = ref('');
let errors = ref({});
let form = ref({});
let upsCatalogo = ref([]);
let upsAdicionales = ref([]);

const initForm = () => {
    errors.value = {};
    form.value = {
        IdServicio: null,
    };
}

const handleOpen = async () => {
    initForm();
    upsCatalogo.value = [];
    upsAdicionales.value = [];
    title.value = 'UPS adicionales';
    try {
        let response = await axios.get(`/${props.resource}/listar_ups_adicionales/${props.recordId}`)
        Object.assign(upsCatalogo.value, response.data)

        response = await axios.get(`/${props.resource}/ups_adicionales/${props.recordId}`)
        Object.assign(upsAdicionales.value, response.data)
    } catch (error) {
        console.error('Error al obtener detalles:', error)
    } finally {
    }
}

const fetchOptionsUps = async (query) => {
    if (!query) {
        catalogo.value = [];
        return;
    }
    loadingUps.value = true;
    try {
        const response = await axios.post('/configuracion/servicios/filtrar_ups_adicionales', {
            codigo: props.recordId,
            buscar: query
        });
        catalogo.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingUps.value = false;
    }
}

const agregarUpsAdicional = () => {
    let ups = upsCatalogo.value.find(row => row.IdServicio === form.value.IdServicio)
    axios.post(`/${props.resource}/ups_adicional_agregar`, {
        IdServicio: props.recordId,
        IdServicioAtencionSimultanea: ups.IdServicio,
    })
        .then(response => {
            let res = response.data;
            if (res.success) {
                upsAdicionales.value.push({
                    idServicioAtencionSimultanea: ups.IdServicio,
                    Nombre: ups.Nombre,
                })
                initForm();
            } else {
                showAlert('ERROR DURANTE EL PROCESO', res.message, 'error');
            }
        })
        .catch(error => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors
            }
        })
        .finally(() => {
            loadingSubmit.value = false;
        })
}

const removeUpsAdicional = (index, idServicioAtencionSimultanea) => {
    axios.post(`/${props.resource}/ups_adicional_eliminar`, {
        IdServicio: props.recordId,
        IdServicioAtencionSimultanea: idServicioAtencionSimultanea,
    })
        .then(response => {
            let res = response.data;
            if (res.success) {
                upsAdicionales.value.splice(index, 1);
            } else {
                showAlert('ERROR DURANTE EL PROCESO', res.message, 'error');
            }
        })
        .catch(error => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors
            }
        })
        .finally(() => {
            loadingSubmit.value = false;
        })
}

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    isDialogOpen.value = false;
}

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :recordId="form.id"
               :viewRecord="false"
               :loadingSubmit="loadingSubmit"
               @close="closeDialog"
               @open="handleOpen"
               :header="title"
               size="modal-sm">
        <div class="row">
            <div class="col">
                <base-combo label="Almacén destino"
                            v-model="form.IdServicio"
                            :options="upsCatalogo"
                            option-value="IdServicio"
                            option-label="Nombre"
                            filter
                            :show-clear="false"></base-combo>
            </div>
            <div class="col-auto" style="display: flex; align-items: flex-end">
                <button class="btn btn-primary" @click="agregarUpsAdicional" :disabled="!form.IdServicio">Agregar</button>
            </div>
            <div class="col-12" v-if="upsAdicionales.length > 0">
                <DataTable :value="upsAdicionales" tableStyle="width: 100%" size="small">
                    <Column field="idServicioAtencionSimultanea" header="Código"></Column>
                    <Column field="Nombre" header="Nombre"></Column>
                    <Column field="acciones" header="" style="width: 60px">
                        <template #body="slotProps">
                            <button @click="removeUpsAdicional(slotProps.index, slotProps.data.idServicioAtencionSimultanea)"
                                    class="btn btn-danger"
                                    :disabled="loadingUps">
                                <i class="ti ti-trash"></i></button>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </BaseModal>
</template>
