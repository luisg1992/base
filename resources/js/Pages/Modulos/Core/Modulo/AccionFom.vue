<script setup>
import {ref} from 'vue';
import axios from 'axios';

import BaseModal from '@/components/BaseModal.vue';
import BaseInput from "@/components/WInput/WInput.vue";
import WButton from "@/components/WButton/WButton.vue";
import {showToastSuccess} from "../../../../utils/alert.js";

const props = defineProps(['moduloId']);

const emit = defineEmits(['close', 'success']);

const resource = 'core/modulos';

let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let title = ref();
let errors = ref({});
let form = ref({});

const initForm = () => {
    errors.value = {};
    form.value = {
        ModuloId: props.moduloId,
        Nombre: null,
        Descripcion: null,
    }
}

const handleOpen = async () => {
    initForm();
    isModalLoading.value = true;
    title.value = 'Nueva acción';

    try {
        if (props.recordId) {
            title.value = 'Editar acción';
            const { data } = await axios.get(`/${resource}/record/${props.recordId}`);
            Object.assign(form.value, data.data);
        }
    } catch (error) {
        console.error('Error al cargar datos del formulario:', error);
    } finally {
        isModalLoading.value = false;
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
        await axios.post(`/${resource}/action`, form.value)
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
            })
            .finally(() => {
                loadingSubmit.value = false;
            })
    }
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
               :header="title"
               @close="closeDialog"
               @open="handleOpen"
               :loading="isModalLoading"
               size="modal-sm">
        <div class="row">
            <div class="col-md-12">
                <BaseInput v-model="form.Nombre"
                           label="Nombre"
                           :error="errors.Nombre"/>
            </div>
            <div class="col-md-12">
                <BaseInput v-model="form.Descripcion"
                           label="Descripción"
                           :error="errors.Descripcion"/>
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
                     :disabled="loadingSubmit"/>
        </div>
    </BaseModal>
</template>
