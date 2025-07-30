<script setup>
import ModalFormulario from './CartasGarantiaForm.vue';
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import {ref} from 'vue';

const resource = ref('facturacion/cartas-garantia');
const refTable = ref();
const refDialogForm = ref();
const recordId = ref(null);
const viewRecord = ref(false);

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

const successAction = () => {
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
    <BaseDataTable ref="refTable" :resource="resource" @actions="successActions" @success-delete="successAction"
                   @success-active="successAction"/>

    <ModalFormulario ref="refDialogForm" :record-id="recordId" :resource="resource" :view-record="viewRecord"
                     @success="successAction"/>
</template>
