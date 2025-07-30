<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'
import {useForm} from '@inertiajs/vue3'
import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import Tree from 'primevue/tree'

const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

const modulosHijos = ref([])
let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);

let isViewMode = computed(() => props.viewRecord)
let errors = ref({});
const form = useForm({
    name: '',
    permisos: []
})

const nodes = ref([])
const selectedKey = ref({})
const expandedKeys = ref({})

const searchQuery = ref("")

// Filtrar nodos seleccionados cuando esté en vista (isViewMode)
const filteredNodes = computed(() => {
    if (isViewMode.value) {
        // Filtrar solo los nodos seleccionados
        const selectedNodes = []
        nodes.value.forEach(mod => {
            const selectedChildren = mod.children.filter(child => selectedKey.value[child.key]?.checked)
            if (selectedChildren.length > 0) {
                selectedNodes.push({...mod, children: selectedChildren})
            }
        })
        return selectedNodes
    }
    return nodes.value
})

// Generar árbol jerárquico de módulos y permisos con todos expandidos
const mapModulosToTreeNodes = (modulos) => {
    const expanded = {}

    const tree = modulos.map(mod => {
        const modKey = `mod-${mod.id}`
        expanded[modKey] = true

        const children = (mod.permissions || []).map(p => {
            const permKey = String(p.id)
            expanded[permKey] = true
            return {
                key: permKey,
                label: p.descripcion,
                selectable: true
            }
        })

        return {
            key: modKey,
            label: mod.name,
            children,
            selectable: false
        }
    })

    expandedKeys.value = expanded
    return tree
}

const cargarModulosYPermisos = async () => {
    try {
        const {data} = await axios.get(`/${props.resource}/listar-tablas`)
        modulosHijos.value = data

        nodes.value = mapModulosToTreeNodes(modulosHijos.value)
        if (form.permisos.length > 0) {
            const seleccion = {}
            form.permisos.forEach(id => {
                seleccion[String(id)] = {checked: true}
            })
            selectedKey.value = seleccion
        }
    } catch (error) {
        console.error('Error al cargar módulos:', error)
    }
}

// Propagar selección de hijos a padres
const actualizarSeleccionPadre = (node) => {
    if (node.parent) {
        const allSelected = node.parent.children.every(child => selectedKey.value[child.key]?.checked);
        if (allSelected) {
            selectedKey.value[node.parent.key] = {checked: true}
        } else {
            delete selectedKey.value[node.parent.key];
        }
        actualizarSeleccionPadre(node.parent);
    }
}

// Manejar la selección de nodos
const handleSelect = (event) => {
    if (event.node.selectable) {
        actualizarSeleccionPadre(event.node);
    }
}

// Abrir el modal con datos de registro si es necesario
const handleOpen = async () => {
    errors.value = {};
    form.reset()
    form.clearErrors()
    selectedKey.value = {}

    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            form.name = data.data.name
            form.permisos = data.data.permission_ids || []
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
    await cargarModulosYPermisos();
}

const guardar = async () => {
    const accion = props.recordId ? 'ACTUALIZACIÓN' : 'REGISTRO'
    const confirmado = await showAlertConfirmacion(
        'VALIDACIÓN DE DATOS',
        `¿Está seguro que desea realizar la ${accion} solicitada?`,
        'warning'
    )

    if (!confirmado) return
    loadingSubmit.value = true

    const formData = {
        id: props.recordId ?? null,
        name: form.name,
        permisos: Object.keys(selectedKey.value).map(Number)
    }

    await axios.post(`/${props.resource}`, formData)
        .then(response => {
            loadingSubmit.value = false;
            emit('success');
            isDialogOpen.value = false;

            const accion = props.recordId ? 'ACTUALIZADO' : 'REALIZADO';
            const mensaje = `EL REGISTRO FUE ${accion} DE FORMA EXITOSA`;
            showAlert('PROCESO REALIZADO EXITOSAMENTE', mensaje, 'success');
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
const seleccionarTodos = () => {
    const seleccion = {}
    modulosHijos.value.forEach(modulo => {
        modulo.permissions.forEach(permiso => {
            seleccion[String(permiso.id)] = {checked: true}
        })
    })
    selectedKey.value = seleccion
    showAlert('PROCESO REALIZADO EXITOSAMENTE', 'SELECCIÓN GENERAL DE PERMISOS FUE REALIZADA CON ÉXITO.', 'success');
}

const deseleccionarTodos = () => {
    selectedKey.value = {}
    showAlert('PROCESO REALIZADO EXITOSAMENTE', 'SELECCIÓN GENERAL DE PERMISOS FUE RETIRADA CON ÉXITO.', 'success');
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
               :loading="isModalLoading"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-md">
        <form @submit.prevent="guardar">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <BaseInput v-model="form.name" label="Título" placeholder="Ingrese nombre del rol"
                               :disabled="isViewMode" :error="errors.name"/>
                </div>

                <!-- Botón de Guardar -->
                <div v-if="!isViewMode" class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" :disabled="loadingSubmit">
                        {{ loadingSubmit ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>

                <div class="col-md-12 mb-3" v-if="nodes.length">
                    <div v-if="!isViewMode" class="d-flex justify-content-between">
                        <!-- Botones de Seleccionar y Deseleccionar Todos -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-success" @click="seleccionarTodos"
                                    :disabled="loadingSubmit">
                                SELECCIONAR TODOS
                            </button>
                            <button type="button" class="btn btn-danger" @click="deseleccionarTodos"
                                    :disabled="loadingSubmit">
                                DESELECCIONAR TODOS
                            </button>
                        </div>
                    </div>

                    <!-- El filtro se pasa al Tree -->
                    <div v-if="!isViewMode" class="row">
                        <Tree v-model:selectionKeys="selectedKey" v-model:expandedKeys="expandedKeys"
                              :value="filteredNodes" selectionMode="checkbox" class="w-100" :filter="!isViewMode"
                              :filterValue="searchQuery" filterMode="lenient" @select="handleSelect"/>
                    </div>
                    <!-- El filtro se pasa al Tree -->
                    <div v-if="isViewMode" class="row">
                        <Tree v-model:selectionKeys="selectedKey" v-model:expandedKeys="expandedKeys"
                              :value="filteredNodes" class="w-100"/>
                    </div>
                </div>
            </div>

        </form>
    </BaseModal>
</template>
