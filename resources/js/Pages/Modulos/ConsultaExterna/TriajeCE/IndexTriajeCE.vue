<script setup>
import ModalFormulario from './FormTriajeCE.vue';
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import { ref } from 'vue'
//import AtencionMedica from '../../AtencionMedica/AtencionMedica.vue'

// Recurso para la tabla
const resource = ref('consulta-externa/triaje-ce')
const refTable = ref()
const refDialogForm = ref()

// Control de estado del modal
const recordId = ref(null)
const viewRecord = ref(false)
const modalVisible = ref(false)
const IdTipoServicio =ref(2)

const clickCreate = (id = null) => {
    recordId.value = id
    viewRecord.value = false
    modalVisible.value = true
    refDialogForm.value.openDialog()
}

const clickView = (id) => {
    recordId.value = id
    viewRecord.value = true
    modalVisible.value = true
    refDialogForm.value.openDialog()
}

const cerrarModal = () => {
    modalVisible.value = false
}

const successAction = () => {
    refTable.value.listarRegistros();
    cerrarModal();
}

const successActions = (data) => {
    if (data.action === 'view') {
        clickView(data.id)
    }
    if (data.action === 'edit') {
        clickCreate(data.id)
    }
    if (data.action === 'new') {
        clickCreate()
    }
}
</script>

<template>
    <BaseDataTable ref="refTable" :resource="resource" @actions="successActions" @success-delete="successAction"
        @success-active="successAction" />

    <ModalFormulario ref="refDialogForm" :record-id="recordId" :resource="resource" :view-record="viewRecord"
        @success="successAction"/>
</template>
