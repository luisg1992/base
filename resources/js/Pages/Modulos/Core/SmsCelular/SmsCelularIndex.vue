<script setup>
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import {ref} from 'vue';
import {useAppStore} from '@/stores/useAppStore';
import SmsCelularForm from "./SmsCelularForm.vue";
import axios from "axios";
import ModalLoader from "@/components/ModalLoader.vue";

let appStore = useAppStore();
const resource = ref('core/sms-celulares');
const refTable = ref();
const refDialogForm = ref();
const loadingSend = ref(false);
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

const successActions = async (data) => {
    if (data.action === 'view') {
        clickView(data.id)
    }
    if (data.action === 'edit') {
        clickCreate(data.id)
    }
    if (data.action === 'new') {
        clickCreate()
    }
    if (data.action === 'enviar_sms') {
        loadingSend.value = true;
        await axios.get(`/${resource.value}/enviar-sms/${data.id}`)
            .then(response => {
                let data = response.data;
                if (data.success) {
                    showToastSuccess(data.message);
                } else {
                    showToastError(data.message);
                }
            })
            .finally(() => {
                loadingSend.value = false;
            })
    }
}
</script>

<template>
    <div>
        <BaseDataTable ref="refTable"
                       :resource="resource"
                       @actions="successActions"
                       @success-delete="successAction"
                       @success-active="successAction"/>

        <sms-celular-form ref="refDialogForm"
                          :record-id="recordId"
                          :resource="resource"
                          :view-record="viewRecord"
                          @success="successAction"/>
        <ModalLoader v-if="loadingSend" text-loading="Enviando SMS..."/>
    </div>
</template>
