<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import axios from 'axios'
import Textarea from 'primevue/textarea'
import Select from 'primevue/select'
import BaseInput from "@/components/WInput/WInput.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue"
import { useAppStore } from '@/stores/useAppStore'
import { useDiagnosticoStore } from '@/stores/useDiagnosticoStore'
import { useInterconsultaStore } from '@/stores/useInterconsultaStore';

const props = defineProps({
    cabecera: { type: Object, required: true },
    diagnosticos: { type: Array, required: true },
    interconsultas: { type: Array, required: true },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean,
    usoCatalogoDX: Boolean
})

const isViewMode = computed(() => props.viewRecord)
const usoCatalogoDX = computed(() => props.usoCatalogoDX)

const appStore = useAppStore()
const diagnosticoStore = useDiagnosticoStore()
const interconsultaStore = useInterconsultaStore();
const listaInterconsultasMedicas = computed(() => interconsultaStore.interconsultas);


// Modo sin catálogo
const idDiagnostico1 = ref(null)
const idDiagnostico2 = ref(null)
const idDiagnostico3 = ref(null)
const diagnosticoOptions = computed(() => diagnosticoStore.diagnosticoOptions)

// Otros campos
const idSolicitud = ref(null)
const idEspecialidad = ref(null)
const motivo = ref('')
const observacion = ref('')

// Diagnósticos
const diagnosticoSeleccionado1 = ref(null)
const diagnosticoSeleccionado2 = ref(null)
const diagnosticoSeleccionado3 = ref(null)

const diagnosticosGlobal = ref([[], [], []])
const loadingDiagnosticoGlobal = ref([false, false, false])
const debounceTimeouts = [null, null, null]
const diagnosticoRefs = [ref(null), ref(null), ref(null)]

// Cargar datos iniciales desde interconsultas
const cargarInterconsultas = () => {
    const lista = Array.isArray(props.interconsultas) ? props.interconsultas : [];

    const listaClonada = lista.map(item => ({
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
        IdMedicoIngreso: parseInt(props.cabecera.IdMedicoIngreso),

        IdSolicitudEspecialidad: parseInt(item.IdSolicitudEspecialidad),
        Solicitud: (item.Solicitud).toUpperCase(),
        IdEspecialidad: parseInt(item.IdEspecialidad),
        Especialidad: (item.Especialidad ?? '').toUpperCase(),
        IdDiagnostico: parseInt(item.IdDiagnostico),
        Diagnostico_1: (item.Diagnostico_1 ?? '').toUpperCase().trim(),
        IdDiagnostico2: parseInt(item.IdDiagnostico2),
        Diagnostico_2: (item.Diagnostico_2 ?? '').toUpperCase().trim(),
        IdDiagnostico3: parseInt(item.IdDiagnostico3),
        Diagnostico_3: (item.Diagnostico_3 ?? '').toUpperCase().trim(),
        Motivo: (item.Motivo ?? '').toUpperCase(),
        IdModalidad: parseInt(item.IdModalidad),
        Modalidad: (item.Modalidad ?? '').toUpperCase(),
        Observacion: (item.Observacion ?? '').toUpperCase()
    }));

    interconsultaStore.setInterconsultas(listaClonada);
};

watch(() => props.interconsultas, cargarInterconsultas, { immediate: true, deep: true });

