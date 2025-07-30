<script setup>
import axios from 'axios'
import {ref} from 'vue'

import ImpresoraFormModal from './ImpresoraFormModal.vue'
import BaseModal from '@/components/BaseModal.vue'
import ModalLoader from '@/components/ModalLoader.vue'
import WButton from "../../../../../components/WButton/WButton.vue";

const props = defineProps({
    recordId: Number,
    resource: String,
    viewRecord: Boolean
})

const emit = defineEmits(['close', 'success'])

const viewRecord = ref(false)
const recordId = ref(null)
const isDialogOpenListadoImpresoras = ref(false)
const isModalLoading = ref(false)
const impresoras = ref([])


const showFormModal = ref()
const refDialogDeleteForm = ref()
const refDialogActiveForm = ref()
const recordDeleteId = ref(null)
const recordActiveId = ref(null)


const openDialogListadoImpresoras = () => {
    isDialogOpenListadoImpresoras.value = true
}

const handleOpen = async () => {
    await cargarImpresoras()
}

const cargarImpresoras = async () => {
    isModalLoading.value = true
    try {
        const response = await axios.post(`/${props.resource}/lista-tablas`, {IdTerminales: props.recordId})
        impresoras.value = response.data?.terminales
    } catch (error) {
        console.error('Error al cargar impresoras:', error)
    } finally {
        isModalLoading.value = false
    }
}

const abrirFormulario = (modo, impresora) => {
    if (modo == 'create') {
        viewRecord.value = false
        recordId.value = null
        showFormModal.value.openDialogFormularioImpresoras()
    }
    if (modo == 'edit') {
        viewRecord.value = false
        recordId.value = impresora.ImpresorasId
        showFormModal.value.openDialogFormularioImpresoras()
    }
    if (modo == 'view') {
        viewRecord.value = true
        recordId.value = impresora.ImpresorasId
        showFormModal.value.openDialogFormularioImpresoras()
    }
    if (modo == 'delete') {
        recordId.value = impresora.ImpresorasId
        recordDeleteId.value = impresora.ImpresorasId
        refDialogDeleteForm.value.openDialog(recordDeleteId.value)
    }
    if (modo == 'cambioEstado') {
        recordId.value = impresora.ImpresorasId
        recordActiveId.value = impresora.ImpresorasId
        refDialogActiveForm.value.openDialog(recordActiveId.value)
    }
}

const cambiarEstado = async (impresora) => {
    try {
        const responseConfirm = await axios.get(`/${props.resource}/record_active/${impresora.ImpresorasId}`)
        const confirmMsg = responseConfirm.data?.title || '¿Cambiar estado?'

        if (!confirm(confirmMsg)) return

        await axios.post(`/${props.resource}/change_active`, {id: impresora.ImpresorasId})
        await cargarImpresoras()
    } catch (error) {
        console.error('Error al cambiar estado:', error)
    }
}

defineExpose({openDialogListadoImpresoras})
</script>

<template>
    <BaseModal :isVisible="isDialogOpenListadoImpresoras"
               :recordId="props.recordId"
               size="modal-sm"
               @close="isDialogOpenListadoImpresoras = false"
               title="Lista de Impresoras"
               @open="handleOpen">

        <ModalLoader v-if="isModalLoading"/>

        <template v-else>
            <div class="d-flex justify-content-end">
                <w-button type="primary"
                         label="Nueva impresora"
                         @click="abrirFormulario('create', null)"/>
            </div>
            <ul class="list-group mt-3"
                v-if="impresoras.length > 0"
                style="font-size: 12px !important;">
                <li class="list-group-item d-flex justify-content-between align-items-center"
                    v-for="impresora in impresoras" :key="impresora.ImpresorasId">
                    <div>
                        {{ impresora.Nombre }} <b>(Formato: {{ impresora.Formato }})</b>
                        <span v-if="impresora.PorDefecto === 1" class="badge bg-label-success ms-2">POR
                                DEFECTO</span>
                        <span v-if="impresora.Estado === 0" class="badge bg-label-danger ms-2">INACTIVO</span>
                    </div>
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-fixed">
                            <a href="javascript:void(0);" class="dropdown-item"
                               @click="abrirFormulario('view', impresora)">
                                <i class="ti ti-eye me-1"></i>VER
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item"
                               @click="abrirFormulario('edit', impresora)">
                                <i class="ti ti-pencil me-1"></i>EDITAR
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item"
                               @click="abrirFormulario('delete', impresora)">
                                <i class="ti ti-trash me-1"></i>ELIMINAR
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item"
                               @click="abrirFormulario('cambioEstado', impresora)">
                                <i class="ti ti-reload me-1"></i>CAMBIAR ESTADO
                            </a>
                            <!-- <a href="javascript:void(0);" class="dropdown-item" @click="cambiarEstado(impresora)">
                                <i class="ti ti-reload me-1"></i> CAMBIAR ESTADO
                            </a> -->
                        </div>
                    </div>
                </li>
            </ul>
            <div v-else class="alert alert-info mt-3 mb-0">No se encontraron impresoras para esta terminal.</div>
        </template>
    </BaseModal>

    <ImpresoraFormModal ref="showFormModal" :record-id="recordId" :terminalId="props.recordId"
                        :resource="props.resource" @success="cargarImpresoras" :view-record="viewRecord"/>


    <!--    &lt;!&ndash; Componente de eliminación &ndash;&gt;-->
    <!--    <BaseModalDelete ref="refDialogDeleteForm" @success="cargarImpresoras" :resource="props.resource"/>-->
    <!--    &lt;!&ndash; Componente de cambio de estado &ndash;&gt;-->
    <!--    <BaseModalEstado ref="refDialogActiveForm" @success="cargarImpresoras" :resource="props.resource"/>-->
</template>
