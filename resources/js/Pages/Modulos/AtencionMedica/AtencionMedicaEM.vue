<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'
import DatosPacienteEM from './Componentes/paciente/DatosPacienteEM.vue';
import SeccionesAtencionEM from './Componentes/atencion/SeccionesAtencionEM.vue';

const cabecera = ref(null);
const props = defineProps({
    recordId: [Number, String],
    IdTipoServicio: [Number, String],
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let isViewMode = computed(() => props.viewRecord)


const handleOpen = async () => {
    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const { data } = await axios.get(`/${props.resource}/record/${props.recordId}`);
            if (data.success) {
                cabecera.value = data.data;
            }
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
};


const onSubmit = async () => {

};

const openDialog = () => {
    isDialogOpen.value = true;

}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

defineExpose({ openDialog })
</script>

<template>
    <BaseModal :isVisible="isDialogOpen" :recordId="props.recordId" :viewRecord="isViewMode" :loading="isModalLoading"
        @close="closeDialog" @open="handleOpen" size="modal-xl">

        <div class="row">
            <div class="col-12">
                <DatosPacienteEM v-if="cabecera" :cabecera="cabecera" :viewRecord="isViewMode"
                    :IdTipoServicio="IdTipoServicio" />
            </div>

            <div class="col-12">
                <SeccionesAtencionEM v-if="cabecera" :cabecera="cabecera" :viewRecord="isViewMode"
                    :IdTipoServicio="IdTipoServicio" />
            </div>
        </div>

    </BaseModal>
</template>
