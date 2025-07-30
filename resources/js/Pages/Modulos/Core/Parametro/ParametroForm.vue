<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import WButton from "@/components/WButton/WButton.vue";
import {showToastSuccess} from "../../../../utils/alert.js";

const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);

let isViewMode = computed(() => props.viewRecord)
let errors = ref({});
let form = ref({});
let title = ref('');

const initForm = () => {
    errors.value = {};
    form.value = {
        Tipo: null,
        Codigo: null,
        ValorTexto: null,
        ValorInt: null,
        ValorFloat: null,
        Descripcion: null,
        Grupo: null,
    };
}

const handleOpen = async () => {
    initForm();
    title.value = "Registrar parámetro";
    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form.value, data.data)
            title.value = "Editar parámetro";
        } catch (error) {
            if (error.status === 403) {

                closeDialog();
            }
        } finally {
            showModal.value = true;
            isModalLoading.value = false;
        }
    } else {
        showModal.value = true;
    }
}

const onSubmit = async () => {
    const accionConfirmacion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PAR EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensajeConfirmacion = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accionConfirmacion}?`;
    const confirmado = await showAlertConfirmacion(
        'VALIDACIÓN DE DATOS REALIZADOS', mensajeConfirmacion,
        'warning'
    );

    if (confirmado) {
        loadingSubmit.value = true;

        await axios.post(`/${props.resource}`, form.value)
            .then(response => {
                const data = response.data;
                if(data.success) {
                    emit('success');
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
                loadingSubmit.value = false;
            })
    }
};

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :recordId="form.id"
               :viewRecord="isViewMode"
               :loading="isModalLoading"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-md">
        <div class="row">
            <div class="col-md-6">
                <BaseInput v-model="form.Tipo"
                           label="Tipo"
                           :maxlength="20"
                           :disabled="isViewMode"
                           :error="errors.Tipo"/>
            </div>
            <div class="col-md-6">
                <BaseInput v-model="form.Codigo"
                           label="Código"
                           :maxlength="20"
                           :disabled="isViewMode"
                           :error="errors.Codigo"/>
            </div>
            <div class="col-md-12">
                <BaseInput v-model="form.ValorTexto"
                           label="Valor texto"
                           :maxlength="355"
                           :disabled="isViewMode"
                           :error="errors.ValorTexto"/>
            </div>
            <div class="col-md-6">
                <BaseInput v-model="form.ValorInt"
                           label="Valor int"
                           :disabled="isViewMode"
                           :error="errors.ValorInt"/>
            </div>
            <div class="col-md-6">
                <BaseInput v-model="form.ValorFloat"
                           label="Valor float"
                           :disabled="isViewMode"
                           :error="errors.ValorFloat"/>
            </div>
            <div class="col-md-12">
                <BaseInput v-model="form.Descripcion"
                           label="Descripción"
                           :maxlength="150"
                           :disabled="isViewMode"
                           :error="errors.Descripcion"/>
            </div>
            <div class="col-md-6">
                <BaseInput v-model="form.Grupo"
                           label="Grupo"
                           :maxlength="30"
                           :disabled="isViewMode"
                           :error="errors.Grupo"/>
            </div>
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
                     v-if="!isViewMode"
                     :disabled="loadingSubmit"/>
        </div>
    </BaseModal>
</template>
