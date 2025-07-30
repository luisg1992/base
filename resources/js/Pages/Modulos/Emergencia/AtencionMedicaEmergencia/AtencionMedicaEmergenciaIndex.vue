<script setup>
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import { ref } from 'vue';
import AtencionMedicaEM from '../../AtencionMedica/AtencionMedicaEM.vue';

const resource = ref('emergencia/atencion-medica-emergencia');
const refTable = ref();
const refDialogForm = ref();
const recordId = ref(null);
const viewRecord = ref(false);
const IdTipoServicio = ref(2)

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
    refTable.value.listarRegistros();
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

    <AtencionMedicaEM ref="refDialogForm" :record-id="recordId" :resource="resource" :view-record="viewRecord"
        :IdTipoServicio="IdTipoServicio" @success="successAction" />
</template>
