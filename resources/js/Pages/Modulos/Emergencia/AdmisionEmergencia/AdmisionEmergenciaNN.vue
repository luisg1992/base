<script setup>

import {ref, computed} from 'vue';
import axios from 'axios';
import BaseModal from '@/components/BaseModal.vue';
import {useAppStore} from '@/stores/useAppStore';
import WButton from "@/components/WButton/WButton.vue";
import WSelect from "@/components/WSelect/WSelect.vue";
import {showToastSuccess} from "../../../../utils/alert.js";


// Props y emits
const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let appStore = useAppStore();
let loadingSubmit = ref(false);
let loading = ref(false);
let showModal = ref(false);
let isDialogOpen = ref(false);
let title = ref();
let errors = ref({});
let form = ref({});

const initForm = () => {
    title.value = 'Registra paciente no identificado';
    form.value = {
        id: null,
        IdSexo: null,
        IdEdadCategoria : null,
    }
}

const handleOpen = async () => {
    initForm();
}

const onSubmit = async () => {
    if(form.value.IdEdadCategoria === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo categoría de edad es requerido', 'error');
    }
    if(form.value.IdSexo === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo sexo es requerido', 'error');
    }

    const accion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PARA EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accion}?`;
    const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');

    if (!confirmado) return;
    loading.value = true
    await axios.post(`/${props.resource}/no-identificado`, form.value)
        .then(response => {
            const data = response.data;
            console.log(data);
            if (data.success) {
                 emit('success', data);
                closeDialog();
                showToastSuccess(data.mensaje)
            } else {
                showToastError(data.mensaje)
            }
        })
        .catch(error => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors
            }
        })
        .finally(() => {
            loading.value = false;
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
               @close="closeDialog"
               @open="handleOpen"
               :loading="loading"
               :header="title"
               size="modal-xs">
        <div class="row">
            <div class="col-12">
                <w-select v-model="form.IdEdadCategoria"
                          label="Categoría de edad"
                          :show-clear="false"
                          :options="appStore.tablasCache.tipoEdadCategoriaCache"></w-select>
            </div>
            <div class="col-12">
                <w-select v-model="form.IdSexo"
                          label="Sexo"
                          :show-clear="false"
                          :options="appStore.personaTipoSexos"></w-select>
            </div>
            <div class="d-flex justify-content-end mt-4 gap-2">
                <w-button type="secondary"
                          label="Cerrar"
                          text
                          @click="closeDialog"
                          :disabled="loadingSubmit"/>
                <w-button type="primary"
                          :label="`${loadingSubmit?'Guardando...' : 'Guardar'}`"
                          @click="onSubmit"
                          :disabled="loadingSubmit"/>
            </div>
        </div>
    </BaseModal>
</template>
