<script setup>
import ModalFormulario from './ParametroForm.vue';
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import {ref} from 'vue';
import {useAppStore} from '@/stores/useAppStore';

let appStore = useAppStore();
const resource = ref('core/parametros');
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
    appStore.fetchTabla('coreParametros');
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
        <BaseDataTable ref="refTable"
                       :resource="resource"
                       @actions="successActions"
                       @success-delete="successAction"
                       @success-active="successAction"/>

        <ModalFormulario ref="refDialogForm"
                         :record-id="recordId"
                         :resource="resource"
                         :view-record="viewRecord"
                         @success="successAction"/>
</template>
