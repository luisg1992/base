<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'
import {useForm} from '@inertiajs/vue3'
import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import Tree from 'primevue/tree'
import BaseNested from "@/components/BaseNested.vue";
import BaseRoleNested from "@/components/BaseRoleNested.vue";
import {showToastSuccess} from "../../../../utils/alert.js";

const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

const modulosHijos = ref([])
let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let loading = ref(false);
let showModal = ref(false);
let modulos = ref([]);
let selectedKey = ref([]);

let title = ref();
let errors = ref({});
let form = ref({});

const initForm = () => {
    errors.value = {};
    form.value = {
        id: null,
        name: null,
    }
    modulos.value = [];
    selectedKey.value = [];
}

const handleOpen = async () => {
    title.value = 'Nuevo Rol';
    initForm();
    await getRecords();
    if (props.recordId) {
        loading.value = true;
        title.value = 'Editar Rol';
        await axios.get(`/seguridad/roles/modulos/record/${props.recordId}`)
            .then(response => {
                let data = response.data;
                Object.assign(form.value, data)
            })
            .catch(error => {
                console.error('Error al cargar datos del registro:', error)
            })
            .finally(() => {
                loading.value = false;
            })
    }
}

const getRecords = async () => {
    let url = (props.recordId) ? `/seguridad/roles/modulos/records/${props.recordId}` : '/seguridad/roles/modulos/records';
    const {data} = await axios.get(url)
    modulos.value = data.records;
    selectedKey.value = data.selected_key;
}

const onSubmit = async () => {
    await axios.post(`/seguridad/roles/modulos`, {
        id: form.value.id,
        name: form.value.name,
        modules: selectedKey.value,
    })
        .then(response => {
            const data = response.data;
            if(data.success) {
                emit('success');
                closeDialog();
                showToastSuccess(data.mensaje)
            } else {
                showToastError(data.mensaje)
            }
        })
        .catch(error => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors
            }
        })
        .finally(() => {
            loadingSubmit.value = false;
        })
}

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    isDialogOpen.value = false;
}

defineExpose({openDialog})

</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :header="title"
               @close="closeDialog"
               @open="handleOpen"
               :loading="loading"
               size="modal-md">
        <div class="row">
            <div class="col-md-12 mb-3">
                <BaseInput v-model="form.name"
                           label="Título"
                           placeholder="Ingrese nombre del rol"
                           :error="errors.name"/>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button class="btn btn-primary"
                        :disabled="loadingSubmit"
                        @click="onSubmit">
                    {{ loadingSubmit ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
            <div class="col-md-12 mb-3" v-if="modulos">
                <Tree v-model:selectionKeys="selectedKey"
                      :value="modulos"
                      selectionMode="checkbox"
                      :filter="true"
                      filterMode="lenient">
                    <template #default="slotProps">
                        {{ slotProps.node.label }}<br/>
                        <span style="text-transform: none!important; font-size: 13px; color: grey; font-weight: 400">
                            {{ slotProps.node.description }}
                        </span>
                    </template>
                </Tree>
            </div>
        </div>
    </BaseModal>
</template>
