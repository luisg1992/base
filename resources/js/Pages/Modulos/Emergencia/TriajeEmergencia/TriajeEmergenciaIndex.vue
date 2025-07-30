<script setup>
import TriajeEmergenciaForm from './TriajeEmergenciaForm.vue';
import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
import {onMounted, ref} from 'vue';
import axios from "axios";
import BuscarPacienteForm from "./components/BuscarPacienteForm.vue";
import AdmisionEmergenciaForm from "../AdmisionEmergencia/AdmisionEmergenciaForm.vue";

const resource = ref('emergencia/triaje-emergencia');
const refTable = ref();
const refDialogForm = ref();
const refDialogBuscarCliente = ref();
const recordId = ref(null);
const viewRecord = ref(false);
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
    recordId.value = id
    viewRecord.value = false
    accion.value = 'editar'
    refDialogForm.value.openDialog()
}

const clickView = (id) => {
    recordId.value = id
    viewRecord.value = true
    accion.value = 'ver'
    refDialogForm.value.openDialog()
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
        refDialogBuscarCliente.value.openDialog();
    }
}

const successBuscarPaciente = (data) => {
    refDialogBuscarCliente.value.closeDialog();
    paciente.value = data;
    accion.value = 'registrar';
    refDialogForm.value.openDialog();
}

onMounted(() => {
    obtenerModuloActual()
})
</script>

<template>
    <BaseDataTable ref="refTable"
                   :resource="resource"
                   @actions="successActions"
                   @success-delete="successAction"
                   @success-active="successAction"/>

    <triaje-emergencia-form ref="refDialogForm"
                            :accion="accion"
                            :paciente="paciente"
                            :record-id="recordId"
                            :resource="resource"
                            :view-record="viewRecord"
                            :modulo-actual="moduloActual"
                            @success="successAction"/>

    <buscar-paciente-form ref="refDialogBuscarCliente"
                          @success="successBuscarPaciente"/>
</template>
