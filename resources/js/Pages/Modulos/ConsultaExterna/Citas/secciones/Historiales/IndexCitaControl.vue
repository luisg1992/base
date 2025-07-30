<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

import BaseCombo from "@/components/WSelect/WSelect.vue";
import BasePaginatorHTML from '@/components/BasePaginatorHTML.vue';
import { useAppStore } from '@/stores/useAppStore'
import CuposDisponibles from '../Programacion/CuposDisponibles.vue'

const props = defineProps(['moduloActual']);

let appStore = useAppStore();
let CitasControl = ref([])
let isModalLoading = ref(false)
let currentPage = ref(1)
let perPage = ref(50)
let totalRegistros = ref(0)
let IdEspecialidad = ref(null)
let itemSeleccion = ref(null)
let BusquedaPaciente = ref(null)

const cuposDisponiblesRef = ref(null)
const totalPaginas = computed(() => {
    return Math.ceil(totalRegistros.value / perPage.value)
})

const cambiarPagina = (pagina) => {
    if (pagina >= 1 && pagina <= totalPaginas.value) {
        currentPage.value = pagina
        obtenerCitasControl()
    }
}

const obtenerCitasControl = async () => {
    isModalLoading.value = true;
    CitasControl.value = []

    const pacienteLimpio = BusquedaPaciente.value ? BusquedaPaciente.value.replace(/\s+/g, '') : null;
    BusquedaPaciente.value = pacienteLimpio;
    const usarDocumentoHC = !!pacienteLimpio;

    const tieneEspecialidad = !!IdEspecialidad.value;
    const tieneDocumento = !!pacienteLimpio;

    // Validación: al menos uno de los filtros debe estar llenado
    if (!tieneEspecialidad && !tieneDocumento) {
        isModalLoading.value = false;
        return showAlert(
            'VALIDACIÓN DE CAMPOS',
            'Debe seleccionar una especialidad o ingresar un número de documento.',
            'warning'
        );
    }

    try {
        const { data } = await axios.post('/consulta-externa/citas/WebS_Pacientes_CitaControl_Lista_BuscarFiltro', {
            IdEspecialidad: IdEspecialidad.value || null,
            Page: currentPage.value,
            PerPage: perPage.value,
            DocumentoHC: usarDocumentoHC ? pacienteLimpio : null
        });

        CitasControl.value = data.CitaControlFiltro;
        totalRegistros.value = data.Total ?? 0;
    } catch (error) {
        console.error('Error al cargar las citas:', error);
    } finally {
        isModalLoading.value = false;
    }
};


const success = async () => {
    CitasControl.value = CitasControl.value.filter(item => item.IdCitaControl !== itemSeleccion.value.IdSeleccion);
}

const onChangeEspecialidad = () => {
    currentPage.value = 1
    obtenerCitasControl()
}

const consultarSisPaciente = async (citaControl) => {
    try {
        showAlert("VERIFICANDO AFILIACIÓN SIS ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        const response = await axios.post('/core/servicios/obtenerDatosSisCompletos', {
            numerodocumento: citaControl.NroDocumento,
            tipodocumento: citaControl.IdDocIdentidad,
            consultareniec: true,
            tipopersona: 0,
            modulo_origen: 'admicion_ce',
        });
        showAlert(
            "CONSULTA REALIZADA",
            "LA CONSULTA FUE REALIZADA DE FORMA EXITOSA",
            "success"
        );

        IdEspecialidad.value = citaControl.IdEspecialidad
        if (response?.data.respuesta == 1) {
            itemSeleccion.value = {
                Tipo: 'CitaControl',
                IdPaciente: citaControl.IdPaciente,
                IdSeleccion: citaControl.IdCitaControl,
                idfuentefinanciamiento: 3,
                IdTipoFinanciamiento: 2
            }

            cuposDisponiblesRef.value.openDialog()

        } else {
            itemSeleccion.value = {
                Tipo: 'CitaControl',
                IdPaciente: citaControl.IdPaciente,
                IdCitaControl: citaControl.IdCitaControl,
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

const clearChangeEspecialidad = () => {
    BusquedaPaciente.value = '';
    obtenerCitasControl()
};

</script>

<template>
    <ModalLoader v-if="isModalLoading" />
    <div class="row">
        <div class="col-md-4 ">
            <BaseCombo v-model="IdEspecialidad" :options="appStore.citaAtencionProximaEspecialidades"
                label="Especialidad" option-label="label" option-value="id" :filter="true"
                placeholder="Seleccione una Especialidad" @update:modelValue="onChangeEspecialidad" />
        </div>

        <div class="col-md-4 mt-1">
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
            <div v-if="CitasControl.length" class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p style="font-size: 12px;" class="mb-0">
                        Se han encontrado <b>({{ CitasControl.length }})</b> Registros(s)
                    </p>

                    <!-- Paginación alineada a la derecha -->
                    <BasePaginatorHTML :currentPage="currentPage" :totalRecords="totalRegistros" :perPage="perPage"
                        :totalPaginas="totalPaginas" @cambiarPagina="cambiarPagina" />
                </div>

                <table class="table table-sm table-hover dataTable no-footer dtr-inline mt-2"
                    style="table-layout: auto;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>F.Propuesta</th>
                            <th>Origen</th>
                            <th>Paciente</th>
                            <th>Destino</th>
                            <th>Estado</th>
                            <th class="text-center" v-if="$can(`${moduloActual}.tab.citas.control.registrar.cita`)">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in CitasControl" :key="item.IdCitaControl">
                            <td style="width: 1%;">{{ index + 1 }}</td>
                            <td style="width: 1%;">
                                {{ item.FechaPropuesta }}
                            </td>
                            <td style="width: 5%;">
                                <b>{{ item.ServicioOrigen }}</b> <br>
                                MED: {{ item.MedicoOrigen }}
                            </td>
                            <td style="width: 5%;">
                                {{ item.NumeroDocumento }} - HC: {{ item.HistoriaClinica }} <br>
                                {{ item.Paciente }}<br>
                                <b>EDAD: {{ item.Edad }}</b>
                            </td>
                            <td style="width: 5%;">
                                <b>{{ item.EspecialidadDestino }}</b>
                            </td>
                            <td style="width: 3%;">
                                <span class="badge bg-label-success">{{ item.Estado }}</span>
                            </td>
                            <td style="width: 2%;" class="text-center"
                                v-if="$can(`${moduloActual}.tab.citas.control.registrar.cita`)">
                                <button class="btn btn-xs btn-icon btn-primary" @click="consultarSisPaciente(item)">
                                    <i class="ti ti-calendar text-white"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="alert alert-info mt-3 mb-0 text-center">No cuenta con citas control para mostrar.</div>
        </div>
    </div>

    <CuposDisponibles ref="cuposDisponiblesRef" :recordId="IdEspecialidad" :itemSeleccion="itemSeleccion"
        @success="success()" />
</template>
