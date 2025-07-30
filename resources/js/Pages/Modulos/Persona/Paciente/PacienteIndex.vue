<script setup>
import ModalFormulario from './PacienteForm.vue';
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import {useConfirm} from "primevue/useconfirm";
import {ref} from 'vue';
import axios from "axios";
import {useAppStore} from '@/stores/useAppStore';
import ModalLoader from "@/components/ModalLoader.vue";

let appStore = useAppStore();
const resource = ref('personas/pacientes')
const refTable = ref()
const refDialogForm = ref()
const confirm = useConfirm();
const loading = ref(false);

// Control de estado del modal
const recordId = ref(null)
const viewRecord = ref(false)

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

const clicActualizarDatosReniec = (id) => {
    confirm.require({
        message: '¿Seguro que desea actualizar los datos del paciente con las de RENIEC?',
        header: 'Confirmación',
        icon: 'ti ti-exclamation-circle',
        rejectProps: {
            label: 'Cancelar',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Confirmar',
        },
        accept: async () => {
            try {
                loading.value = true;
                const response = await axios.post(`/${resource.value}/actualizar_paciente_por_reniec`, { id });
                const data = response.data;

                if (data.success) {
                    await refTable.value.listarRegistros();
                    showAlert('PROCESO REALIZADO EXITOSAMENTE', data.mensaje, 'success');
                } else {
                    showAlert('PROCESO NO REALIZADO', data.mensaje, 'error');
                }
            } catch (error) {
                showAlert('ERROR DE CONEXIÓN', 'No se pudo conectar con el servidor.', 'error');
                console.error(error);
            } finally {
                loading.value = false;
            }
        },
        reject: () => {

        }
    });
}

const successAction = () => {
    refTable.value.listarRegistros();
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
    if (data.action === 'actualiza_datos_reniec') {
        clicActualizarDatosReniec(data.id);
    }

}
</script>

<template>
    <ConfirmDialog></ConfirmDialog>
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

    <modal-loader v-if="loading"></modal-loader>
</template>
