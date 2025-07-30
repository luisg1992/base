<script setup>
import {ref, computed} from 'vue';
import axios from 'axios';
import {useForm} from '@inertiajs/vue3';

import BaseDatePicker from "@/components/WDatePicker/WDatePicker.vue";
import BaseModal from '@/components/BaseModal.vue';
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import {useAppStore} from '@/stores/useAppStore';
import WButton from "@/components/WButton/WButton.vue";
import WCheckbox from "@/components/WCheckbox/WCheckbox.vue";
import {showToastSuccess} from "../../../../utils/alert.js";

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

const gruposFiltrados = ref([]);
const categoriasFiltradas = ref([]);
let isViewMode = computed(() => props.viewRecord)
let errors = ref({});

const form = useForm({
    CodigoExportacion: '',
    Descripcion: '',
    DescripcionMINSA: '',
    CodigoCIE9: '',
    CodigoCIE10: '',
    Gestacion: false,
    Morbilidad: false,
    Intrahospitalario: false,
    codigoCIEsinPto: '',
    CodigoCIE10: '',
    EdadMaxDias: '',
    EdadMinDias: '',
    Restriccion: false,
    IdTipoSexo: null,
    FechaInicioVigencia: '',
    IdCapitulo: null,
    IdGrupo: null,
    IdCategoria: null,
    EsActivo: 1
})

const handleOpen = async () => {
    errors.value = {};
    form.reset()
    form.clearErrors()
    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form, data.data)
            gruposFiltrados.value = appStore.configuracionGrupo.filter(row => row.IdCapitulo === Number(form.IdCapitulo));
            categoriasFiltradas.value = appStore.configuracionCategoria.filter(row => row.IdGrupo === Number(form.IdGrupo));

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

const changeCapitulo = () => {
    if (form.IdCapitulo !== null) {
        gruposFiltrados.value = appStore.configuracionGrupo.filter(row => {
            return row.IdCapitulo === Number(form.IdCapitulo);
        });
        form.IdGrupo = null;
        form.IdCategoria = null;
        categoriasFiltradas.value = [];
    }
};

const changeGrupo = () => {
    form.IdCategoria = null;
    categoriasFiltradas.value = [];
    if (form.IdGrupo !== null) {
        categoriasFiltradas.value = appStore.configuracionCategoria.filter(row => {
            return row.IdGrupo === Number(form.IdGrupo);
        });
    }
};

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
        form.CodigoCIE10 = form.CodigoCIE10;
        form.codigoCIEsinPto = form.CodigoCIE10;
        const formData = {
            id: props.recordId ?? null,
            CodigoExportacion: form.CodigoExportacion,
            Descripcion: form.Descripcion,
            DescripcionMINSA: form.DescripcionMINSA,
            CodigoCIE9: form.CodigoCIE9,
            CodigoCIE10: form.CodigoCIE10,
            CodigoCIE10: form.CodigoCIE10,
            codigoCIEsinPto: form.codigoCIEsinPto,
            Gestacion: form.Gestacion,
            Intrahospitalario: form.Intrahospitalario,
            EdadMaxDias: form.EdadMaxDias,
            EdadMinDias: form.EdadMinDias,
            Restriccion: form.Restriccion,
            Morbilidad: form.Morbilidad,
            IdTipoSexo: form.IdTipoSexo,
            FechaInicioVigencia: form.FechaInicioVigencia,
            IdCapitulo: form.IdCapitulo,
            IdGrupo: form.IdGrupo,
            IdCategoria: form.IdCategoria,
            EsActivo: form.EsActivo
        };

        await axios.post(`/${props.resource}`, formData)
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
};

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
               :loading="isModalLoading"
               :viewRecord="isViewMode"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-md">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.IdCapitulo" :options="appStore.configuracionCapitulos"
                           label="Capitulo" :disabled="isViewMode" @update:model-value="changeCapitulo"/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.IdGrupo" :options="gruposFiltrados" label="Grupo"
                           :disabled="isViewMode" filter @update:model-value="changeGrupo"/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.IdCategoria" :options="categoriasFiltradas" label="Categoria"
                           :disabled="isViewMode"/>
            </div>
            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                <BaseInput v-model="form.CodigoCIE10" label="CodigoCIE10" placeholder="Ingrese una código"
                           :disabled="isViewMode"
                           :error="errors.CodigoCIE10"/>
            </div>
            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                <BaseInput v-model="form.CodigoCIE9"
                           label="CodigoCIE9"
                           placeholder="Ingrese una código"
                           :disabled="isViewMode"
                           :error="errors.CodigoCIE9"/>
            </div>
            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                <BaseInput v-model="form.CodigoExportacion"
                           label="CodigoExportacion"
                           placeholder="Ingrese un código"
                           :disabled="isViewMode"
                           :error="errors.CodigoExportacion"/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.Descripcion"
                           label="Descripcion"
                           placeholder="Ingrese una descripcion"
                           :disabled="isViewMode"
                           :error="errors.Descripcion"/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.DescripcionMINSA"
                           label="Descripcion MINSA"
                           placeholder="Ingrese una descripcion"
                           :disabled="isViewMode"
                           :error="errors.DescripcionMINSA"/>
            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                <BaseDatePicker v-model="form.FechaInicioVigencia"
                                label="Fecha"
                                placeholder="Selecciona la fecha"
                                :disabled="isViewMode"
                                :error="errors.FechaInicioVigencia"/>

            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-1" style="margin-top: 24px; ">
                <w-checkbox v-model="form.Restriccion"
                            :disabled="isViewMode"
                            label="Restricción"
                            binary>
                </w-checkbox>
            </div>

            <div class="col-12 col-sm-4  col-md-4 col-lg-4">
                <BaseCombo v-model="form.IdTipoSexo"
                           :options="appStore.personaTiposSexo"
                           label="Sexo"
                           :disabled="isViewMode" :filter="true">
                </BaseCombo>
            </div>
            <div class="col-12 col-sm-4  col-md-4  col-lg-4">
                <BaseInput v-model="form.EdadMinDias" label="Edad Min Días" placeholder="Ingrese una edad"
                           :disabled="isViewMode" :error="errors.EdadMinDias"/>
            </div>
            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                <BaseInput v-model="form.EdadMaxDias" label="Edad Max Días" placeholder="Ingrese una edad"
                           :disabled="isViewMode" :error="errors.EdadMaxDias"/>
            </div>
            <div class="col-12 col-sm-4  col-md-4  col-lg-4">
                <w-checkbox v-model="form.Gestacion"
                            :disabled="isViewMode"
                            label="Gestación"
                            binary>
                </w-checkbox>
            </div>
            <div class="col-12 col-sm-4  col-md-4 col-lg-4">
                <w-checkbox v-model="form.Morbilidad"
                            :disabled="isViewMode"
                            label="Morbilidad"
                            binary>
                </w-checkbox>
            </div>
            <div class="col-12 col-sm-4  col-md-4 col-lg-4">
                <w-checkbox v-model="form.Intrahospitalario"
                            :disabled="isViewMode"
                            label="Intrahospitalario"
                            binary>
                </w-checkbox>
            </div>

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
    </BaseModal>
</template>
