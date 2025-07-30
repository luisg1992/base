<script setup>

import {computed, onBeforeUnmount, onMounted, ref} from "vue";
import axios from 'axios';
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import {useForm} from '@inertiajs/vue3';
import BaseModalFull from "../../../components/BaseModalFull.vue";
import {useAppStore} from "../../../stores/useAppStore.js";
import BaseDatePicker from "@/components/WDatePicker/WDatePicker.vue";
import Select from 'primevue/select';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputNumber from 'primevue/inputnumber';
import CatalogoForm from "./Parcial/CatalogoForm.vue";

const props = defineProps(['showDialog', 'recordId', 'viewRecord']);
const emit = defineEmits(['success', 'update:showDialog']);

let appStore = useAppStore();
let resource = 'farmacia/notas_ingresos_almacen';
let refDialogCatalogoForm = ref()
let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let loadingProveedor = ref(false);
let title = ref('');
let errors = ref({});
let form = useForm({
    id: null,
    MovNumero: null,
    idAlmacenOrigen: null,
    idAlmacenDestino: null,
    idTipoConcepto: null,
    DocumentoIdtipo: null,
    DocumentoNumero: null,
    idProveedor: null,
    concepto: null,
    fechaRegistro: null,
    horaRegistro: null,
    productos: [],
    Observaciones: null,
    idEstadoMovimiento: null,
})
let proveedores = ref([]);
let tipoConceptos = ref([]);
let origenAlmacenes = ref([]);
let destinoAlmacenes = ref([]);
let tipoConceptoDetalle = ref(null);
let debounceTimeout = null;
let intervalo = null;
let isViewMode = computed(() => props.viewRecord)

const initForm = () => {
    errors.value = {};
    form.reset();
    form.clearErrors();
}

const handleOpen = async () => {
    title.value = `Nueva nota de ingreso`;
    // $q.loading.show();
    initForm()
    if (props.recordId) {
        const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
        Object.assign(form, response.data.data);
        await changeAlmacenDestino();
        await changeTipoConcepto();
        //     await axios.get(`/${resource}/record/${props.recordId}`)
        //         .then(response => {
        //             form.value = Object.assign({}, form.value, response.data.data);
        //         })
        //         .catch(error => {
        //             console.log(error);
        //         })
        //     if (props.viewRecord) {
        //         title.value = `Ver nota de ingreso`;
        //     } else {
        //         title.value = `Editar nota de ingreso`;
        //     }
        title.value = `Editar nota de ingreso`;
    }
    destinoAlmacenes.value = appStore.farmaciaAlmacenes.filter(row => row.idTipoLocales === 'A');
    // $q.loading.hide();
}

const changeAlmacenDestino = async () => {
    console.log('changeAlmacenDestino');
    //form.idTipoConcepto = null;
    tipoConceptos.value = [];
    let almacenDestino = appStore.farmaciaAlmacenes.find(row => row.value === form.idAlmacenDestino);
    if (almacenDestino) {
        const response = await axios.post('/filtrar_tipo_de_conceptos', {
            'TipoAlmacen': 'A',
            'TipoMov': 'E',
            'TipoSuministro': almacenDestino.idTipoSuministro
        });
        tipoConceptos.value = response.data;
    }
}

const changeTipoConcepto = async () => {
    console.log('changeTipoConcepto');
    let tipoConcepto = tipoConceptos.value.find(row => row.value === form.idTipoConcepto);

    if (tipoConcepto) {
        form.DocumentoIdtipo = parseInt(tipoConcepto.DocumentoId)
        form.DocumentoOrigenIdtipo = parseInt(tipoConcepto.NiDocumentoOrigenId)
        await filtrarAlmacenOrigen(tipoConcepto);
    }
}

const filtrarAlmacenOrigen = async (tipoConcepto) => {
    console.log(tipoConcepto);
    console.log('filtrarAlmacenOrigen');
    const response = await axios.post('/filtrar_almanacenes_por_tipo_de_conceptos', {
        'filtro': tipoConcepto.NiFiltroAlmacenOrigen,
    });
    origenAlmacenes.value = response.data;
}

const fetchOptionsProveedor = async (query) => {
    if (!query) {
        proveedores.value = [];
        return;
    }
    loadingProveedor.value = true;
    try {
        const response = await axios.post('/filtrar_proveedores', {buscar: query});
        proveedores.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingProveedor.value = false;
    }
}

