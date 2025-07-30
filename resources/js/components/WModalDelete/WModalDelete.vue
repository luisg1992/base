<script setup>
import { ref } from 'vue'
import axios from 'axios'

import Dialog from 'primevue/dialog'
import Password from 'primevue/password'
import WButton from '@/components/WButton/WButton.vue'
import ModalLoader from '@/components/ModalLoader.vue'
import {showToastWarning} from "../../utils/alert.js";

const props = defineProps({ resource: String })
const emit = defineEmits(['success'])

const isDialogOpen = ref(false)
const loading = ref(false)
const loadingSubmit = ref(false)
const title = ref('')
const errors = ref({})
const form = ref({})
const localRecordId = ref(null)

// Inicializa el formulario
function initForm() {
    title.value = ''
    errors.value = {}
    form.value = {
        id: localRecordId.value,
        password: '',
        verify_password: false
    }
}

// Abrir modal y cargar datos
async function openDialog(recordId) {
    if (!recordId) {
        showAlert('ERROR', 'No se puede abrir el modal sin un recordId válido', 'error')
        return
    }

    localRecordId.value = recordId
    isDialogOpen.value = true
    initForm()

    loading.value = true;
    try {
        const { data } = await axios.get(`/${props.resource}/record_destroy/${recordId}`)
        title.value = data.title
        form.value.verify_password = data.verify_password
    } catch (e) {
        if (e?.response?.status === 403) {

        } else {
            showToastError('No se pudo cargar el formulario')
        }
        closeDialog()
    } finally {
        loading.value = false;
    }
}

// Cierra el modal
function closeDialog() {
    isDialogOpen.value = false
}

// Enviar formulario
async function onSubmit() {
    if (form.value.verify_password && !form.value.password) {
        showToastWarning('El campo contraseña es requerido.', {duration: 5})
        return
    }

    loading.value = true;
    loadingSubmit.value = true;
    try {
        const { data } = await axios.post(`/${props.resource}/destroy`, form.value)
        if (data.success) {
            showToastSuccess(data.mensaje)
            emit('success', data.data)
            closeDialog()
        } else {
            showToastWarning(data.mensaje)
        }
    } catch (err) {
        errors.value = err?.response?.data?.errors || {}
        showToastError('Hubo un error al procesar la solicitud')
    } finally {
        loadingSubmit.value = false;
        loading.value = false;
    }
}

defineExpose({ openDialog })
</script>

<template>
    <Dialog v-model:visible="isDialogOpen" modal :closable="false" :style="{ width: '480px' }">
        <div class="b-dialog-delete">
            <div class="left">
                <div class="icon">
                    <i class="ti ti-alert-triangle" style="font-size: 36px; color: #E7000B" />
                </div>
            </div>
            <div class="right">
                <div class="title">Eliminar registro</div>
                <div class="description">
                    ¿Seguro que desea eliminar el registro? Todos sus datos se eliminarán permanentemente de nuestros servidores.
                    Esta acción no se puede deshacer.
                </div>
                <div v-if="form.verify_password" class="verify-password">
                    <label class="verify-password-label" for="password">Ingresar contraseña</label>
                    <Password
                        id="password"
                        v-model="form.password"
                        :feedback="false"
                        toggleMask
                        class="verify-password-input"
                        :class="{ 'p-invalid': errors.password }"
                    />
                    <small v-if="errors.password" class="p-error">{{ errors.password }}</small>
                </div>
            </div>
        </div>
        <template #footer>
            <w-button
                type="secondary"
                label="No, cancelar"
                text
                @click="closeDialog"
                :disabled="loadingSubmit"
            />
            <w-button
                type="danger"
                label="Sí, estoy seguro"
                @click="onSubmit"
                :disabled="loadingSubmit"
            />
        </template>
        <ModalLoader v-if="loading" />
    </Dialog>
</template>
