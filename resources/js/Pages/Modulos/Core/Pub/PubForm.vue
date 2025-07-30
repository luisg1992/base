<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import WCheckbox from "@/components/WCheckbox/WCheckbox.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
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
let publicidadTipos = ref([]);

const initTables = async () => {
    const {data} = await axios.get(`/${props.resource}/init_tables`)
    publicidadTipos.value = data.publicidadTipos;
}

const initForm = () => {
    errors.value = {};
    form.value = {
        IdPublicidadTipo: 2,
        Titulo: null,
        Descripcion: null,
        TamanoLetra: 36,
        PosicionVertical: true,
        Estado: 1
    };
}

const handleOpen = async () => {
    isModalLoading.value = true;
    showModal.value = false;
    initForm();
    await initTables();
    title.value = "Registrar publicidad";
    if (props.recordId) {
        try {
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form.value, data.data)
            title.value = "Editar publicidad";
        } catch (error) {
            if (error.status === 403) {
                closeDialog();
            }
        } finally {
            showModal.value = true;
            isModalLoading.value = false;
        }
    } else {
        isModalLoading.value = false;
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
            <div class="col-md-4">
                <BaseCombo v-model="form.IdPublicidadTipo"
                           label="Tipo de publicidad"
                           :show-clear="false"
                           :options="publicidadTipos"
                           :disabled="isViewMode"
                           :error="errors.IdPublicidadTipo"/>
            </div>
            <div class="col-md-12">
                <BaseInput v-model="form.Descripcion"
                           label="Descripción"
                           :disabled="isViewMode"
                           :error="errors.Descripcion"/>
            </div>
<!--            <div class="col-md-6">-->
<!--                <BaseInput v-model="form.TamanoLetra"-->
<!--                           label="Tamaño de letra en pixeles"-->
<!--                           :disabled="isViewMode"-->
<!--                           :error="errors.TamanoLetra"/>-->
<!--            </div>-->
            <div class="col-md-12">
                <w-checkbox v-model="form.PosicionVertical"
                            :disabled="isViewMode"
                            label="¿Posición vertical?">
                </w-checkbox>
            </div>
            <div class="col-md-12">
                <w-checkbox v-model="form.Estado"
                            :disabled="isViewMode"
                            label="¿Activo?">
                </w-checkbox>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4 gap-2">
            <w-button type="secondary"
                     label="Cerrar"
                     text
                     @click="closeDialog"
                     :disabled="loadingSubmit"/>
            <w-button :label="`${loadingSubmit?'Guardando...' : 'Guardar'}`"
                     type-button="primary"
                     @click="onSubmit"
                     v-if="!isViewMode"
                     :disabled="loadingSubmit"/>
        </div>
    </BaseModal>
</template>
