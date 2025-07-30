<script setup>

import {ref} from "vue";
import {router as inertiaRouter} from '@inertiajs/vue3';
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import AppLayout from "../../../layouts/AppLayout.vue";
import NotaIngresoAlmacenForm from "./NotaIngresoAlmacenForm.vue";

const refStoreTableServer = ref();
const refDialogForm = ref();
let resource = 'farmacia/notas_ingresos_almacen';
let recordId = ref(null);
let viewRecord = ref(false);

const clickCreate = (id = null) => {
    recordId.value = id;
    viewRecord.value = false;
    refDialogForm.value.openDialog();
}

const clickView = (id) => {
    recordId.value = id;
    viewRecord.value = true;
    refDialogForm.value.openDialog();
}

const successCreate = () => {
    refStoreTableServer.value.listarRegistros();
}

const successActions = (data) => {
    if (data.action === 'view') {
        clickView(data.id);
    }
    if (data.action === 'edit') {
        clickCreate(data.id);
    }
    if (data.action === 'new') {
        if (data.url) {
            inertiaRouter.visit(`/${resource}/${data.url}`);
        } else {
            clickCreate();
        }
    }
}
</script>

<template>
    <app-layout>
        <base-data-table ref="refStoreTableServer"
                         :resource="resource"
                         @actions="successActions"/>

        <nota-ingreso-almacen-form ref="refDialogForm"
                                   :record-id="recordId"
                                   :resource="resource"
                                   :view-record="viewRecord"
                                   @success="successCreate"></nota-ingreso-almacen-form>
    </app-layout>
</template>
