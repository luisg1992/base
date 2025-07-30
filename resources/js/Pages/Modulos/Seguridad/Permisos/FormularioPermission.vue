<script setup>
import {ref, computed, onMounted} from 'vue'
import axios from 'axios'
import {useForm} from '@inertiajs/vue3'

import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";

// Props y emits
const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

// Refs
let modulosPadre = ref([]);
let cargandoModulos = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let isDialogOpen = ref(false);

// Computed
let isViewMode = computed(() => props.viewRecord)
let errors = ref({});
const form = useForm({
    modulo_id: 0,
    name: '',
    guard_name: 'web',
    descripcion: '',
    tipo: 'Link'
})

// Métodos
const cargarTablas = async () => {
    cargandoModulos.value = true
    try {
        const {data} = await axios.get(`/${props.resource}/listar-tablas`)
        modulosPadre.value = data.modulos_permisos
    } catch (error) {
        console.error('Error al cargar módulos padre:', error)
    } finally {
        cargandoModulos.value = false
    }
}

const guardar = async () => {
    const accion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PARA EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accion}?`;
    const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');

    if (!confirmado) return;

    loadingSubmit.value = true

    const formData = {
        id: props.recordId ?? null,
        name: form.name,
        guard_name: form.guard_name,
        modulo_id: form.modulo_id,
        descripcion: form.descripcion,
        tipo: form.tipo
    }

    await axios.post(`/${props.resource}`, formData)
        .then(response => {
            loadingSubmit.value = false;
            emit('success');
            isDialogOpen.value = false;

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

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

onMounted(() => {
    cargarTablas()
})

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
        <form @submit.prevent="guardar">
            <div class="row">
                <!-- Campos -->
                <div class="col-md-12">
                    <BaseCombo v-model="form.modulo_id" :options="modulosPadre" optionLabel="Etiqueta"
                               optionValue="ModuloId" label="Módulo Padre" :disabled="isViewMode" filter>
                        <template #option="{ option }">
                            <div class="flex flex-column">
                                <div>{{ option.Etiqueta }}</div>
                                <div class="text-truncate" style="max-width: 300px; font-weight: 400">
                                    {{ option.descripcion }}
                                </div>
                            </div>
                        </template>
                    </BaseCombo>
                </div>

                <div class="col-md-12">
                    <BaseInput v-model="form.name"
                               label="Nombre del Permiso"
                               placeholder="Ingrese Nombre del Permiso"
                               :disabled="isViewMode"
                               :error="errors.name"/>
                </div>

                <div class="col-md-12">
                    <BaseInput v-model="form.descripcion"
                               label="Descripción Detallada"
                               placeholder="Ingrese Descripción del Permiso"
                               :disabled="isViewMode"
                               :error="errors.descripcion"/>
                </div>

                <div class="col-md-12">
                    <BaseCombo v-model="form.tipo"
                               label="Tipo de permiso"
                               :disabled="isViewMode"
                               :options="[{'value': 'link', 'label':'Link'}, {'value': 'action', 'label':'Acción'}]"/>
                </div>
            </div>

            <div v-if="!isViewMode" class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary" :disabled="loadingSubmit">
                    {{ loadingSubmit ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </BaseModal>
</template>
