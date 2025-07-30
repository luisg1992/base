<script setup>
import { ref } from 'vue'
import axios from 'axios'
import ModalPDF from '@/components/ModalPDF.vue';
import BaseCombo from '../../../../../../components/WSelect/WSelect.vue';
import BaseDatePicker from '../../../../../../components/WDatePicker/WDatePicker.vue';
import BusquedaRangoDeFecha from '@/components/Busquedas/BusquedaRangoDeFecha.vue'
import { verReferencia } from '../../funciones/verReferencia.js';
import WButton from "../../../../../../components/WButton/WButton.vue";

const props = defineProps(['moduloActual']);

const modalBusquedaRef = ref(null)
let records = ref([])
let isModalLoading = ref(false)

let estado = ref(-1)
let estadosReferencia = [
    { label: 'TODOS', id: -1 },
    { label: 'PENDIENTE', id: 2 },
    { label: 'ACEPTADO', id: 3 },
    { label: 'RECHAZADO', id: 4 },
    { label: 'OBSERVADO', id: 9 },
    { label: 'PACIENTE RECIBIDO', id: 5 },
    { label: 'PACIENTE CITADO', id: 7 },
    { label: 'CONTRAREFERIDO', id: 8 },
    { label: 'DE BAJA', id: 0 },
];

function obtenerClaseEstado(estado) {
    switch (estado) {
        case 'CITADO':
            return 'badge bg-label-success';
        case 'PACIENTE CITADO':
            return 'badge bg-label-info';
        case 'Solicitud':
            return 'badge bg-label-info';
        case 'SOLICITUD':
            return 'badge bg-label-info';
        case 'PACIENTE RECIBIDO':
            return 'badge bg-label-primary';
        case 'ACEPTADO':
            return 'badge bg-label-success';
        case 'Aprobado':
            return 'badge bg-label-success';
        case 'APROBADO':
            return 'badge bg-label-success';
        case 'PENDIENTE':
            return 'badge bg-label-warning';
        case 'Observado':
            return 'badge bg-label-warning';
        case 'RECHAZADO':
            return 'badge bg-label-secondary';
        case 'OBSERVADO':
            return 'badge bg-label-danger';
        default:
            return 'badge bg-label-light';
    }
}
const modalPDF = ref(null);
const FechaDesde = ref(null)


const obtenerCitasReferencia = async () => {
    if (!FechaDesde.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE FECHA ES NECESARIA PARA PODER REALIZAR LA BÚSQUEDA NECESARIA, POR FAVOR VERIFIQUE LA INFORMACIÓN INGRESADA.',
            'warning', false, true
        );
    }

    isModalLoading.value = true
    records.value = [];
    try {
        showAlert("VERIFICANDO LISTADO DE REFERENCIAS ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        const { data } = await axios.post('/core/servicios/RefConListarPacienteReferidos', {
            FechaDesde: FechaDesde.value,
            FechaHasta: FechaDesde.value,
            estado: estado.value
        })

        if (data.data.length === 0) {
            const mensaje = `¿LA CONSULTA REALIZADA NO PUDO SER PROCESADA, DESEAS VOLVER A CONSULTAR EL SERVICIO DE REFERENCIAS?`;
            const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');

            if (confirmado) {
                obtenerCitasReferencia();
            }
            return;
        } else {
            if (data.success) {
                showAlert("VALIDACIÓN REALIZADA", "EL PROCESO FUE REALIZADO DE FORMA EXITOSA, LAS REFERENCIAS FUERON CARGADAS DE FORMA CORRECTA", "success");
                records.value = data.data;
            } else {
                showAlert("VALIDACIÓN REALIZADA", "NO SE HA ENCONTRADO REFERENCIAS RELACIONADAS, POR FAVOR VERIFIQUE LA FECHA INGRESADA", "warning");
            }
        }

    } catch (error) {
        console.error('Error al cargar las citas:', error)
    } finally {
        isModalLoading.value = false
    }
}

const abrirModal = () => {
    modalBusquedaRef.value?.openDialog()
}

const recibirFechas = async (parametros) => {
    isModalLoading.value = true;

    try {
        // Validar que el rango de fechas no sea mayor a 7 días
        const fechaInicio = new Date(parametros.FechaDesde.split('/').reverse().join('-'));
        const fechaFin = new Date(parametros.FechaHasta.split('/').reverse().join('-'));

        const diferenciaEnDias = (fechaFin - fechaInicio) / (1000 * 60 * 60 * 24);
        if (diferenciaEnDias >= 7) {
            showAlert("VALIDACIÓN DE RANGO DE FECHAS INVÁLIDO",
                "EL RANGO DE FECHAS SELECCIONADO NO DEBE DE SER MAYOR DE 7 DÍAS, VERIFIQUE LA INFORMACIÓN INGRESADA Y VUELVA A INTENTARLO",
                "warning", true, true);
            return;
        }

        const queryParams = new URLSearchParams({
            FechaDesde: parametros.FechaDesde,
            FechaHasta: parametros.FechaHasta,
            estado: estado.value
        }).toString();

        const response = await axios.get(`/core/servicios/RefConListarPacienteReferidosExportar?${queryParams}`, {
            responseType: 'blob'
        });

        const blob = new Blob([response.data], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `referencias_${new Date().toISOString().slice(0, 19).replace(/[:T]/g, '')}.xlsx`);
        document.body.appendChild(link);
        link.click();
        link.remove();

    } catch (error) {
        console.error('Error al exportar:', error);
        showAlert("ERROR", "Ocurrió un error al exportar las referencias.", "error");
    } finally {
        isModalLoading.value = false;
    }
};


