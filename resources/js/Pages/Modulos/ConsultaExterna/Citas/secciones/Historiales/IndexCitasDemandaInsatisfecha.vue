<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import BaseCombo from "@/components/WSelect/WSelect.vue";
import BasePaginatorHTML from '@/components/BasePaginatorHTML.vue';
import { useAppStore } from '@/stores/useAppStore'
import BaseDatePicker from "@/components/WDatePicker/WDatePicker.vue";
import CuposDisponibles from '../Programacion/CuposDisponibles.vue'

const props = defineProps(['moduloActual']);

let appStore = useAppStore();
let records = ref([])
let isModalLoading = ref(false)
let currentPage = ref(1)
let perPage = ref(50)
let totalRecords = ref(0)
let IdEspecialidad = ref(null)
let itemSeleccion = ref(null)

let FechaFiltro = ref(null)
let BusquedaPaciente = ref(null)


const cuposDisponiblesRef = ref(null)
const totalPaginas = computed(() => {
    return Math.ceil(totalRecords.value / perPage.value)
})

const cambiarPagina = (pagina) => {
    if (pagina >= 1 && pagina <= totalPaginas.value) {
        currentPage.value = pagina
        obtenerDemaInsatisfecha()
    }
}

const obtenerDemaInsatisfecha = async () => {
    isModalLoading.value = true;
    records.value = [];
    const pacienteLimpio = BusquedaPaciente.value ? BusquedaPaciente.value.replace(/\s+/g, '') : null;
    BusquedaPaciente.value = pacienteLimpio;

    const tieneEspecialidad = !!IdEspecialidad.value;
    const tieneFecha = !!FechaFiltro.value;
    const tieneDocumento = !!pacienteLimpio;

    // Validación: al menos un campo debe estar lleno
    if (!tieneEspecialidad && !tieneFecha && !tieneDocumento) {
        isModalLoading.value = false;
        return showAlert(
            'VALIDACIÓN DE CAMPOS',
            'Debe completar al menos uno de los campos de filtro: Especialidad, Fecha o Documento.',
            'warning'
        );
    }

    try {
        const payload = {
            IdEspecialidad: IdEspecialidad.value || null,
            Page: currentPage.value,
            PerPage: perPage.value,
            Fecha: tieneDocumento ? null : FechaFiltro.value,
            DocumentoHC: tieneDocumento ? pacienteLimpio : null
        };

        const { data } = await axios.post('/consulta-externa/citas/WebS_DemandaInsatisfecha_Lista_BuscarFiltro', payload);

        records.value = data.CitasDemandaInsatisfecha;
        totalRecords.value = data.Total ?? 0;
    } catch (error) {
        console.error('Error al cargar las citas:', error);
    } finally {
        isModalLoading.value = false;
    }
};



const success = async () => {
    records.value = records.value.map(item => {
        if (item.IdDemaInsatisfecha === itemSeleccion.value.IdSeleccion) {
            return {
                ...item,
                Estado: 2 // Cambiar a CITADO
            };
        }
        return item;
    });
};

const onChangeEspecialidad = () => {
    currentPage.value = 1
    obtenerDemaInsatisfecha()
}

const clearChangeEspecialidad = () => {
    BusquedaPaciente.value = '';
    obtenerDemaInsatisfecha()
};

const consultarSisPaciente = async (demandaIns) => {
    try {
        showAlert("VERIFICANDO AFILIACIÓN SIS ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        const response = await axios.post('/core/servicios/obtenerDatosSisCompletos', {
            numerodocumento: demandaIns.NroDocumento,
            tipodocumento: demandaIns.IdDocIdentidad,
            consultareniec: true,
            tipopersona: 0,
            modulo_origen: 'admicion_ce',
        });

        showAlert(
            "CONSULTA REALIZADA",
            "LA CONSULTA FUE REALIZADA DE FORMA EXITOSA",
            "success"
        );

        IdEspecialidad.value = demandaIns.IdEspecialidad
        if (response?.data.respuesta == 1) {
            itemSeleccion.value = {
                Tipo: 'DemandaInsatisfecha',
                IdPaciente: demandaIns.IdPaciente,
                IdSeleccion: demandaIns.IdDemaInsatisfecha,
                idfuentefinanciamiento: 3,
                IdTipoFinanciamiento: 2
            }

            cuposDisponiblesRef.value.openDialog()
        } else {
            itemSeleccion.value = {
                Tipo: 'DemandaInsatisfecha',
                IdPaciente: demandaIns.IdPaciente,
                IdSeleccion: demandaIns.IdDemaInsatisfecha,
                idfuentefinanciamiento: 1,
                IdTipoFinanciamiento: 1
            }

            cuposDisponiblesRef.value.openDialog()
        }
    } catch (error) {
        console.error('Error al obtener datos del SIS:', error);
        return null;
    }
}