// ✅ FUNCIÓN: Insertar Interconsulta
const agregarInterconsulta = async () => {
    // Validación
    if (!idEspecialidad.value) {
        showAlert("VALIDACIÓN", "Debe seleccionar una especialidad.", "warning")
        return
    }

    if (!idSolicitud.value) {
        showAlert("VALIDACIÓN", "Debe seleccionar una solicitud de interconsulta.", "warning")
        return
    }

    let dx1 = null
    let dx2 = null
    let dx3 = null

    if (usoCatalogoDX.value) {
        dx1 = diagnosticoSeleccionado1.value?.id
        dx2 = diagnosticoSeleccionado2.value?.id
        dx3 = diagnosticoSeleccionado3.value?.id
    } else {
        dx1 = idDiagnostico1.value
        dx2 = idDiagnostico2.value
        dx3 = idDiagnostico3.value
    } 
    
    if (!dx1) {
        showAlert("VALIDACIÓN", "Debe seleccionar al menos un diagnóstico.", "warning")
        return
    }

    if (!motivo.value || motivo.value.trim().length === 0) {
        showAlert("VALIDACIÓN", "Debe ingresar un motivo de interconsulta.", "warning")
        return
    }

    const payload = {
        IdAtencion: parseInt(props.cabecera.IdAtencion),
        IdCuentaAtencion: parseInt(props.cabecera.IdCuentaAtencion),
        IdPaciente: parseInt(props.cabecera.IdPaciente),
        IdFuenteFinanciamiento: parseInt(props.cabecera.IdFuenteFinanciamiento),
        IdTipoFinanciamiento: parseInt(props.cabecera.IdTipoFinanciamiento),
        IdServicioIngreso: parseInt(props.cabecera.IdServicioIngreso),
        IdMedicoIngreso: parseInt(props.cabecera.IdMedicoIngreso),

        Solicitud: appStore.tablasCache.tiposSolicitudInterconsultaCache.find(s => s.id === idSolicitud.value)?.label.toUpperCase() ?? '',
        IdSolicitud: idSolicitud.value,
        IdEspecialidad: idEspecialidad.value,
        Especialidad: appStore.citaAtencionInterconsultaEspecialidades.find(e => e.id === idEspecialidad.value)?.label.toUpperCase() ?? '',

        IdDiagnostico: dx1,
        Diagnostico_1: obtenerDescripcionDiagnostico(dx1)?.toUpperCase() ?? '',

        IdDiagnostico2: dx2 ?? null,
        Diagnostico_2: obtenerDescripcionDiagnostico(dx2)?.toUpperCase() ?? '',

        IdDiagnostico3: dx3 ?? null,
        Diagnostico_3: obtenerDescripcionDiagnostico(dx3)?.toUpperCase() ?? '',

        Motivo: motivo.value.toUpperCase(),
        Observacion: observacion.value.toUpperCase(),

        IdModalidad: props.cabecera.IdModalidad ?? null,
        Modalidad: (props.cabecera.Modalidad ?? '').toUpperCase()
    }

    try {
        const { data } = await axios.post('/atencion-medica/WebS_Atenciones_Interconsulta_Insertar', payload)
        if (data.success) {
            limpiarCampos()
            interconsultaStore.agregarOActualizarInterconsulta({ ...payload });
        } else {
            showAlert("ERROR", data.mensaje || "Ocurrió un error al insertar", "error")
        }
    } catch (error) {
        console.error("Error al insertar interconsulta:", error)
        showAlert("ERROR", "Error en la comunicación con el servidor", "error")
    }
}

// Función auxiliar para obtener el texto de diagnóstico según su ID
const obtenerDescripcionDiagnostico = (id) => {
    if (!id) return '' 
    const fuente = usoCatalogoDX.value ? [...diagnosticosGlobal.value.flat()] : diagnosticoOptions.value
    return fuente.find(d => usoCatalogoDX.value ? d.id : d.value === id)?.label ?? ''
}


const fetchDiagnosticosGlobal = async (index, query) => {
    if (!query || query.length < 3) {
        diagnosticosGlobal.value[index] = []
        return
    }

    clearTimeout(debounceTimeouts[index])
    debounceTimeouts[index] = setTimeout(async () => {
        loadingDiagnosticoGlobal.value[index] = true
        try {
            const { data } = await axios.post('/filtrar_diagnosticos', { buscar: query })
            diagnosticosGlobal.value[index] = data
        } catch (error) {
            console.error(`Error al buscar diagnóstico ${index + 1}:`, error)
        } finally {
            loadingDiagnosticoGlobal.value[index] = false
        }
    }, 500)
}

const handleFilterDiagnostico = (index, e) => {
    if (e.value.length > 2) {
        fetchDiagnosticosGlobal(index, e.value)
    }
}

