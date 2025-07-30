<script setup>
import {ref} from 'vue';
import axios from 'axios';

import BaseModal from '@/components/BaseModal.vue';
import BaseInput from "@/components/WInput/WInput.vue";
import WCheckbox from "@/components/WCheckbox/WCheckbox.vue";
import WButton from "@/components/WButton/WButton.vue";
import BaseButton from "@/components/BaseButton.vue";
import {showToastSuccess} from "../../../../utils/alert.js";

const props = defineProps(['parentId', 'recordId']);

const emit = defineEmits(['close', 'success']);

const resource = 'core/modulos';

let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let title = ref();
let errors = ref({});
let formParent = ref({});
let form = ref({});

const initForm = () => {
    errors.value = {};
    formParent.value = {
        ModuloPadreId: null,
        Etiqueta: null,
        Subtitulo: null,
        Descripcion: null,
        Icono: null,
        Url: null,
        EsAccesoDirecto: 0,
        EstaBloqueado: 0,
        Estado: 1,
    };
    form.value = {
        ModuloPadreId: props.parentId,
        Etiqueta: null,
        Subtitulo: null,
        Descripcion: null,
        Icono: null,
        Url: null,
        EsAccesoDirecto: 0,
        EstaBloqueado: 0,
        Estado: 1,
        acciones: [
            {Nombre: 'Acceder', Valor: 'acceder', Seleccionado: true},
            {Nombre: 'Crear', Valor: 'crear', Seleccionado: true},
            {Nombre: 'Editar', Valor: 'editar', Seleccionado: true},
            {Nombre: 'Eliminar', Valor: 'eliminar', Seleccionado: true},
            {Nombre: 'Visualizar', Valor: 'visualizar', Seleccionado: true},
            {Nombre: 'Cambiar estado', Valor: 'cambiar.estado', Seleccionado: true},
        ]
    }
}

const handleOpen = async () => {
    initForm();
    isModalLoading.value = true;
    title.value = 'Nuevo módulo';

    try {
        if (props.parentId) {
            const {data} = await axios.get(`/${resource}/record/${props.parentId}`);
            Object.assign(formParent.value, data.data);
        }

        if (props.recordId) {
            title.value = 'Editar módulo';
            const {data} = await axios.get(`/${resource}/record/${props.recordId}`);
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
        await axios.post(`/${resource}`, form.value)
            .then(response => {
                const data = response.data;
                if (data.success) {
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
            <div class="col-md-12" v-if="parentId">
                <BaseInput v-model="formParent.Etiqueta"
                           disabled
                           label="Módulo Padre"/>
            </div>
            <div class="col-md-12">
                <BaseInput v-model="form.Etiqueta"
                           label="Título"
                           :error="errors.Etiqueta"/>
            </div>
            <div class="col-md-12">
                <BaseInput v-model="form.Subtitulo"
                           label="Subtítulo"
                           :error="errors.Subtitulo"/>
            </div>
            <div class="col-md-12">
                <BaseInput v-model="form.Descripcion"
                           label="Descripción"
                           :error="errors.Descripcion"/>
            </div>
            <div class="col-md-4">
                <BaseInput v-model="form.Icono"
                           label="Ícono"
                           :error="errors.Icono"/>
            </div>
            <div class="col-md-8">
                <BaseInput v-model="form.Url"
                           label="Url"
                           :error="errors.Url"/>
            </div>
            <div class="col-md-4 form-check mt-2">
                <w-checkbox v-model="form.EsAccesoDirecto"
                            label="¿Es acceso directo?">
                </w-checkbox>
            </div>

            <div class="col-md-4 form-check mt-2">
                <w-checkbox v-model="form.EstaBloqueado"
                            label="¿Está bloqueado?">
                </w-checkbox>
            </div>

            <div class="col-md-4 form-check mt-2">
                <w-checkbox v-model="form.Estado"
                            label="¿Está activo?">
                </w-checkbox>
            </div>

            <template v-if="form.acciones && !props.recordId" v-for="accion in form.acciones">
                <div class="col-md-4 form-check mt-2">
                    <w-checkbox v-model="form.Seleccionado"
                                :label="accion.Nombre">
                    </w-checkbox>
                </div>
            </template>
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
