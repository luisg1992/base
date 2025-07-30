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

let isViewMode = computed(() => props.viewRecord)
let errors = ref({});

const form = useForm({
    Descripcion: '',
    esOficina: false,
    esSalida: false,
    SeIngresPrecios: false,
    EsFarmacia: false,
    idCajaTiposComprobante: null,
    tipoVenta: '',
    SeImprimeComprobante: false,
    esFuenteFinanciamiento: false,
    GeneraPago: null,
    idTipoConcepto: null,
    Estado: 1
})

const opcionesGeneraPago = ref([
    {value: 0, label: 'Ninguno'},
    {value: 1, label: 'Trabaja solo Particulares'},
    {value: 2, label: 'Trabaja solo Seguro SIS'},
    {value: 3, label: 'Trabaja solo Seguro SOAT'},
    {value: 4, label: 'Trabaja solo Seguro Convenios'}

]);

const opcionestipoVenta = ref([
    {value: "D", label: 'Venta Directa con IAFFA'},
    {value: "N", label: 'Venta Directa sin IAFFA'},
    {value: "P", label: 'Preventa'},

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
            esOficina: form.esOficina,
            esSalida: form.esSalida,
            SeIngresPrecios: form.SeIngresPrecios,
            EsFarmacia: form.EsFarmacia,
            idCajaTiposComprobante: form.idCajaTiposComprobante,
            tipoVenta: form.tipoVenta,
            SeImprimeComprobante: form.SeImprimeComprobante,
            esFuenteFinanciamiento: form.esFuenteFinanciamiento,
            GeneraPago: form.GeneraPago,
            idTipoConcepto: form.idTipoConcepto,
            Estado: form.Estado

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
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 ">
                <BaseInput v-model="form.Descripcion" label="Descripcion" placeholder="Ingrese un Descripcion"
                           :disabled="isViewMode" :error="errors.Descripcion"/>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-1" style="margin-top: 24px; ">
                <w-checkbox v-model="form.esOficina"
                            :disabled="isViewMode"
                            label="Oficina">
                </w-checkbox>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-1" style="margin-top: 24px; ">
                <w-checkbox v-model="form.esSalida"
                            :disabled="isViewMode"
                            label="Salida">
                </w-checkbox>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.GeneraPago" label="Genera Pago" :options="opcionesGeneraPago"
                           :disabled="isViewMode" :error="errors.UtilizadoEn"/>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.tipoVenta" label="Tipo Venta" :options="opcionestipoVenta"
                           :disabled="isViewMode" :error="errors.UtilizadoEn"/>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-1" style="margin-top: 24px; ">
                <w-checkbox v-model="form.SeIngresPrecios"
                            :disabled="isViewMode"
                            label="Se ingresa Precios">
                </w-checkbox>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-1" style="margin-top: 24px; ">
                <w-checkbox v-model="form.EsFarmacia"
                            :disabled="isViewMode"
                            label="Farmacia">
                </w-checkbox>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.idCajaTiposComprobante"
                           :options="appStore.configuracionCajaTipoComprobante"
                           label="Tipo Financiador" :disabled="isViewMode" filter/>
            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-1" style="margin-top: 24px; ">
                <w-checkbox v-model="form.SeImprimeComprobante"
                            :disabled="isViewMode"
                            label="Imprime Comprobante">
                </w-checkbox>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-1" style="margin-top: 24px; ">
                <w-checkbox v-model="form.esFuenteFinanciamiento"
                               :disabled="isViewMode" label="Fuente Financiamiento">
                </w-checkbox>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <BaseCombo v-model="form.idTipoConcepto" :options="appStore.configuracionFarmTipoConceptos"
                           required label="Tipo Financiador" :disabled="isViewMode" filter/>
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