function getEstadoInfo(estado) {
    const estados = {
        0: { label: 'ANULADO', class: 'badge bg-label-danger' },
        1: { label: 'PENDIENTE', class: 'badge bg-label-warning' },
        2: { label: 'CITADO', class: 'badge bg-label-success' }
    };
    return estados[estado] || { label: 'DESCONOCIDO', class: 'badge bg-label-secondary' };
}
</script>

<template>
    <ModalLoader v-if="isModalLoading" />
    <div class="row">
        <div class="col-md-3">
            <BaseCombo v-model="IdEspecialidad" :options="appStore.citaAtencionInterconsultaEspecialidades"
                label="Especialidad" option-label="label" option-value="id" :filter="true"
                placeholder="Seleccione una Especialidad" @update:modelValue="onChangeEspecialidad" />
        </div>

        <div class="col-md-3">
            <BaseDatePicker v-model="FechaFiltro" label="Fecha Solicitud" placeholder="Selecciona la fecha" view="month"
                dateFormat="mm/yy" @update:modelValue="onChangeEspecialidad" />
        </div>

        <div class="col-md-6 mt-1">
            <label for="sis" class="mb-0 me-2">BÚSQUEDA DE PACIENTE POR HC O DOCUMENTO</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" placeholder="Buscar por HC o DOCUMENTO ... "
                    v-model="BusquedaPaciente" @keyup.enter="onChangeEspecialidad" />
                <button class="btn btn-primary" @click="onChangeEspecialidad">
                    <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-danger" @click="clearChangeEspecialidad">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div v-if="records.length" class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p style="font-size: 12px;" class="mb-0">
                        Se han encontrado <b>({{ records.length }})</b> Registros(s)
                    </p>

                    <!-- Paginación alineada a la derecha -->
                    <BasePaginatorHTML :currentPage="currentPage" :totalRecords="totalRecords" :perPage="perPage"
                        :totalPaginas="totalPaginas" @cambiarPagina="cambiarPagina" />
                </div>

                <table class="table table-sm table-hover dataTable no-footer dtr-inline mt-2"
                    style="table-layout: auto;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Paciente</th>
                            <th>Edad </th>
                            <th>Destino</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th class="text-center"
                                v-if="$can(`${moduloActual}.tab.demanda.insatisfecha.registrar.cita`)">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in records" :key="item.IdDemaInsatisfecha">
                            <td style="width: 1%;">{{ index + 1 }}</td>
                            <td style="width: 5%;">
                                {{ item.NumeroDocumento }} - HC: {{ item.HistoriaClinica }} <br>
                                {{ item.Paciente }}
                            </td>
                            <td style="width: 5%;">
                                <b>{{ item.Edad }}</b>
                            </td>
                            <td style="width: 5%;">
                                <b>{{ item.Especialidad }}</b>
                            </td>
                            <td style="width: 2%;">
                                {{ item.FechaRegistro }} <br>
                                {{ item.Empleado }}
                            </td>
                            <td style="width: 3%;">
                                <span :class="getEstadoInfo(item.Estado).class">
                                    {{ getEstadoInfo(item.Estado).label }}
                                </span>
                            </td>
                            <td style="width: 2%;" class="text-center">
                                <button class="btn btn-xs btn-icon btn-primary" @click="consultarSisPaciente(item)"
                                    v-if="$can(`${moduloActual}.tab.demanda.insatisfecha.registrar.cita`) && item.Estado == 1">
                                    <i class="ti ti-calendar text-white"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>


            </div>
            <div v-else class="alert alert-info mt-3 mb-0 text-center">
                No cuenta con pacientes con demanda insatisfecha para mostrar.
            </div>
        </div>
    </div>

    <CuposDisponibles ref="cuposDisponiblesRef" :recordId="IdEspecialidad" :itemSeleccion="itemSeleccion"
        @success="success()" />
</template>
