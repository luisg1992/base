<script setup>
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import {ref} from 'vue';
import {useAppStore} from '@/stores/useAppStore';
import SetisisForm from "./SetisisForm.vue";
import axios from "axios";
import SetisisConsulta from "./SetisisConsulta.vue";

let appStore = useAppStore();
const resource = ref('core/setisis');
const refTable = ref();
const refDialogForm = ref();
const refDialogConsulta = ref();
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

const clickEnviarPaquete = async (id) => {
    await axios.get(`/${resource.value}/enviar-paquete/${id}`)
        .then(response => {
            let res = response.data;
            // console.log(res);
            if (res.success) {
            } else {
            }
            // console.log(res);
        })
        .catch(error => {
        })
        .finally(() => {
        })
}

const clickConsultarPaquete = (id) => {
    recordId.value = id
    refDialogConsulta.value.openDialog();
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
    if (data.action === 'enviar_paquete') {
        clickEnviarPaquete(data.id)
    }
    if (data.action === 'consultar_paquete') {
        clickConsultarPaquete(data.id)
    }
}
</script>

<template>
    <BaseDataTable ref="refTable"
                   :resource="resource"
                   @actions="successActions"
                   @success-delete="successAction"
                   @success-active="successAction"/>

    <setisis-form ref="refDialogForm"
                  :resource="resource"
                  @success="successAction"/>

    <setisis-consulta ref="refDialogConsulta"
                      :record-id="recordId"
                      @success="successAction"/>
</template>
