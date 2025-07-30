<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ModalFormulario from './FormularioEspecialidad.vue';
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import {ref} from 'vue';
import axios from "axios";
import {useConfirm} from "primevue/useconfirm";
import {useAppStore} from '@/stores/useAppStore';

// Recurso para la tabla
let appStore = useAppStore();
const resource = ref('configuracion/especialidades');
const refTable = ref();
const refDialogForm = ref();
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

const successAction = () => {
    appStore.fetchTabla('configuracionEspecialidades');
    refTable.value.listarRegistros()
    cerrarModal()
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
    if (data.action === 'duplicar') {
        clickDuplicar(data.id);
    }
}

</script>

<template>
    <ConfirmDialog></ConfirmDialog>
    <BaseDataTable ref="refTable" :resource="resource"
                   @actions="successActions"
                   @success-delete="successAction"
                   @success-active="successAction"/>

    <ModalFormulario ref="refDialogForm"
                     :record-id="recordId"
                     :resource="resource"
                     :view-record="viewRecord"
                     @success="successAction"/>
</template>
