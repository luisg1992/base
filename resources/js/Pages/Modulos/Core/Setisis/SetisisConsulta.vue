<script setup>
import {ref} from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue';
import BaseInput from "@/components/BaseInput.vue";

const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false);
let isModalLoading = ref(false);
let loadingConsultar = ref(false);

const resource = ref('core/setisis');
let errors = ref({});
let form = ref({});
let title = ref('');

const initForm = () => {
    title.value = "Consultar paquete";
    errors.value = {};
    form.value = {
        IdSetisis: null,
        Fecha: null,
        PaqueteNumero: null,
        Carga: null,
        Estado: null,
        Observaciones: [],
    };
}

const handleOpen = async () => {
    initForm();
    try {
        isModalLoading.value = true;
        const {data} = await axios.get(`/${resource.value}/record/${props.recordId}`)
        Object.assign(form.value, data.data)
    } catch (error) {
        console.log(error);
    } finally {
        isModalLoading.value = false;
    }
}

const consultar = async () => {
    isModalLoading.value = true;
    await axios.get(`/${resource.value}/consultar-paquete/${form.value.id}`)
        .then(response => {
            let res = response.data;
            console.log(res);
            if (res.success) {
                form.value.Estado = res.data.data.estado;
                form.value.Carga = res.data.data.carga;
                form.value.Observaciones = res.data.data.observaciones;
            }
        })
        .finally(() => {
            isModalLoading.value = false;
        })
};

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
               :loading="isModalLoading"
               :header="title"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-lg">
        <div class="row">
            <div class="col-md-4">
                <BaseInput v-model="form.Fecha"
                           label="Fecha"
                           disabled
                           :error="errors.Fecha"/>
            </div>
            <div class="col-md-8">
                <BaseInput v-model="form.PaqueteNumero"
                           label="Número de paquete"
                           disabled
                           :error="errors.PaqueteNumero"/>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <button @click="consultar" class="btn btn-primary" :disabled="loadingConsultar">
                {{ loadingConsultar ? 'Consultando...' : 'Consultar' }}
            </button>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span><strong>Estado:</strong></span> {{ form.Estado }}
            </div>
            <div class="col-md-12">
                <div v-for="(value, key) in form.Carga" :key="key">
                    {{ key }}: {{ value }}
                </div>
            </div>
            <div class="col-md-12">
                <strong>Observaciones:</strong>
                <template v-for="(value, key) in form.Observaciones" :key="key">
                    <div v-for="(value1, key1) in value.errores" :key="key1">
                        - {{ value1 }}
                    </div>
                </template>
            </div>
        </div>
    </BaseModal>
</template>
