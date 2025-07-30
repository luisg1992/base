<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'
import {useForm} from '@inertiajs/vue3'
import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import {useAppStore} from '@/stores/useAppStore'
import WCheckbox from "@/components/WCheckbox/WCheckbox.vue";
import WButton from "@/components/WButton/WButton.vue";
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

const IdTipoFinanciamiento = ref(null);
let isViewMode = computed(() => props.viewRecord)
let errors = ref({});

const form = useForm({
    Descripcion: '',
    IdTipoFinanciamiento: null,
    idTipoConceptoFarmacia: null,
    UtilizadoEn: null,
    CodigoFuenteFinanciamientoSEM: null,
    idAreaTramitaSeguros: null,
    EsUsadoEnCaja: false,
    CodigoHIS: null,
    idTipoFinanciador: null,
    codigo: null,
    Orden: null,
    fuentefinanciamiento: [],
    idEstado: 1
})

const opcionesUtilizadoEn = ref([
    {value: 1, label: 'Consultorios Externos'},
    {value: 2, label: 'Hospitalización / Emergencia'},
    {value: 3, label: 'Ambos'}
]);

const handleOpen = async () => {
    errors.value = {};
    form.reset();
    form.clearErrors();

    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form, data.data);

            // Asignar las tarifas al formulario
            form.fuentefinanciamiento = data.data.tarifas.map(tarifa => ({
                IdTipoFinanciamiento: tarifa.idTipoFinanciamiento
            }));

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

const agregarTipoFinanciamiento = () => {
    if (IdTipoFinanciamiento.value !== null) {
        let financiamiento = appStore.configuracionTipoFinanciamiento.find(
            row => row.id === IdTipoFinanciamiento.value
        );
        if (financiamiento) {
            form.fuentefinanciamiento.push({
                IdTipoFinanciamiento: financiamiento.id,
            });
            IdTipoFinanciamiento.value = null;
        }
    }
};

const removerTipoFinanciamiento = (index) => {
    form.fuentefinanciamiento.splice(index, 1);
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
        const formData = {
            id: props.recordId ?? null,
            Descripcion: form.Descripcion,
            IdTipoFinanciamiento: form.IdTipoFinanciamiento,
            idTipoConceptoFarmacia: form.idTipoConceptoFarmacia,
            UtilizadoEn: form.UtilizadoEn,
            CodigoFuenteFinanciamientoSEM: form.CodigoFuenteFinanciamientoSEM,
            idAreaTramitaSeguros: form.idAreaTramitaSeguros,
            EsUsadoEnCaja: form.EsUsadoEnCaja,
            CodigoHIS: form.CodigoHIS,
            idTipoFinanciador: form.idTipoFinanciador,
            codigo: form.codigo,
            Orden: form.Orden,
            idEstado: form.idEstado,
            fuentefinanciamiento: form.fuentefinanciamiento //almacena el array
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
               :viewRecord="isViewMode"
               :loading="isModalLoading"
               @close="closeDialog"
               @open="handleOpen"
               size="modal-md">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.idTipoFinanciador" :options="appStore.configuracionTipoFinanciador"
                           label="Tipo Financiador" :disabled="isViewMode" filter/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.codigo" label="Codigo" placeholder="Ingrese un codigo"
                           :disabled="isViewMode" :error="errors.codigo"/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.Descripcion" label="Fuente Financiamiento/IAFA"
                           placeholder="Ingrese Descripcion" :disabled="isViewMode" :error="errors.Descripcion"/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.idTipoConceptoFarmacia" :options="appStore.configuracionFarmTipoConceptos"
                           label="Tipo farmacia" :disabled="isViewMode" filter/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.UtilizadoEn" label="Utilizado En" :options="opcionesUtilizadoEn"
                           :disabled="isViewMode" :error="errors.UtilizadoEn" filter/>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.idAreaTramitaSeguros" :options="appStore.configuracionAreaTramitaSeguros"
                           label="Area Tramita Seguros" :disabled="isViewMode" filter/>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-1" style="margin-top: 24px; ">
                <w-checkbox v-model="form.EsUsadoEnCaja"
                            :disabled="isViewMode"
                            label="Usado en Caja">
                </w-checkbox>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.CodigoHIS" label="Codigo Sistema His" placeholder="Ingrese una descripcion"
                           :disabled="isViewMode" :error="errors.CodigoHIS"/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseInput v-model="form.CodigoFuenteFinanciamientoSEM" label="Codigo Sistema SEM"
                           placeholder="Ingrese una descripcion" :disabled="isViewMode"
                           :error="errors.CodigoFuenteFinanciamientoSEM" required/>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-center gap-2">
                    <base-combo label="Tarifario" v-model="IdTipoFinanciamiento"
                                :options="appStore.configuracionTipoFinanciamiento" :show-clear="false" filter
                                :disabled="isViewMode"
                                class="flex-grow-1"></base-combo>
                    <button @click="agregarTipoFinanciamiento" class="btn btn-primary" type="button"
                            style="height: 38px; margin-top: 24px;" :disabled="isViewMode">
                        <i class="ti ti-plus"></i>
                    </button>
                </div>
            </div>
            <div v-if="form.fuentefinanciamiento && form.fuentefinanciamiento.length > 0">
                <table class="table table-sm table-hover">
                    <tbody>
                    <tr v-for="(f, index) in form.fuentefinanciamiento" :key="f.IdTipoFinanciamiento">
                        <td>
                            <div class="salto-de-linea" style="width: 260px">
                                {{
                                    appStore.configuracionTipoFinanciamiento.find(t => t.id ===
                                        f.IdTipoFinanciamiento)?.label
                                }}
                            </div>
                        </td>
                        <td style="text-align: right">
                            <button @click="removerTipoFinanciamiento(index)" class="btn btn-danger btn-sm"
                                    :disabled="isViewMode">
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
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