const { visualizarReferencia } = verReferencia({
    openPDFModal: (url, title, type) => {
        modalPDF.value.openModal(url, title, type);
    }
});

const onVisualizarReferencia = async (item) => {
    let fechaAceptacion = '';
    if (item.estadoactual == 'ACEPTADO') {
        fechaAceptacion = item.fechaaceptacion;
    } else {
        fechaAceptacion = item.fechaini;
    }
    const anio = fechaAceptacion.split('/')[2].split(' ')[0];
    const value = {
        IdReferenciaDB: item.idreferencia,
        idReferencia: item.idreferencia,
        numeroReferencia: item.nroreferencia,
        estado: item.estadoactual,
        codigoEstado: item.fgestado,
        codigoEstablecimientoOrigen: item.idestorigen,
        anio: anio
    };
    visualizarReferencia(value);
};

</script>

<template>
    <ModalLoader v-if="isModalLoading" />
    <div class="row mb-2">
        <div class="col-md-3 ">
            <BaseCombo v-model="estado" :options="estadosReferencia" label="Estado Referencia" option-label="label"
                option-value="id" :filter="true" placeholder="Seleccione un Estado" />
        </div>
        <div class="col-md-3 ">
            <BaseDatePicker v-model="FechaDesde" label="Fecha Inicial" placeholder="Selecciona la fecha"
                dateFormat="dd/mm/yy" />
        </div>

        <div class="col-md-3  d-flex align-items-end">
            <w-button label="Buscar Referencias"
                      icon="search"
                      type-button="help"
                      variant="outlined"
                      class="w-100"
                      @click="obtenerCitasReferencia"></w-button>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <w-button label="Descargar Excel"
                      icon="file-type-xls"
                      type-button="success"
                      variant="outlined"
                      class="w-100"
                      @click="abrirModal"></w-button>
        </div>
    </div>

    <div class="row ">
        <!-- Tabla -->
        <div v-if="records.length" class="table-responsive" style="max-width: 100%; overflow-x: auto;">
            <div class="d-flex justify-content-between align-items-center mt-3">
                <p style="font-size: 12px;" class="mb-0">
                    Se han encontrado <b>({{ records.length }})</b> Registros(s)
                </p>
            </div>

            <table class="table table-sm table-hover dataTable no-footer dtr-inline mt-2" style="table-layout: auto;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Origen</th>
                        <th>Paciente</th>
                        <th>Destino</th>
                        <th>Estado</th>
                        <th class="text-center" v-if="$canAny([
                            `${moduloActual}.tab.filtro.de.referencias.visualizar.referencia`
                        ])">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in records" :key="item.IdCitaControl">
                        <td style="width: 1%;">{{ index + 1 }}</td>
                        <td style="width: 3%;">
                            N° REFERENCIA : <b>{{ item.nroreferencia }}</b> <br>
                            {{ item.nombestorigen }}
                        </td>
                        <td style="width: 3%;">
                            <span v-if="item.idtipodoc == 1">DNI</span>
                            <span v-else="item.idtipodoc !=1 ">CE</span> {{ item.numdoc }} <br>
                            {{ item.nomcomppac }}
                        </td>
                        <td style="width: 5%;">
                            <b>{{ item.especialidad }}</b> <br>{{ item.descupsdestino }}
                        </td>
                        <td style="width: 3%;">
                            <span :class="obtenerClaseEstado(item.estadoactual)">{{ item.estadoactual }}</span>
                        </td>
                        <td style="width: 2%;" class="text-center" v-if="$canAny([
                            `${moduloActual}.tab.filtro.de.referencias.visualizar.referencia`
                        ])">
                            <w-button @click="onVisualizarReferencia(item)"
                                      v-if="$can(`${moduloActual}.tab.filtro.de.referencias.visualizar.referencia`)"
                                      type-button="help"
                                      size="small"
                                      icon="file-type-pdf">
                            </w-button>
<!--                            <button class="btn btn-xs btn-icon btn-primary me-1" @click="onVisualizarReferencia(item)"-->
<!--                                v-if="$can(`${moduloActual}.tab.filtro.de.referencias.visualizar.referencia`)">-->
<!--                                <i class="ti ti-pdf text-white"></i>-->
<!--                            </button>-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Sin resultados -->
        <div v-else class="text-center text-muted">
            NO CUENTA REFERENCIAS PARA MOSTRAR
        </div>
    </div>

    <!-- Modal de búsqueda -->
    <BusquedaRangoDeFecha ref="modalBusquedaRef" @enviarDatos="recibirFechas"
        @cerrado="() => console.log('Modal cerrado sin enviar')" />

    <ModalPDF ref="modalPDF" />
</template>