const onFilterProveedor = (event) => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        fetchOptionsProveedor(event.value);
    }, 500);
}

const onSubmit = async () => {
    // $q.loading.show();
    // loadingSubmit.value = true;
    // await axios.post(`/${resource}`, form.value)
    //     .then(response => {
    //         let data = response.data;
    //         if (data.success) {
    //             $q.notify({type: 'positive', icon: 'fa-light fa-check', message: data.message});
    //             emit('success', data.data);
    //             closeDialog();
    //         } else {
    //             $q.notify({type: 'negative', icon: 'fa-light fa-xmark', message: data.message});
    //         }
    //     })
    //     .catch(err => {
    //         errors.value = err;
    //     })
    //     .finally(() => {
    //         loadingSubmit.value = false;
    //         $q.loading.hide();
    //     })
}

const actualizarFechaHora = () => {
    if (!props.recordId) {
        const ahora = new Date();
        const dia = ahora.getDate().toString().padStart(2, '0');
        const mes = (ahora.getMonth() + 1).toString().padStart(2, '0');
        const anio = ahora.getFullYear();
        form.fechaRegistro = `${dia}/${mes}/${anio}`;
        form.horaRegistro = ahora.toLocaleTimeString();
    }
}

const clickAgregarCatalogo = () => {
    refDialogCatalogoForm.value.openDialog()
}

const successCatalogo = (data) => {
    form.productos.push(data)
}

const removeCatalogo = (index) => {
    form.productos.splice(index, 1)
}

