<script setup>
import ModalFormulario from './FormularioServicio.vue';
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import {ref} from 'vue';
import UpsForm from "./UpsForm.vue";
import axios from "axios";
import {useConfirm} from "primevue/useconfirm";

// Recurso para la tabla
const resource = ref('configuracion/servicios');
const refTable = ref();
const refDialogForm = ref();
const refDialogUpsForm = ref();
const confirm = useConfirm();

// Control de estado del modal
const recordId = ref(null);
const viewRecord = ref(false);
const modalVisible = ref(false);

const clickCreate = (id = null) => {
    recordId.value = id;
    viewRecord.value = false;
    modalVisible.value = true;
    refDialogForm.value.openDialog();
}

const clickView = (id) => {
    recordId.value = id;
    viewRecord.value = true;
    modalVisible.value = true;
    refDialogForm.value.openDialog();
}

const clickUpsAdicional = (id) => {
    recordId.value = id;
    refDialogUpsForm.value.openDialog();
}

const clickDuplicar = (id) => {
    confirm.require({
        message: '¿Seguro que desea duplicar el registro?',
        header: 'Confirmación',
        icon: 'ti ti-exclamation-circle',
        rejectProps: {
            label: 'Cancelar',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Duplicar'
        },
        accept: () => {
            axios.post(`/${resource.value}/duplicar`, {
                codigo: id
            })
                .then(response => {
                    let data = response.data;
                    if (data.success) {
                        refTable.value.listarRegistros()
                        showAlert('PROCESO REALIZADO EXITOSAMENTE', data.mensaje, 'success');
                    } else {
                        showAlert('PROCESO REALIZADO EXITOSAMENTE', data.mensaje, 'error');
                    }
                })
                .catch(error => {
                })
                .finally(() => {
                })
        },
        reject: () => {

        }
    });
}

const cerrarModal = () => {
    modalVisible.value = false;
}

const recargarLista = () => {
    refTable.value.listarRegistros();
    cerrarModal();
}

const successActions = (data) => {
    if (data.action === 'view') {
        clickView(data.id);
    }
    if (data.action === 'edit') {
        clickCreate(data.id);
    }
    if (data.action === 'new') {
        clickCreate();
    }
    if (data.action === 'ups') {
        clickUpsAdicional(data.id);
    }
    if (data.action === 'duplicar') {
        clickDuplicar(data.id);
    }
}
</script>

<template>
    <ConfirmDialog></ConfirmDialog>
        <BaseDataTable ref="refTable" :resource="resource" @actions="successActions"/>

        <ModalFormulario ref="refDialogForm" :record-id="recordId" :resource="resource" :view-record="viewRecord"
                         @success="recargarLista"/>

        <ups-form ref="refDialogUpsForm"
                          :record-id="recordId"
                          :resource="resource"
                          :view-record="viewRecord"
                          @success="recargarLista"/>
</template>
