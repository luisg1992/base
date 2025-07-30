<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';

import BaseModal from '@/components/BaseModal.vue';
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import { useAppStore } from '@/stores/useAppStore'
import Select from "primevue/select";
import WButton from "@/components/WButton/WButton.vue";
import { showToastSuccess } from "../../../../utils/alert.js";

let appStore = useAppStore();
const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);

let loadingProductosConsulta = ref(false)
let loadingProductosInterconsulta = ref(false)

let moduloEspecialidadesPrimarias = ref([]);
let productosConsulta = ref([]);
let productosInterconsulta = ref([]);
let debounceTimeout = ref(null);

let isViewMode = computed(() => props.viewRecord);
let errors = ref({});
const form = useForm({
    Nombre: '',
    IdEspecialidadPrimaria: null,
    IdDepartamento: null,
    TiempoPromedioAtencion: null,
    IdProductoConsulta: null,
    IdProductoInterconsulta: null,
    IdEstado: 1,
    NombreProductoConsulta: '',
    NombreProductoInterconsulta: '',
});

const handleOpen = async () => {
    moduloEspecialidadesPrimarias.value = appStore.configuracionEspecialidadesPrimarias;
    errors.value = {};
    form.reset()
    form.clearErrors()

    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const { data } = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form, data.data);
            productosConsulta.value = appStore.catalogoServiciosEspecialidades.filter(row => row.id === form.IdProductoConsulta);
            productosInterconsulta.value = appStore.catalogoServiciosEspecialidades.filter(row => row.id === form.IdProductoInterconsulta);
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
};

const changueEspecialidadPrimaria = (valorSeleccionado) => {
    const especialidadPrimaria = moduloEspecialidadesPrimarias.value.find(
        (item) => item.value === valorSeleccionado
    );
    if (especialidadPrimaria) {
        form.IdDepartamento = especialidadPrimaria.IdDepartamento;
    }
};

const fetchOptionsProductosConsulta = async (query) => {
    if (!query) {
        productosConsulta.value = [];
        return;
    }
    loadingProductosConsulta.value = true;
    try {
        const response = await axios.post('/filtrar_catalogo_servicio_especialidades', { buscar: query });
        productosConsulta.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingProductosConsulta.value = false;
    }
}

const onFilterProductosConsulta = (event) => {
    productosConsulta.value = [];
    if (event.value.length > 3) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsProductosConsulta(event.value);
        }, 500);
    }
}

const fetchOptionsProductosInterconsulta = async (query) => {
    if (!query) {
        productosInterconsulta.value = [];
        return;
    }
    loadingProductosInterconsulta.value = true;
    try {
        const response = await axios.post('/filtrar_catalogo_servicio_especialidades', { buscar: query });
        productosInterconsulta.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingProductosInterconsulta.value = false;
    }
}

const onFilterProductosInterconsulta = (event) => {
    productosInterconsulta.value = [];
    if (event.value.length > 3) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsProductosInterconsulta(event.value);
        }, 500);
    }
}

