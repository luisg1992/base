<script setup>
import axios from 'axios';
import { ref, computed } from 'vue';
import Select from "primevue/select";
import Textarea from 'primevue/textarea';
import { useAppStore } from '@/stores/useAppStore'
import BaseCombo from "@/components/WSelect/WSelect.vue";

const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

const isViewMode = computed(() => props.viewRecord)
const appStore = useAppStore()

const idDestinoAtencion = ref(null)
const idServicioDestino = ref(null)
const idEstablecimiento = ref(null)
const idServicioReferencia = ref(null)
const idEspecialidadesReferencia = ref(null)
const idMotivoReferencia = ref(null)
const idCondicionReferencia = ref(null)

const establecimientoSelectRef = ref(null);
const serviciosDestino = ref([])
const otrasObservaciones = ref('')

const establecimientos = ref([]);
const servicios = ref([]);
const especialidades = ref([]);
const condiciones = ref([]);

const loadingEstablecimientos = ref(false);
const debounceTimeout = ref(null);
const mostrarDatosReferencia = ref(false);

const destinosFiltrados = computed(() => {
    return appStore.tablasCache.tiposDestinoAtencionCache.filter(
        item => item.idTipoServicio == props.IdTipoServicio
    )
})

// Buscar órdenes médicas (Establecimientos)
const fetchOptionsEstablecimientos = async (query) => {
    if (!query) {
        establecimientos.value = [];
        return;
    }
    loadingEstablecimientos.value = true;
    try {
        const { data } = await axios.post('/filtrar_establecimientos', {
            Filtro: query
        });
        establecimientos.value = data;
    } catch (error) {
        console.error('Error al cargar opciones CPT:', error);
    } finally {
        loadingEstablecimientos.value = false;
    }
};

const onEstablecimientoChange = async (id) => {
    mostrarDatosReferencia.value = false;
    const item = establecimientos.value.find(e => e.value === id);
    if (!item) return;

    console.log('Establecimiento seleccionado (por @change):', item);
    try {
        servicios.value = []
        especialidades.value = []
        condiciones.value = []
        loadingEstablecimientos.value = true;
        condiciones.value = appStore.tablasCache.refconCondiContraRefCache.filter(item => item.tipo === 1);


        // Cargar servicios
        const { data: serviciosData } = await axios.post('/refcon/listadoUps', {
            Codigo: item.Codigo,
        });
        servicios.value = serviciosData.data?.codigos_servicios_ups.map(e => ({
            label: `${e.codUps} = ${e.descripcion}`,
            value: e.codUps,
        }));

        // Cargar especialidades
        const { data: especialidadesData } = await axios.post('/refcon/listadoEspecialidades', {
            Codigo: item.Codigo,
        });
        especialidades.value = especialidadesData.data?.motivoReferencias.map(e => ({
            label: `${e.codigo} = ${e.esp_descripcion}`,
            value: e.codigo,
        }));
    } catch (error) {
        console.error('Error al cargar datos del establecimiento:', error);
    } finally {
        mostrarDatosReferencia.value = true;
        loadingEstablecimientos.value = false;
    }
};

const onFilterEstablecimientos = (event) => {
    if (event.value.length >= 3) {
        clearTimeout(debounceTimeout.value);
        debounceTimeout.value = setTimeout(() => {
            fetchOptionsEstablecimientos(event.value);
        }, 500);
    }
};

function agregarDestinoAtencion() {
    console.log('Destino seleccionado (agregar):', idDestinoAtencion.value)
    console.log('Servicio destino:', idServicioDestino.value)
    console.log('Observaciones:', otrasObservaciones.value)
}

function onDestinoAtencionChange(value) {
    idServicioDestino.value = null

    if (value === 11) {
        serviciosDestino.value = appStore.configuracionServicio.filter(item => item.IdTipoServicio === 3)
    } else if (value === 54) {
        serviciosDestino.value = appStore.configuracionServicio.filter(item => item.IdTipoServicio === 2)
    } else {
        serviciosDestino.value = []
    }

    console.log('Seleccionaste destino con id:', value)
}
</script>

