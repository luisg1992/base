<script setup>
import {ref, onMounted} from 'vue'

import BaseModal from '@/components/BaseModal.vue'
import BaseNested from "@/components/BaseNested.vue";
import {useAppStore} from '@/stores/useAppStore';
import axios from "axios";
import FormularioModuloNew from "./FormularioModuloNew.vue";
// import BaseModalDelete from "@/components/BaseModalDelete.vue";
import AccionFom from "./AccionFom.vue";
import BaseInput from "@/components/WInput/WInput.vue";

const emit = defineEmits(['close', 'success'])

const appStore = useAppStore();
const resource = 'core/modulos';
const refDialogModuloForm = ref();
const refDialogAccionForm = ref();
const refDialogDeleteForm = ref();
let isDialogOpen = ref(false);

let modulos = ref(null);
let parentId = ref(null);
let recordId = ref(null);
let moduleId = ref(null);
let searchModule = ref('');
let filterModules = ref([]);

const handleOpen = async () => {
    await getRecords();
}

const getRecords = async () => {
    const {data} = await axios.post(`/${resource}/get_records`)
    modulos.value = data;
    changeSearchModule();
}

const clickModuleNew = (data) => {
    parentId.value = data.ModuloId;
    recordId.value = null;
    refDialogModuloForm.value.openDialog();
}

const clickModuleEdit = (data) => {
    parentId.value = data.ModuloPadreId;
    recordId.value = data.ModuloId;
    refDialogModuloForm.value.openDialog();
}

const successModule = async () => {
    await getRecords();
}

const clickModuleDelete = (data) => {
    refDialogDeleteForm.value.openDialog(data.ModuloId);
}

const clickActionNew = (data) => {
    moduleId.value = data.ModuloId;
    refDialogAccionForm.value.openDialog();
}

const successAction = async () => {
    await getRecords();
}

const clickActionDelete = async (data) => {
    await axios.post(`/${resource}/action/destroy`, {'id': data.ModuloAccionId})
        .then(response => {
            getRecords();
        })
        .finally(() => {
        })
}

const successDelete = async () => {
    await getRecords();
}

const finishOrder = async (val) => {
    await axios.post(`/${resource}/actualizar_orden`, {'modulos': val})
        .then(response => {
            let data = response.data
            if (data.success) {
                getRecords()
                appStore.setMenu(data.data.menu);
            } else {
            }
        })
        .finally(() => {
        })
}

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    isDialogOpen.value = false;
}

const changeSearchModule = () => {
    filterModules.value = searchModule.value.trim()
        ? modulos.value.filter(item =>
            item.Etiqueta.toLowerCase().includes(searchModule.value.trim().toLowerCase())
        )
        : modulos.value;
}

onMounted(() => {

})

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               @close="closeDialog"
               @open="handleOpen"
               header="Módulos"
               size="modal-md">
        <div v-if="modulos">
            <base-input v-model="searchModule"
                        label="Buscar módulo"
                        @update:model-value="changeSearchModule"></base-input>
            <base-nested :children="filterModules"
                         @success="finishOrder"
                         @click-new="clickModuleNew"
                         @click-edit="clickModuleEdit"
                         @click-delete="clickModuleDelete"
                         @click-action-new="clickActionNew"
                         @click-action-delete="clickActionDelete"/>
        </div>


        <formulario-modulo-new ref="refDialogModuloForm"
                               :parent-id="parentId"
                               :record-id="recordId"
                               @success="successModule"></formulario-modulo-new>

        <accion-fom ref="refDialogAccionForm"
                    :modulo-id="moduleId"
                    @success="successAction"></accion-fom>

        <base-modal-delete ref="refDialogDeleteForm"
                           @success="successDelete"
                           :resource="resource"/>
    </BaseModal>
</template>