const onSubmit = async () => {
    const accionConfirmacion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PAR EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensajeConfirmacion = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accionConfirmacion}?`;
    const confirmado = await showAlertConfirmacion(
        'VALIDACIÓN DE DATOS REALIZADOS', mensajeConfirmacion,
        'warning'
    );

    // Si el usuario confirma, procedemos con el guardado
    if (confirmado) {
        loadingSubmit.value = true;

        const formData = {
            id: props.recordId ?? null,
            Nombre: form.Nombre,
            IdEspecialidadPrimaria: form.IdEspecialidadPrimaria,
            IdDepartamento: form.IdDepartamento,
            IdEstado: form.IdEstado,
            TiempoPromedioAtencion: form.TiempoPromedioAtencion,
            IdProductoConsulta: form.IdProductoConsulta,
            IdProductoInterconsulta: form.IdProductoInterconsulta,
        };

        await axios.post(`/${props.resource}`, formData)
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
};

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

defineExpose({ openDialog });
</script>


<template>
    <BaseModal :isVisible="isDialogOpen" :recordId="form.id" :viewRecord="isViewMode" :loading="isModalLoading"
        @close="closeDialog" @open="handleOpen" size="modal-xl">
        <div class="row">

            <!-- Campos -->
            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                <BaseCombo v-model="form.IdEspecialidadPrimaria" :options="moduloEspecialidadesPrimarias"
                    label="Especialidades Primarias" :disabled="isViewMode" :filter="true"
                    @update:modelValue="changueEspecialidadPrimaria">
                </BaseCombo>
            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                <BaseInput v-model="form.Nombre" label="Descripción" placeholder="Ingrese una descripción"
                    :disabled="isViewMode" :error="errors.Nombre" />
            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                <BaseInput v-model="form.TiempoPromedioAtencion" label="Tiempo Promedio de Atención" type="number"
                    placeholder="Ingrese un Tiempo Promedio de Atención" :disabled="isViewMode"
                    :error="errors.TiempoPromedioAtencion" />
            </div>

            <div class="col-md-12">
                <label class="form-label">Producto Consulta</label>
                <Select v-model="form.IdProductoConsulta" :options="productosConsulta" option-label="Nombre"
                    option-value="IdProducto" filter filterPlaceholder="Buscar..." @filter="onFilterProductosConsulta"
                    :loading="loadingProductosConsulta" placeholder="Buscar producto consulta por descripción"
                    :showClear="true" class="w-full" size="small" style="width: 100%;" :autoFilterFocus="true"></Select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Producto Interconsulta</label>
                <Select v-model="form.IdProductoInterconsulta" :options="productosInterconsulta" option-label="Nombre"
                    option-value="IdProducto" filter filterPlaceholder="Buscar..."
                    @filter="onFilterProductosInterconsulta" :loading="loadingProductosInterconsulta"
                    placeholder="Buscar producto interconsulta por descripción" :showClear="true" class="w-full"
                    size="small" style="width: 100%;" :autoFilterFocus="true"></Select>

                <!--                    <div class="d-flex align-items-center">-->
                <!--                        <div class="flex-grow-1 me-2">-->
                <!--                            <label for="IdProductoInterconsulta">Producto Interconsulta</label> <br>-->
                <!--                            <AutoComplete v-model="form.IdProductoInterconsulta"-->
                <!--                                          :suggestions="productosInterconsulta"-->
                <!--                                          optionLabel="Nombre"-->
                <!--                                          optionValue="IdProducto"-->
                <!--                                          field="Nombre"-->
                <!--                                          :disabled="isViewMode"-->
                <!--                                          placeholder="Buscar producto consulta por Descripción"-->
                <!--                                          dropdownStyle="width: 100%;"-->
                <!--                                          @complete="buscarproductosInterconsulta"-->
                <!--                                          :value="form.NombreProductoInterconsulta"/>-->
                <!--                        </div>-->
                <!--                        &lt;!&ndash;                        <button type="button" class="btn btn-secondary h-auto py-2 mt-5"&ndash;&gt;-->
                <!--                        &lt;!&ndash;                                @click="buscarproductosInterconsulta"&ndash;&gt;-->
                <!--                        &lt;!&ndash;                                :disabled="isViewMode">&ndash;&gt;-->
                <!--                        &lt;!&ndash;                            Buscar&ndash;&gt;-->
                <!--                        &lt;!&ndash;                        </button>&ndash;&gt;-->
                <!--                    </div>-->
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4 gap-2">
            <w-button type="secondary" label="Cerrar" text @click="closeDialog" :disabled="loadingSubmit" />
            <w-button type="primary" :label="`${loadingSubmit ? 'Guardando...' : 'Guardar'}`" @click="onSubmit"
                v-if="!isViewMode" :disabled="loadingSubmit" />
        </div>
    </BaseModal>
</template>
