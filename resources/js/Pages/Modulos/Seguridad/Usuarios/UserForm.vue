<script setup>

import {ref, computed} from 'vue';
import axios from 'axios';
import {useForm} from '@inertiajs/vue3';
import BaseModal from '@/components/BaseModal.vue';
import BaseInput from "@/components/WInput/WInput.vue";
import Password from 'primevue/password';
import {useAppStore} from '@/stores/useAppStore';
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import TabPanels from "primevue/tabpanels";
import Tab from "primevue/tab";
import TabPanel from "primevue/tabpanel";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import WButton from "@/components/WButton/WButton.vue";
import {showToastSuccess} from "../../../../utils/alert.js";
import Tree from "primevue/tree";

// Props y emits
const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

// Refs
let appStore = useAppStore();
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let isDialogOpen = ref(false);
let idRol = ref();
let modulos = ref([]);
let selectedKey = ref([]);
// Computed
let isViewMode = computed(() => props.viewRecord)
let errors = ref({});
let form = ref({});

const initForm = () => {
    form.value = {
        id: props.recordId ?? null,
        name: null,
        email: null,
        password: null,
        modules: [],
    }
}

const onSubmit = async () => {
    const accion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PARA EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accion}?`;
    const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');

    if (!confirmado) return;
    loadingSubmit.value = true
    form.value.modules = selectedKey.value;
    await axios.post(`/${props.resource}`, form.value)
        .then(response => {
            const data = response.data;
            if (data.success) {
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

const handleOpen = async () => {
    initForm();
    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form.value, data.data)
            await getPermissions();
        } catch (error) {
            if (error.status === 403) {
                closeDialog();
            }
        } finally {
            showModal.value = true;
            isModalLoading.value = false;
        }
    } else {
        showModal.value = true;
    }
}

const agregarRole = () => {
    if (idRol.value !== null) {
        let rol = appStore.seguridadRole.find(row => row.id === idRol.value);
        form.value.roles.push({
            id: rol.id,
            name: rol.label,
        })
        idRol.value = null;
    }
}

const removerRole = (index) => {
    form.value.roles.splice(index, 1);
}

const getPermissions = async () => {
    let url = `/seguridad/user_roles/modulos/records/${form.value.id}`;
    const {data} = await axios.get(url)
    modulos.value = data.records;
    selectedKey.value = data.selected_key;
}

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :recordId="form.id"
               :viewRecord="isViewMode"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-md">
        <div class="row">
            <div class="col-12">
                <BaseInput v-model="form.empleado_nombre"
                           label="Nombre del empleado"
                           disabled/>
            </div>
            <div class="col-12">
                <Tabs value="0">
                    <TabList>
                        <Tab value="0">Datos</Tab>
                        <Tab value="1">Roles</Tab>
                        <Tab value="2">Permisos</Tab>
                    </TabList>
                    <TabPanels style="padding: 16px 0">
                        <TabPanel value="0">
                            <div class="row">
                                <div class="col-md-12">
                                    <BaseInput v-model="form.name" label="Nombre del Usuario"
                                               placeholder="Ingrese el Nombre del Usuario"
                                               :disabled="isViewMode"
                                               :error="errors.name"/>
                                </div>

                                <div class="col-md-6">
                                    <BaseInput v-model="form.email" label="Usuario de Acceso al Sistema"
                                               placeholder="Ingrese el Usuario" :disabled="isViewMode"
                                               :error="errors.email"/>
                                </div>

                                <div class="col-md-6">
                                    <label for="password">Contraseña</label><br>
                                    <Password v-model="form.password" :feedback="false" toggleMask class="w-full"
                                              style="width: 100%;"
                                              :disabled="isViewMode" :autocomplete="false"
                                              :class="{ 'p-invalid': errors.password }"/>
                                </div>
                            </div>
                        </TabPanel>
                        <TabPanel value="1">
                            <div class="col-12" v-if="!isViewMode">
                                <base-combo label="Asignar roles"
                                            v-model="idRol"
                                            :options="appStore.seguridadRole"
                                            :show-clear="false"
                                            :show-button="!isViewMode"
                                            @click-button="agregarRole"
                                            filter></base-combo>
                            </div>
                            <div class="col-12" style="margin-top: 16px">
                                <table class="table table-sm table-hover">
                                    <tbody>
                                    <tr v-for="(c, index) in form.roles">
                                        <td>
                                            <div class="salto-de-linea" style="width: 260px">
                                                {{ c.name }}
                                            </div>
                                        </td>
                                        <td style="text-align: right">
                                            <button @click="removerRole(index)"
                                                    class="btn btn-danger btn-sm"
                                                    v-if="!isViewMode">
                                                <i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </TabPanel>
                        <TabPanel value="2">
                            <div class="col-12">
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
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
            <div class="d-flex justify-content-end mt-4 gap-2">
                <w-button type="secondary"
                          label="Cerrar"
                          text
                          @click="closeDialog"
                          :disabled="loadingSubmit"/>
                <w-button type="primary"
                          :label="`${loadingSubmit?'Guardando...' : 'Guardar'}`"
                          @click="onSubmit"
                          v-if="!isViewMode"
                          :disabled="loadingSubmit"/>
            </div>
        </div>
    </BaseModal>
</template>
