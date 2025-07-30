<script setup>
import {ref, computed, onMounted} from 'vue'
import axios from 'axios'
import {useForm} from '@inertiajs/vue3'

import BaseModal from '@/components/BaseModal.vue';
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import WCheckbox from "@/components/WCheckbox/WCheckbox.vue";

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

const modulosPadre = ref([])
const cargandoModulos = ref(false)

let isViewMode = computed(() => props.viewRecord)
let errors = ref({});
const form = useForm({
    ModuloPadreId: null,
    Etiqueta: '',
    Subtitulo: '',
    Descripcion: '',
    Icono: '',
    Url: '',
    EsAccesoDirecto: 0,
    EstaBloqueado: 0,
    Estado: 1,
    Orden: 1
})

const cargarTablas = async () => {
    cargandoModulos.value = true
    try {
        const {data} = await axios.get(`/${props.resource}/lista-tablas`)
        modulosPadre.value = data.modulos
    } catch (error) {
        console.error('Error al cargar módulos padre:', error)
    } finally {
        cargandoModulos.value = false
    }
}

const guardar = async () => {
    const accionConfirmacion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PAR EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensajeConfirmacion = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accionConfirmacion}?`;
    const confirmado = await showAlertConfirmacion(
        'VALIDACIÓN DE DATOS REALIZADOS', mensajeConfirmacion,
        'warning'
    );

    if (confirmado) {
        loadingSubmit.value = true;

        const formData = {
            id: props.recordId ?? null,
            Etiqueta: form.Etiqueta,
            Subtitulo: form.Subtitulo,
            Descripcion: form.Descripcion,
            Icono: form.Icono,
            Url: form.Url,
            EsAccesoDirecto: form.EsAccesoDirecto ? 1 : 0,
            EstaBloqueado: form.EstaBloqueado ? 1 : 0,
            Estado: form.Estado ? 1 : 0,
            Orden: form.Orden,
            ModuloPadreId: form.ModuloPadreId
        };

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
            <div class="row">
                <div class="col-md-12">
                    <BaseCombo v-model="form.ModuloPadreId" :options="modulosPadre" optionLabel="Etiqueta"
                               optionValue="ModuloId" label="Módulo Padre" :disabled="isViewMode" :filter="true">
                        <template #option="{ option }">
                            <div class="flex flex-column">
                                <div>{{ option.Etiqueta }}</div>
                                <div class="text-truncate" style="max-width: 300px; font-weight: 400">
                                    {{ option.Descripcion }}
                                </div>
                            </div>
                        </template>
                    </BaseCombo>
                </div>

                <!-- Campos -->
                <div class="col-md-12">
                    <BaseInput v-model="form.Etiqueta" label="Título" placeholder="Ingrese Etiqueta"
                               :disabled="isViewMode" :error="errors.Etiqueta"/>
                </div>

                <div class="col-md-12">
                    <BaseInput v-model="form.Subtitulo" label="Subtítulo" placeholder="Ingrese subtítulo"
                               :disabled="isViewMode" :error="errors.Subtitulo"/>
                </div>

                <div class="col-md-12">
                    <BaseInput v-model="form.Descripcion" label="Descripción" placeholder="Ingrese descripción"
                               :disabled="isViewMode" :error="errors.Descripcion"/>
                </div>

                <div class="col-md-8">
                    <BaseInput v-model="form.Icono" label="Ícono" placeholder="Ingrese ícono" :disabled="isViewMode"
                               :error="errors.Icono"/>
                </div>

                <div class="col-md-4">
                    <BaseInput v-model="form.Orden" label="Orden" type="number" placeholder="Ingrese Orden"
                               :disabled="isViewMode" :error="errors.Orden"/>
                </div>

                <div class="col-md-12">
                    <BaseInput v-model="form.Url" label="Url" placeholder="Ingrese la Url" :disabled="isViewMode"
                               :error="errors.Url"/>
                </div>

                <div class="col-md-4 form-check mt-2">
                    <w-checkbox v-model="form.EsAccesoDirecto"
                                :disabled="isViewMode"
                                label="¿Es acceso directo?">
                    </w-checkbox>
                </div>

                <div class="col-md-4 form-check mt-2">
                    <x-checkbox v-model="form.EstaBloqueado"
                                  :disabled="isViewMode"
                                  label="¿Está bloqueado?">

                    </x-checkbox>
                </div>

                <div class="col-md-4 form-check mt-2">
                    <BaseCheckbox v-model="form.Estado" :disabled="isViewMode">
                        ¿Está activo?
                    </BaseCheckbox>
                </div>
            </div>
            <div v-if="!isViewMode" class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary" :disabled="loadingSubmit">
                    {{ loadingSubmit ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
    </BaseModal>
</template>