const clearAndFocusFilter = (index) => {
    nextTick(() => {
        const refCombo = diagnosticoRefs[index].value
        if (refCombo) {
            refCombo.filterValue = ''
        }
    })
}

const eliminarInterconsulta = async (item) => {
    try {
        const payload = {
            IdSolicitudEspecialidad: parseInt(item.IdSolicitudEspecialidad)
        };

        const { data } = await axios.post('/atencion-medica/WebS_Atenciones_Interconsulta_Eliminar', payload);
        if (data.success) {
            limpiarCampos()
            interconsultaStore.eliminarInterconsulta(item.IdEspecialidad, item.IdAtencion);
        } else {
            alert(data.message || 'Error al eliminar el examen de apoyo.');
        }
    } catch (error) {
        console.error('Error al eliminar:', error);
        alert('Error al eliminar el examen de apoyo.');
    }
};

const limpiarCampos = () => {
    idEspecialidad.value = null
    idSolicitud.value = null
    motivo.value = ''
    observacion.value = ''

    if (usoCatalogoDX.value) {
        diagnosticoSeleccionado1.value = null
        diagnosticoSeleccionado2.value = null
        diagnosticoSeleccionado3.value = null
    } else {
        idDiagnostico1.value = null
        idDiagnostico2.value = null
        idDiagnostico3.value = null
    }

    diagnosticosGlobal.value = [[], [], []]
}

</script>