const calcularTotal = (data, index) => {
    form.productos[index]['Total'] = Number((Number(data.Cantidad) * Number(data.Precio)).toFixed(2));
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

const openDialog = () => {
    isDialogOpen.value = true;
}

onMounted(() => {
    actualizarFechaHora();
    intervalo = setInterval(actualizarFechaHora, 1000);
});

onBeforeUnmount(() => {
    clearInterval(intervalo);
});

defineExpose({openDialog});

</script>

<template>

    <base-modal-full :isVisible="isDialogOpen"
                     :recordId="form.id"
                     :viewRecord="isViewMode"
                     :loadingSubmit="loadingSubmit"
                     :title="title"
                     @close="closeDialog"
                     @open="handleOpen"
                     size="modal-lg">
        <form @submit.prevent="onSubmit">
            <div class="row">
                <div class="col-12 col-md-7">
                    <div class="row q-col-gutter-sm">
                        <div class="col-12 col-sm-4">
                            <base-input label="Número Nota Ingreso"
                                        v-model="form.MovNumero"
                                        :error="form.errors.MovNumero"
                                        :readonly="true"></base-input>
                        </div>
                        <div class="col-12 col-sm-4">
                            <base-date-picker label="F.Registro"
                                              v-model="form.fechaRegistro"
                                              :readonly="true"/>
                        </div>
                        <div class="col-12 col-sm-4">
                            <base-input label="H.Registro"
                                        v-model="form.horaRegistro"
                                        :readonly="true"/>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-combo label="Almacén destino"
                                        v-model="form.idAlmacenDestino"
                                        :options="destinoAlmacenes"
                                        :readonly="props.viewRecord"
                                        @update:model-value="changeAlmacenDestino"></base-combo>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-combo label="Concepto"
                                        v-model="form.idTipoConcepto"
                                        :options="tipoConceptos"
                                        :readonly="props.viewRecord"
                                        @update:model-value="changeTipoConcepto"></base-combo>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-combo label="Origen"
                                        v-model="form.idAlmacenOrigen"
                                        :options="origenAlmacenes"
                                        :readonly="props.viewRecord"></base-combo>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-combo label="Tipo documento"
                                        v-model="form.DocumentoIdtipo"
                                        :options="appStore.farmaciaTipoDocumentos"
                                        :readonly="props.viewRecord"></base-combo>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-combo label="Tipo documento origen"
                                        v-model="form.DocumentoOrigenIdtipo"
                                        :options="appStore.farmaciaTipoDocumentos"
                                        :readonly="props.viewRecord"></base-combo>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-combo label="Tipo de compra"
                                        v-model="form.concepto"
                                        :options="appStore.farmaciaTipoCompras"
                                        :readonly="props.viewRecord"></base-combo>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <div class="row q-col-gutter-sm">
                        <div class="col-12">
                            <label class="form-label">Proveedor</label>
                            <Select v-model="form.idProveedor"
                                    :options="proveedores"
                                    optionLabel="label"
                                    optionValue="value"
                                    filter
                                    filterPlaceholder="Buscar..."
                                    @filter="onFilterProveedor"
                                    :loading="loadingProveedor"
                                    placeholder="Seleccione una opción"
                                    :showClear="true"
                                    class="w-full"
                                    size="small"
                                    style="width: 100%;"></Select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-input label="Nro.Documento"
                                        v-model="form.DocumentoNumero"
                                        :readonly="props.viewRecord"></base-input>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-input label="Nro.Entrega"
                                        v-model="form.numero"
                                        :readonly="props.viewRecord"></base-input>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-combo label="T/proceso"
                                        v-model="form.concepto"
                                        :options="appStore.farmaciaTipoProcesos"
                                        :readonly="props.viewRecord"></base-combo>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-combo label="Estado"
                                        v-model="form.idEstadoMovimiento"
                                        :options="appStore.farmaciaEstadoMovimientos"
                                        :disabled="true"
                                        :show-clear="false"></base-combo>
                        </div>
                        <div class="col-12 col-sm-4">
                            <base-date-picker label="F.Recepción"
                                              v-model="form.fecha"
                                              :readonly="props.viewRecord"/>
                        </div>
                        <div class="col-12 col-sm-4">
                            <base-date-picker label="F.Doc.Origen"
                                              v-model="form.fecha"
                                              :readonly="props.viewRecord"/>
                        </div>
                        <div class="col-12 col-sm-4">
                            <base-input label="Nro.Proceso"
                                        v-model="form.numero"
                                        :readonly="props.viewRecord"></base-input>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <div class="col-9">
                                    <base-input label="Observaciones"
                                                v-model="form.Observaciones"
                                                :readonly="props.viewRecord"></base-input>
                                </div>
                                <div class="col-3" style="display: flex; align-items: flex-end">
                                    <button @click="clickAgregarCatalogo"
                                            class="btn btn-primary">Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12" style="margin-top: 24px">
                    <DataTable :value="form.productos"
                               tableStyle="min-width: 50rem"
                               class="x-tabla-catalogo"
                               v-if="form.productos.length > 0">
                        <Column field="Nombre" header="Nombre"></Column>

                        <Column field="Cantidad" header="Cantidad" style="width: 160px">
                            <template #body="slotProps">
                                <InputNumber v-model="slotProps.data.Cantidad"
                                             show-buttons
                                             buttonLayout="horizontal"
                                             :min="1"
                                             fluid
                                             @update:modelValue="calcularTotal(slotProps.data, slotProps.index)">
                                </InputNumber>
                            </template>
                        </Column>

                        <Column field="Precio" header="Precio">
                            <template #body="slotProps">
                                <div style="text-align: right">{{ slotProps.data.Precio }}</div>
                            </template>
                        </Column>

                        <Column field="Total" header="Total">
                            <template #body="slotProps">
                                <div style="text-align: right">{{ slotProps.data.Total }}</div>
                            </template>
                        </Column>

                        <Column field="Lote" header="Lote" style="width: 140px">
                            <template #body="slotProps">
                                <base-input v-model="slotProps.data.Lote"></base-input>
                            </template>
                        </Column>

                        <Column field="RegistroSanitario" header="Registro Sanitario" style="width: 140px">
                            <template #body="slotProps">
                                <base-input v-model="slotProps.data.RegistroSanitario"></base-input>
                            </template>
                        </Column>

                        <Column field="acciones" header="" style="width: 60px">
                            <template #body="slotProps">
                                <button @click="removeCatalogo(slotProps.index)"
                                        class="btn btn-danger"
                                        :disabled="loadingSubmit">
                                    <i class="ti ti-trash"></i></button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
            <div style="position: absolute; padding: 20px;
            background-color: #ccc; bottom: 0; right: 0; width: 100%;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            text-align: right;">
                <div>
                    <button @click="closeDialog"
                            class="btn btn-secondary"
                            :disabled="loadingSubmit">Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary"
                            style="margin-left: 8px;"
                            :disabled="loadingSubmit">
                        {{ loadingSubmit ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </form>
        <catalogo-form ref="refDialogCatalogoForm"
                       @success="successCatalogo"></catalogo-form>
    </base-modal-full>
</template>
