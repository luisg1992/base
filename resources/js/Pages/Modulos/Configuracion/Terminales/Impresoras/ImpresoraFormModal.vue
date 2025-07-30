<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "../../../../../components/WInput/WInput.vue";
import WCheckbox from "../../../../../components/WCheckbox/WCheckbox.vue";
import BaseCombo from "../../../../../components/WSelect/WSelect.vue";
import WButton from "../../../../../components/WButton/WButton.vue";

const props = defineProps({
    recordId: Number,
    terminalId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let isDialogOpenFormularioImpresoras = ref(false);
let loadingSubmit = ref(false)
let isModalLoading = ref(false)
let showModal = ref(false);


let isViewMode = computed(() => props.viewRecord)
let errors = ref({});
const form = useForm({
    IdTerminales: null,
    Nombre: '',
    Formato: '',
    Estado: 1,
    PorDefecto: 0
})
// Guardar

const guardar = async () => {
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
            IdTerminales: props.terminalId,
            Nombre: form.Nombre,
            Formato: form.Formato,
            Estado: form.Estado,
            PorDefecto: form.PorDefecto
        };

        axios.post(`/${props.resource}`, formData)
            .then(response => {
                loadingSubmit.value = false;
                emit('success');
                isDialogOpenFormularioImpresoras.value = false;

                const accion = props.recordId ? 'ACTUALIZADO' : 'REALIZADO';
                const mensaje = `EL REGISTRO FUE ${accion} DE FORMA EXITOSA`;
                showAlert('PROCESO REALIZADO EXITOSAMENTE', mensaje, 'success');
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
            console.error('Error al obtener detalles:', error)
        } finally {
            showModal.value = true;
            isModalLoading.value = false;
        }
    } else {
        showModal.value = true;
    }
}

const openDialogFormularioImpresoras = () => {
    isDialogOpenFormularioImpresoras.value = true;
}

const tipoOptions = [
    { label: 'TICKET', value: 'ticket' },
    { label: 'A4', value: 'A4' },
    { label: 'A5', value: 'A5' }
];

defineExpose({ openDialogFormularioImpresoras })
</script>

<template>
    <BaseModal :isVisible="isDialogOpenFormularioImpresoras" :recordId="form.id" :viewRecord="isViewMode"
        :loadingSubmit="loadingSubmit" @close="isDialogOpenFormularioImpresoras = false" @submit="guardar" @open="handleOpen"
        size="modal-sm">

        <ModalLoader v-if="isModalLoading" />

        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.Nombre" label="Nombre" placeholder="Nombre" :disabled="isViewMode"
                    :error="errors.Nombre" />
            </div>

            <!-- Combo de PrimeVue -->
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.Formato" :options="tipoOptions" optionLabel="label"
                    optionValue="value" label="Tipo" :disabled="isViewMode">
                    <template #option="{ option }">
                        <div class="flex flex-column">
                            <div>{{ option.label }}</div>
                        </div>
                    </template>
                </BaseCombo>
            </div>
            <!-- Formato -->
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <w-checkbox v-model="form.PorDefecto"
                            :disabled="isViewMode"
                            label="¿Impresora Por defecto?">
                </w-checkbox>
            </div>

            <div v-if="!isViewMode" class="d-flex justify-content-end mt-4">
                <w-button type="primary"
                         :label="`${loadingSubmit ? 'Guardando...' : 'Guardar'}`"
                         @click="guardar"/>
            </div>
        </div>
    </BaseModal>
</template>
