<script setup>
import AdmisionEmergenciaForm from './AdmisionEmergenciaForm.vue';
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import {onMounted, ref} from 'vue';
import TabList from "primevue/tablist";
import TabPanel from "primevue/tabpanel";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import Tabs from "primevue/tabs";
import TriajeEmergenciaForm from "../TriajeEmergencia/TriajeEmergenciaForm.vue";
import axios from "axios";
import BuscarPacienteForm from "../TriajeEmergencia/components/BuscarPacienteForm.vue";

const resource = ref('emergencia/admision-emergencia');
const refTable = ref();
const refTableTriaje = ref();
const refDialogForm = ref();
const refDialogTraijeForm = ref();
const refDialogBuscarCliente = ref();
const recordId = ref(null);
const triajeId = ref(null);
const viewRecord = ref(false);
const viewRecordTriaje = ref(false);
let moduloActual = ref();
let paciente = ref();
let accion = ref(null);

const obtenerModuloActual = async () => {
    await axios.post('/core/modulos/obtener-modulo-actual', {
        path: window.location.pathname.substring(1),
    })
        .then(response => {
            moduloActual.value = response.data;
        })
        .catch(error => {
            console.log(error);
        });
}

const clickCreate = (id = null) => {
    triajeId.value = null;
    viewRecordTriaje.value = false;
    recordId.value = id
    viewRecord.value = false
    refDialogForm.value.openDialog()
}

const clickView = (id) => {
    recordId.value = id
    triajeId.value = null;
    viewRecord.value = true
    viewRecordTriaje.value = true;
    refDialogForm.value.openDialog()
}

const clickViewTraije = (id) => {

}

const successAction = () => {
    refTable.value.listarRegistros();
    refTableTriaje.value.listarRegistros();
}

const successActionsTriaje = (data) => {
    if (data.action === 'new') {
        accion.value = 'registrar';
        recordId.value = null;
        triajeId.value = null;
        viewRecord.value = false;
        viewRecordTriaje.value = false;
        refDialogBuscarCliente.value.openDialog();
    }
    if (data.action === 'view') {
        accion.value = 'ver';
        recordId.value = data.id;
        triajeId.value = null;
        viewRecord.value = true;
        viewRecordTriaje.value = true;
        refDialogTraijeForm.value.openDialog()
    }
    if (data.action === 'admitir') {
        accion.value = 'admitir';
        triajeId.value = data.id;
        recordId.value = null;
        viewRecord.value = false;
        viewRecordTriaje.value = true;
        refDialogForm.value.openDialog();
    }
}

const successActions = (data) => {
    if (data.action === 'view') {
        accion.value = 'ver';
        recordId.value = data.id
        triajeId.value = null;
        viewRecord.value = true
        viewRecordTriaje.value = true;
        refDialogForm.value.openDialog()
    }
}

const successBuscarPaciente = (data) => {
    refDialogBuscarCliente.value.closeDialog();
    paciente.value = data;
    refDialogForm.value.openDialog();
}

onMounted(() => {
    obtenerModuloActual()
})
</script>

<template>
    <Tabs value="0">
        <TabList>
            <Tab value="0">Admisión</Tab>
            <Tab value="1">Lista de pacientes admitidos</Tab>
        </TabList>
        <TabPanels style="padding: 16px 0">
            <TabPanel value="0">
                <BaseDataTable ref="refTableTriaje"
                               :resource="`${resource}/triaje`"
                               @actions="successActionsTriaje"/>
            </TabPanel>
            <TabPanel value="1">
                <BaseDataTable ref="refTable"
                               :resource="resource"
                               @actions="successActions"
                               @success-delete="successAction"
                               @success-active="successAction"/>
            </TabPanel>
        </TabPanels>
    </Tabs>
    <admision-emergencia-form ref="refDialogForm"
                              :accion="accion"
                              :triaje-id="triajeId"
                              :record-id="recordId"
                              :resource="resource"
                              :view-record-triaje="viewRecordTriaje"
                              :view-record="viewRecord"
                              :modulo-actual="moduloActual"
                              :paciente="paciente"
                              @success="successAction"/>

    <triaje-emergencia-form ref="refDialogTraijeForm"
                            :accion="accion"
                            :record-id="recordId"
                            :view-record="true"
                            :modulo-actual="moduloActual"
                            resource="emergencia/triaje-emergencia"></triaje-emergencia-form>

    <buscar-paciente-form ref="refDialogBuscarCliente"
                          @success="successBuscarPaciente"/>
</template>