<template>
    <div class="row g-3 align-items-end">

        <div class="row">
            <!-- Destino de Atencion -->
            <div class="col-xl-4 col-lg-8 col-md-12">
                <BaseCombo v-model="idDestinoAtencion" :options="destinosFiltrados" label="Destino de Atención"
                    @update:modelValue="onDestinoAtencionChange" :filter="true" />
            </div>
        </div>

        <!-- ALTA A HOSPITALIZACION -->
        <div class="row" v-show="idDestinoAtencion === 11">
            <div class="col-xl-4 col-lg-8 col-md-12">
                <BaseCombo v-model="idServicioDestino" :options="serviciosDestino" label="Servicios de Hospitalización"
                    :filter="true" />
            </div>
        </div>

        <!-- ALTA A Emergencia -->
        <div class="row" v-show="idDestinoAtencion === 54">
            <div class="col-xl-4 col-lg-8 col-md-12">
                <BaseCombo v-model="idServicioDestino" :options="serviciosDestino" label="Servicios de Emergencia"
                    :filter="true" />
            </div>
        </div>

        <!-- REFERENCIAS -->
        <div class="row" v-show="idDestinoAtencion === 12">
            <!-- Select de Establecimientos -->
            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12">
                <label class="w-select__label">Establecimientos</label>
                <Select ref="establecimientoSelectRef" v-model="idEstablecimiento" :options="establecimientos"
                    option-label="label" option-value="value" filter
                    filterPlaceholder="Ingrese como mínimo 3 caracteres" @filter="onFilterEstablecimientos"
                    @update:modelValue="onEstablecimientoChange" :loading="loadingEstablecimientos"
                    placeholder="Seleccione una opción" :showClear="true" class="w-full" size="small"
                    style="width: 100%;" :autoFilterFocus="true" />
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12" v-if="mostrarDatosReferencia">
                <BaseCombo v-model="idServicioReferencia" :options="servicios" label="Servicios" :filter="true"
                    :disabled="loadingEstablecimientos" :showClear="true" :autoFilterFocus="true" />
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12" v-if="mostrarDatosReferencia">
                <BaseCombo v-model="idEspecialidadesReferencia" :options="especialidades" label="Especialidades"
                    :disabled="loadingEstablecimientos" :showClear="true" :autoFilterFocus="true" :filter="true" />
            </div>

            <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12" v-if="mostrarDatosReferencia">
                <BaseCombo v-model="idMotivoReferencia" :options="appStore.tablasCache.refconMotivoContraRefCache"
                    label="Motivo" placeholder="Seleccione un Motivo" :filter="true" />
            </div>

            <!-- Solicitud -->
            <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12" v-if="mostrarDatosReferencia">
                <BaseCombo v-model="idCondicionReferencia" :options="condiciones" label="Codición"
                    placeholder="Seleccione una Condición" :filter="true" />
            </div>
        </div>


        <div class="row" v-show="idDestinoAtencion === 13">CONTRAREFERENCIA</div>
        <div class="row" v-show="idDestinoAtencion === 60">CITADO</div>
        <div class="row" v-show="idDestinoAtencion === 61">APOYO AL DX</div>
        <div class="row" v-show="idDestinoAtencion === 62">FALLECIDO</div>
        <div class="row" v-show="idDestinoAtencion === 70">REFERENCIA DE APOYO AL DX</div>

        <!-- Observaciones -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <label class="form-label">Otras Observaciones</label>
                <Textarea v-model="otrasObservaciones" rows="4" autoResize class="w-100" />
            </div>
        </div>

        <!-- Botón de acción -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <button @click="agregarDestinoAtencion" type="button" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle me-1"></i> CERRAR ATENCIÓN MÉDICA
                </button>
            </div>
        </div>

    </div>
</template>
