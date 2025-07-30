<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'
import {useForm} from '@inertiajs/vue3'

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
const form = useForm({
    Descripcion: '',
    sip2000: '',
    codigoReniec: '',
    IdTipoDIRIS: '',
    Estado: 1
})

const handleOpen = async () => {
    errors.value = {};
    form.reset()
    form.clearErrors()

    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form, data.data)

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

    // Si el usuario confirma, procedemos con el guardado
    if (confirmado) {
        loadingSubmit.value = true;

        const formData = {
            id: props.recordId ?? null,
            Descripcion: form.Descripcion,
            sip2000: form.sip2000,
            codigoReniec: form.codigoReniec,
            IdTipoDIRIS: form.IdTipoDIRIS,
            Estado: form.Estado
        };

        await axios.post(`/${props.resource}`, formData)
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
            <div class="col-6 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.Descripcion" label="Descripcion" placeholder="Ingrese una descripción"
                           :disabled="isViewMode" :error="errors.Descripcion"/>
            </div>
            <div class="col-6 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.sip2000" label="sip2000" placeholder="Ingrese una descripción"
                           :disabled="isViewMode" :error="errors.sip2000"/>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.codigoReniec" label="codigoReniec" placeholder="Ingrese el codigo reniec"
                           :disabled="isViewMode" :error="errors.codigoReniec"/>
            </div>
            <div class="col-6 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.IdTipoDIRIS" label="IdTipoDIRIS" placeholder="Ingrese un Tipo Diris"
                           :disabled="isViewMode" :error="errors.IdTipoDIRIS"/>
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
