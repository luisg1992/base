<script setup>

import {ref} from "vue";
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import {router as inertiaRouter} from '@inertiajs/vue3';
import NotaSalidaAlmacenForm from "./NotaSalidaAlmacenForm.vue";

const refStoreTableServer = ref();
const refDialogForm = ref();
let resource = 'farmacia/notas_salidas_almacen';
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
    <base-data-table ref="refStoreTableServer"
                     :resource="resource"
                     @actions="successActions"/>

    <nota-salida-almacen-form ref="refDialogForm"
                              :record-id="recordId"
                              :resource="resource"
                              :view-record="viewRecord"
                              @success="successCreate"></nota-salida-almacen-form>
</template>
