<script setup>
import ModalFormulario from './FormularioPermission.vue'
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import {ref} from 'vue'

// Recurso para la tabla
let resource = ref('seguridad/permisos')
let refTable = ref()
let refDialogForm = ref()

// Control de estado del modal
let recordId = ref(null);
let viewRecord = ref(false);

const clickCreate = (id = null) => {
    recordId.value = id
    viewRecord.value = false
    refDialogForm.value.openDialog()
}

const clickView = (id) => {
    recordId.value = id
    viewRecord.value = true
    refDialogForm.value.openDialog()
}

const recargarLista = () => {
    refTable.value.listarRegistros()
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
