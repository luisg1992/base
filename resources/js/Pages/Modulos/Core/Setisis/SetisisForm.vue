<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import WButton from "@/components/WButton/WButton.vue";

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
        fecha: null,
    };
}

const handleOpen = async () => {
    initForm();
    title.value = "Generar paquete";
    // if (props.recordId) {
    //     try {
    //         isModalLoading.value = true;
    //         showModal.value = false;
    //         loadingSubmit.value = true;
    //         const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
    //         Object.assign(form.value, data.data)
    //         title.value = "Editar número de celular";
    //     } catch (error) {
    //         if (error.status === 403) {
    //
    //             closeDialog();
    //         }
    //     } finally {
    //         loadingSubmit.value = false;
    //         showModal.value = true;
    //         isModalLoading.value = false;
    //     }
    // } else {
    //     showModal.value = true;
    // }
}

const onSubmit = async () => {
    loadingSubmit.value = true;
    await axios.post(`/${props.resource}/generar-paquete`, form.value)
        .then(response => {
            let res = response.data;
            if (res.success) {
                closeDialog();
                emit('success');
            } else {
            }
            console.log(res);
        })
        .catch(error => {
        })
        .finally(() => {
            loadingSubmit.value = false;
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
               :recordId="form.id"
               :viewRecord="isViewMode"
               :loading="isModalLoading"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-sm">
        <div class="row">
            <div class="col-md-12">
                <base-input v-model="form.fecha"
                            label="Fecha"
                            :error="errors.fecha"/>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4 gap-2">
            <w-button type="secondary"
                     label="Cerrar"
                     text
                     @click="closeDialog"
                     :disabled="loadingSubmit"/>
            <w-button type="primary"
                     :label="`${loadingSubmit?'Generando...' : 'Generar'}`"
                     @click="onSubmit"
                     v-if="!isViewMode"
                     :disabled="loadingSubmit"/>
        </div>
    </BaseModal>
</template>
