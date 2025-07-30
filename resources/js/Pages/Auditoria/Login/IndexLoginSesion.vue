<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ModalFormLoginSesion from './FormLoginSesion.vue'
import BaseDataTable from '@/components/BaseDataTable.vue'
import { ref } from 'vue'

// Recurso para la tabla
let resource = ref('auditoria/login')
let refTable = ref()
let refDialogForm = ref()

// Control de estado del modal
let recordId = ref(null);
let viewRecord = ref(false);
 

const clickView = (id) => { 
    recordId.value = id
    viewRecord.value = true
    refDialogForm.value.openDialog()
}
 

const successActions = (data) => {
    if (data.action === 'view') {
        clickView(data.id)
    } 
}
</script>

<template>
    <AppLayout>
        <BaseDataTable ref="refTable" :resource="resource" @actions="successActions" />

        <ModalFormLoginSesion ref="refDialogForm" :record-id="recordId" :resource="resource" :view-record="viewRecord" />
    </AppLayout>
</template>
