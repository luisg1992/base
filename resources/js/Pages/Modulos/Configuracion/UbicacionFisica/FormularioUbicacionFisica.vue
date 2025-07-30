<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'
import {useForm} from '@inertiajs/vue3'

import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import {useAppStore} from '@/stores/useAppStore'
import WButton from "@/components/WButton/WButton.vue";
import {showToastSuccess} from "../../../../utils/alert.js";

let appStore = useAppStore();
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
    Nombre: '',
    Estado: 1,
    TipoUbicacionFisica: '',
    IdEspecialidadPrimaria: null
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
            Nombre: form.Nombre,
            Estado: form.Estado,
            IdEspecialidadPrimaria: form.IdEspecialidadPrimaria,
            TipoUbicacionFisica: form.TipoUbicacionFisica,
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

const tipoOptions = [
    {label: 'CONSULTORIO', value: 'CONSULTORIO'},
    {label: 'PROCEDIMIENTO', value: 'PROCEDIMIENTO'},
    {label: 'TELEMEDICINA', value: 'TELEMEDICINA'}
];

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
            <div class="col-md-12">
                <BaseCombo v-model="form.IdEspecialidadPrimaria"
                           :options="appStore.configuracionEspecialidadesPrimarias"
                           label="Epecialidades Primarias" :disabled="isViewMode" :filter="true"/>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.Nombre" label="Descripción" placeholder="Ingrese una descripción"
                           :disabled="isViewMode" :error="errors.Nombre"/>
            </div>

            <!-- Combo de PrimeVue -->
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.TipoUbicacionFisica" :options="tipoOptions" optionLabel="label"
                           optionValue="value"
                           label="Tipo" :disabled="isViewMode">
                    <template #option="{ option }">
                        <div class="flex flex-column">
                            <div>{{ option.label }}</div>
                        </div>
                    </template>
                </BaseCombo>
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