<template>
    <div class="row align-items-end">
        <!-- Especialidad -->
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
            <BaseCombo v-model="idEspecialidad" :options="appStore.citaAtencionInterconsultaEspecialidades"
                label="Especialidad" placeholder="Seleccione una especialidad" :filter="true" />
        </div>

        <!-- Solicitud -->
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
            <BaseCombo v-model="idSolicitud" :options="appStore.tablasCache.tiposSolicitudInterconsultaCache"
                label="Solicitud de Interconsulta" placeholder="Seleccione una solicitud" />
        </div>
    </div>

    <div class="row align-items-end mt-1">
        <template v-if="usoCatalogoDX">
            <!-- Diagnóstico 1 -->
            <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
                <label class="w-select__label">Diagnóstico 1</label>
                <Select ref="diagnosticoRefs[0]" v-model="diagnosticoSeleccionado1" :options="diagnosticosGlobal[0]"
                    option-label="label" :option-value="(option) => option" filter :autoFilterFocus="true"
                    :showClear="true" :loading="loadingDiagnosticoGlobal[0]" style="width: 100%;"
                    filterPlaceholder="Ingrese al menos 3 caracteres" placeholder="DIAGNÓSTICO" class="w-full"
                    size="small" @filter="(e) => handleFilterDiagnostico(0, e)"
                    @before-show="() => clearAndFocusFilter(0)" />
            </div>

            <!-- Diagnóstico 2 -->
            <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
                <label class="w-select__label">Diagnóstico 2</label>
                <Select ref="diagnosticoRefs[1]" v-model="diagnosticoSeleccionado2" :options="diagnosticosGlobal[1]"
                    option-label="label" :option-value="(option) => option" filter :autoFilterFocus="true"
                    :showClear="true" :loading="loadingDiagnosticoGlobal[1]" style="width: 100%;"
                    filterPlaceholder="Ingrese al menos 3 caracteres" placeholder="DIAGNÓSTICO" class="w-full"
                    size="small" @filter="(e) => handleFilterDiagnostico(1, e)"
                    @before-show="() => clearAndFocusFilter(1)" />
            </div>

            <!-- Diagnóstico 3 -->
            <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
                <label class="w-select__label">Diagnóstico 3</label>
                <Select ref="diagnosticoRefs[2]" v-model="diagnosticoSeleccionado3" :options="diagnosticosGlobal[2]"
                    option-label="label" :option-value="(option) => option" filter :autoFilterFocus="true"
                    :showClear="true" :loading="loadingDiagnosticoGlobal[2]" style="width: 100%;"
                    filterPlaceholder="Ingrese al menos 3 caracteres" placeholder="DIAGNÓSTICO" class="w-full"
                    size="small" @filter="(e) => handleFilterDiagnostico(2, e)"
                    @before-show="() => clearAndFocusFilter(2)" />
            </div>
        </template>

        <template v-else>
            <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
                <BaseCombo v-model="idDiagnostico1" :options="diagnosticoOptions" label="Diagnóstico" />
            </div>
            <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
                <BaseCombo v-model="idDiagnostico2" :options="diagnosticoOptions" label="Diagnóstico" />
            </div>
            <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
                <BaseCombo v-model="idDiagnostico3" :options="diagnosticoOptions" label="Diagnóstico" />
            </div>
        </template>
    </div>

    <div class="row mt-1">
        <div class="col-xl-5 col-lg-5 col-md-6 col-sm-12 d-flex flex-column justify-content-end">
            <label class="w-select__label">Motivo</label>
            <Textarea v-model="motivo" rows="1" autoResize class="w-100" />
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 d-flex flex-column justify-content-end">
            <label class="w-select__label">Observación</label>
            <Textarea v-model="observacion" rows="1" autoResize class="w-100" />
        </div>
        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 d-flex align-items-end">
            <button @click="agregarInterconsulta" type="button" class="btn btn-primary w-100">
                <i class="fas fa-plus-circle me-1"></i> Agregar
            </button>
        </div>
    </div>

    <div v-if="listaInterconsultasMedicas.length > 0" style="overflow-x: auto;">
        <table class="table table-sm table-bordered-bottom table-bordered-top table-hover mt-3 "
            style="min-width: 1200px; table-layout: fixed;">
            <thead class="align-middle text-center table-light">
                <tr>
                    <th class="py-1" style="width: 200px;">ESPECIALIDAD</th>
                    <th class="py-1" style="width: 180px;">SOLICITUD</th>
                    <th class="py-1" style="width: 200px;">DIAGNÓSTICO 1</th>
                    <th class="py-1" style="width: 200px;">DIAGNÓSTICO 2</th>
                    <th class="py-1" style="width: 200px;">DIAGNÓSTICO 3</th>
                    <th class="py-1" style="width: 40px;"></th>
                </tr>
            </thead>
            <tbody>
                <template v-for="(item, index) in listaInterconsultasMedicas" :key="`interconsulta-${index}`">
                    <!-- Fila principal -->
                    <tr :class="[{ 'bg-light-gray': index % 2 !== 0 }]">
                        <td class="py-1">
                            <BaseInput :value="item.Especialidad" disabled class="form-control-sm" />
                        </td>
                        <td class="py-1">
                            <BaseInput :value="item.Solicitud" disabled class="form-control-sm" />
                        </td>
                        <td class="py-1">
                            <BaseInput :value="item.Diagnostico_1" disabled class="form-control-sm" />
                        </td>
                        <td class="py-1">
                            <BaseInput :value="item.Diagnostico_2" disabled class="form-control-sm" />
                        </td>
                        <td class="py-1">
                            <BaseInput :value="item.Diagnostico_3" disabled class="form-control-sm" />
                        </td>
                        <td class="py-1 text-center align-top">
                            <i class="fas fa-times text-secondary cursor-pointer" style="font-size: 1.2rem;"
                                @click="eliminarInterconsulta(item)" title="Eliminar" />
                        </td>
                    </tr>

                    <!-- Segunda fila: siempre visible pero con contenido condicional -->
                    <tr :class="[{ 'bg-light-gray': index % 2 !== 0 }]">
                        <td class="py-1" colspan="3">
                            <div v-if="item.Motivo?.trim()">
                                <Textarea v-model="item.Motivo" autoResize disabled class="form-control" rows="1" />
                            </div>
                        </td>
                        <td class="py-1" colspan="2">
                            <div v-if="item.Observacion?.trim()">
                                <Textarea v-model="item.Observacion" autoResize disabled class="form-control"
                                    rows="1" />
                            </div>
                        </td>
                        <td></td>
                    </tr>
                </template>
            </tbody>

        </table>
    </div>
</template>
