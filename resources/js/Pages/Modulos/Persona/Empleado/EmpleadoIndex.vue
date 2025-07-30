<script setup>
import ModalFormulario from './EmpleadoForm.vue'
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import {useConfirm} from "primevue/useconfirm";
import {ref} from 'vue';
import axios from "axios";
import {useAppStore} from '@/stores/useAppStore';
import ModalLoader from "@/components/ModalLoader.vue";

let appStore = useAppStore();
const resource = ref('seguridad/empleados')
const refTable = ref()
const refDialogForm = ref()
const confirm = useConfirm();

// Control de estado del modal
const recordId = ref(null);
const viewRecord = ref(false);
const loading = ref(false);

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

const clickGenerarUsuario = (id) => {
    confirm.require({
        message: '¿Seguro que desea generar el usuario?',
        header: 'Confirmación',
        icon: 'ti ti-exclamation-circle',
        rejectProps: {
            label: 'Cancelar',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Generar',
        },
        accept: () => {
            axios.post(`/${resource.value}/generar_usuario`, {
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

const clickEliminarUsuario = (id) => {
    confirm.require({
        message: '¿Seguro que desea eliminar el usuario?',
        header: 'Confirmación',
        icon: 'ti ti-exclamation-circle',
        rejectProps: {
            label: 'Cancelar',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            severity: 'danger',
            label: 'Eliminar',
        },
        accept: () => {
            axios.post(`/${resource.value}/eliminar_usuario`, {
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


const clicActualizarDatosReniec = (id) => {
    confirm.require({
        message: '¿Seguro que desea actualizar los datos del empleado con las de RENIEC?',
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
                const response = await axios.post(`/${resource.value}/actualizar_empleado_por_reniec`, { id });
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
    if (data.action === 'generarUsuario') {
        clickGenerarUsuario(data.id);
    }
    if (data.action === 'eliminarUsuario') {
        clickEliminarUsuario(data.id);
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
