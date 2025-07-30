<script setup>
    import ModalFormulario from './FormularioPrueba.vue';
    import BaseDataTable from '@/components/WDataTable/WDataTable.vue';
    import { ref } from 'vue'
    
    //Recurso de la tabla
    const resource = ref('imagenologia/lista-espera');
    const refTable = ref();
    const refDialogForm = ref();

    //Control de estado del modal
    const recordId = ref(null);
    const puntoCargaId = ref(null);
    const viewRecord = ref(false);
    //let modalVisible = ref(false);


    const clickProgramar = (id, idPc) => {
        //console.log(id);
        recordId.value = id
        puntoCargaId.value = idPc
        viewRecord.value = false
        //modalVisible = true
        refDialogForm.value.openDialog()
    }
    
    //momentaneamente asignamos la variable data.url como puntocargaid
    const successActions = (data) => {
        if(data.action == 'programar') {
            console.log(data)
            clickProgramar(data.id, data.url)
        }
    }


</script>
<template>
    <BaseDataTable ref="refTable" :resource="resource" @actions="successActions" />
    
    <ModalFormulario ref="refDialogForm" :record-id="recordId" :punto-carga-id="puntoCargaId" :resource="resource" :view-record="viewRecord" />
</template>