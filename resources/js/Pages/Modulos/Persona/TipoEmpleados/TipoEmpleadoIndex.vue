<script setup>
import ModalFormulario from './TipoEmpleadoForm.vue'
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import {ref} from 'vue'

// Recurso para la tabla
const resource = ref('personas/tipo-empleados')
const refTable = ref()
const refDialogForm = ref()

// Control de estado del modal
const recordId = ref(null)
const viewRecord = ref(false)
const modalVisible = ref(false)

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

const recargarLista = () => {
    refTable.value.listarRegistros()
    cerrarModal()
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
    <BaseDataTable ref="refTable" :resource="resource" @actions="successActions"/>

    <ModalFormulario ref="refDialogForm" :record-id="recordId" :resource="resource" :view-record="viewRecord"
                     @success="recargarLista"/>
</template>
