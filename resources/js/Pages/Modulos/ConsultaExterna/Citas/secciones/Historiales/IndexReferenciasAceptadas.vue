<script setup>
import { ref } from 'vue'
import axios from 'axios'
import ModalPDF from '@/components/ModalPDF.vue';
import ModalBase from '@/components/BaseModal.vue'
import CitarFormulario from '../CitarFormulario.vue'
import BaseDatePicker from "@/components/WDatePicker/WDatePicker.vue";
import { verReferencia } from '../../funciones/verReferencia.js';
import { listasRelacionadasPaciente } from '@/services/ConsultaExterna/Citas/gestionCitas';
let props = defineProps(['moduloActual']);

let listaReferencias = ref([]); // NUEVA lista simplificada
let isModalLoading = ref(false)

let modalCitaVisible = ref(false)
let modalPDF = ref(null);
let FechaFiltro = ref(null)
let idPaciente = ref(null)
let IdFuenteFinanciamiento = ref(1)
let IdTipoFinanciamiento = ref(1)
let IdEspecialidad = ref(null)
let tipoSeleccion = ref(null)
let itemSeleccion = ref(null)
let pacienteSeleccionado = ref(null)
let citasPendientes = ref([]);
let tipoDocumento = ref(1);
let nroDocumento = ref('');

const obtenerCitasReferencia = async () => {
    if (!FechaFiltro.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE FECHA ES NECESARIA PARA PODER REALIZAR LA BÚSQUEDA NECESARIA, POR FAVOR VERIFIQUE LA INFORMACIÓN INGRESADA.',
            'warning', false, true
        );
    }

    isModalLoading.value = true;
    listaReferencias.value = [];

    try {
        showAlert("VERIFICANDO LISTADO DE REFERENCIAS ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);

        const { data } = await axios.post('/core/servicios/RefConListarPacienteReferidos', {
            FechaDesde: FechaFiltro.value,
            FechaHasta: FechaFiltro.value,
            estado: 3
        });

        if (!data.success) {
            showAlert("VALIDACIÓN REALIZADA", "NO SE ENCONTRARON REFERENCIAS RELACIONADAS A LA FECHA SELECCIONADA, POR FAVOR SELECCIONE UNA FECHA DIFERENTE", "warning");
        } else {
            showAlert("VALIDACIÓN REALIZADA", "EL PROCESO FUE REALIZADO DE FORMA EXITOSA, LAS REFERENCIAS FUERON CARGADAS DE FORMA CORRECTA", "success");
            listaReferencias.value = data.data;
        }

    } catch (error) {
        console.error('Error al cargar las citas:', error);
    } finally {
        isModalLoading.value = false;
    }
}

const { visualizarReferencia } = verReferencia({
    openPDFModal: (url, title, type) => {
        modalPDF.value.openModal(url, title, type);
    }
});

const cargarListasRelacionadasPaciente = async () => {
    try {
        const resultado = await listasRelacionadasPaciente(idPaciente.value, 'CITAS');
        if (resultado) {
            citasPendientes.value = resultado.CitasPendientes;
        }
    } catch (error) {
        console.error('Error al cargar programación:', error);
    }
};

const onVisualizarReferencia = async (item) => {
    if (item) {
        const fechaEnvio = item.fechaenvio ?? '';
        const anio = fechaEnvio.split('/')[2];
        const value = {
            idReferencia: item.idreferencia,
            numeroReferencia: item.nroreferencia,
            estado: 'ACEPTADO',
            codigoEstado: 3,
            codigoEstablecimientoOrigen: item.codunico,
            anio: anio
        };
        visualizarReferencia(value);
    } else {
        showAlert("OPERACIÓN CANCELADA", 'NO SE PUDO UBICAR LA REFERENCIA SELECCIONADA, POR FAVOR VUELVE A INTENTARLO O COMUNÍCATE CON INFORMÁTICA', "warning");
    }
};

const generarCita = async (parametrosReferencia) => {
    if (parametrosReferencia?.idtipodoc == 'DNI') {
        tipoDocumento.value = '1'
    }

    if (parametrosReferencia?.idtipodoc == 'CE') {
        tipoDocumento.value = '2'
    }

    IdEspecialidad.value = null
    IdFuenteFinanciamiento.value = 3
    IdTipoFinanciamiento.value = 2
    nroDocumento.value = parametrosReferencia?.numdoc.replace(/\s+/g, '');


    if (!tipoDocumento.value) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'EL CAMPO DE TIPO DE DOCUMENTO ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.', 'error');
        return;
    }
    if (tipoDocumento.value == 1 && nroDocumento.value.length !== 8) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'EL NÚMERO DE DOCUMENTO DEBE TENER 8 CARACTERES.', 'error');
        return;
    }

    try {
        showAlert("VERIFICANDO DATOS EN RENIEC ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);

        const response = await axios.post('/personas/persona/PacienteBuscarTipoAndDocumento', {
            nombremodulo: 'ADMISIÓN Y CITAS',
            tipodocumento: tipoDocumento.value,
            numerodocumento: nroDocumento.value,
            tipopersona: 0,
        })

        /*VALIDAMOS LA RESPUESTA DE RENIEC SEA EXITOSA O LA CONSULTA A LA BD*/
        if (response.data.success) {
            /*SI ES EXTRANJERO NO PASA POR RENIEC*/
            pacienteSeleccionado.value = response?.data?.data
            idPaciente.value = response?.data?.data.IdPaciente ?? null

            /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
            const parametros = {
                tipodocumento: tipoDocumento.value,
                numerodocumento: nroDocumento.value,
                consultareniec: true,
                tipopersona: 0,
            }
            await obtenerDatosSis(parametros, parametrosReferencia);
            /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
        } else {
            /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
            const parametros = {
                tipodocumento: tipoDocumento.value,
                numerodocumento: nroDocumento.value,
                consultareniec: false,
                tipopersona: 0,
            }
            await obtenerDatosSis(parametros, parametrosReferencia);
            /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
        }
    } catch (error) {
        if (error.response) {
            showAlert('ERROR', error.response.data.message || 'Error al buscar paciente.', 'error');
        } else {
            showAlert('ERROR', error.message || 'Error de conexión.', 'error');
        }
    }
};

async function obtenerDatosSis(parametros = {}, parametrosReferencia = {}) {
    try {
        showAlert("VERIFICANDO AFILIACIÓN SIS ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        const response = await axios.post('/core/servicios/obtenerDatosSisCompletos', {
            numerodocumento: parametros.numerodocumento,
            tipodocumento: parametros.tipodocumento,
            consultareniec: parametros.consultareniec,
            tipopersona: parametros.tipopersona,
            modulo_origen: 'admicion_ce',
        });

        if (response?.data.respuesta == 1) {
            IdFuenteFinanciamiento.value = 3
            IdTipoFinanciamiento.value = 2

            if (parametros.consultareniec == false) {
                if (response?.data.data) {
                    pacienteSeleccionado.value = response?.data?.data
                    idPaciente.value = response?.data?.data.IdPaciente ?? null
                }
            }

            if (idPaciente.value) {
                await cargarListasRelacionadasPaciente();
            }

            itemSeleccion.value = {
                IdReferenciaRefCon: null,
                Tipo: "Refcon",
                anio: parametrosReferencia?.fechaenvio
                    ? parametrosReferencia.fechaenvio.split('/')[2]
                    : null,
                codigoEspecialidad: "",
                codigoEstablecimientoOrigen: parametrosReferencia?.codunico,
                codigoEstado: "3",
                descUpsDestino: "",
                descUpsOrigen: "",
                especialidad: parametrosReferencia?.especialidad,
                establecimientoOrigen: parametrosReferencia?.nombestorigen,
                estado: "ACEPTADO",
                fechaEnvio: parametrosReferencia?.fechaenvio ? parametrosReferencia?.fechaenvio.split('/').reverse().join('-') : null,
                fechaFormateada: parametrosReferencia?.fechaenvio,
                idEstablecimientoReferencia: parametrosReferencia.nroreferencia ? parametrosReferencia.nroreferencia.split('-')[0] : null,
                idReferencia: parametrosReferencia?.idreferencia,
                numeroDocumento: parametrosReferencia?.numdoc,
                numeroReferencia: parametrosReferencia?.nroreferencia,
                tipoDocumento: parametrosReferencia?.idtipodoc == '1' ? 'DNI' : 'CE',
                upsDestino: parametrosReferencia?.idupsdestino,
                upsOrigen: parametrosReferencia?.idupsorigen,
            };

            tipoSeleccion.value = 'Referencia'
            modalCitaVisible.value = true


            showAlert(
                "CONSULTA REALIZADA",
                "LA CONSULTA FUE REALIZADA DE FORMA EXITOSA",
                "success"
            );

        } else {
            showAlert(
                "CONSULTA REALIZADA",
                "EL PACIENTE NO PUDO SER UBICADO NI EN RENIEC NI EN EL SIS, POR FAVOR COMUNÍQUESE CON EL ÁREA DE INFORMÁTICA.",
                "error"
            );
        }
    } catch (error) {
        console.error('Error al obtener datos del SIS:', error);
        return null;
    }
}

const descargarExcel = () => {
    if (!FechaFiltro.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE FECHA ES NECESARIA PARA PODER REALIZAR LA BÚSQUEDA NECESARIA, POR FAVOR VERIFIQUE LA INFORMACIÓN INGRESADA.',
            'warning', false, true
        );
    }

    if (!listaReferencias.value.length) {
        showAlert("SIN DATOS", "No hay referencias para exportar.", "info");
        return;
    }

    const encabezados = [
        "N° Referencia",
        "Establecimiento Origen",
        "Tipo Documento",
        "N° Documento",
        "Nombres",
        "Celular",
        "Dirección",
        "Especialidad",
        "Fecha Envío",
        "Fecha Aceptación"
    ];

    const filas = listaReferencias.value.map(item => [
        item.nroreferencia ?? '',
        item.nombestorigen ?? '',
        (item.idtipodoc == '1' ? 'DNI' : 'CE'),
        item.numdoc ?? '',
        `${item.nomcomppac ?? ''}`,
        item.celularpac ?? '',
        item.direccion ?? '',
        item.especialidad ?? '',
        item.fechaenvio ?? '',
        item.fechaaceptacion ?? ''
    ]);

    const html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="UTF-8">
        </head>
        <body>
            <table border="1">
                <thead>
                    <tr>${encabezados.map(e => `<th>${e}</th>`).join('')}</tr>
                </thead>
                <tbody>
                    ${filas.map(fila => `
                        <tr>${fila.map(dato => `<td>${dato}</td>`).join('')}</tr>
                    `).join('')}
                </tbody>
            </table>
        </body>
        </html>
    `;

    const blob = new Blob([html], {
        type: "application/vnd.ms-excel;charset=utf-8;"
    });

    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `Referencias_${new Date().toISOString().split('T')[0]}.xls`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const cerrarModal = async () => {
    modalCitaVisible.value = false
    isModalLoading.value = false;
}

const cerrarModalCita = async () => {
    modalCitaVisible.value = false
    isModalLoading.value = false;
}

const generacionModalCita = async (itemSeleccion) => {
    const idReferenciaAEliminar = itemSeleccion.idReferencia;
    listaReferencias.value = listaReferencias.value.filter(item => {
        return item.idreferencia !== idReferenciaAEliminar;
    });
    modalCitaVisible.value = false
    isModalLoading.value = false;
};

</script>

<template>
    <ModalLoader v-if="isModalLoading" />
    <div class="row">
        <div class="col-md-3">
            <BaseDatePicker v-model="FechaFiltro" label="Fecha Consulta" placeholder="Selecciona la fecha" />
        </div>

        <div class="col-md-4 d-flex align-items-end gap-2">
            <button class="btn btn-outline-primary waves-effect w-100" @click="obtenerCitasReferencia">
                <i class="fas fa-search me-1"></i> Buscar Referencias
            </button>
            <button class="btn btn-outline-success waves-effect w-100" @click="descargarExcel"
                v-if="listaReferencias.length">
                <i class="fas fa-file-excel me-1"></i> Descargar Excel
            </button>
        </div>
    </div>

    <div class="row ">
        <!-- Tabla -->
        <div class="col-12">
            <div v-if="listaReferencias.length" class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p style="font-size: 12px;" class="mb-0">
                        Se han encontrado <b>({{ listaReferencias.length }})</b> Registros(s)
                    </p>
                </div>
                <table class="table table-sm table-hover dataTable no-footer dtr-inline mt-2"
                    style="table-layout: auto;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Origen</th>
                            <th>Paciente</th>
                            <th>Contacto</th>
                            <th>Destino</th>
                            <th>Estado</th>
                            <th class="text-center" v-if="$canAny([
                                `${moduloActual}.tab.citas.referencia.visualizar.referencia`,
                                `${moduloActual}.tab.citas.referencia.registrar.cita`
                            ])">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in listaReferencias" :key="index">
                            <td style="width: 1%;">{{ index + 1 }}</td>
                            <td style="width: 3%;">
                                <b>N° REFERENCIA: {{ item.nroreferencia ?? '---' }}</b> <br>
                                {{ item.nombestorigen ?? '---' }} 
                            </td>
                            <td style="width: 3%;">
                                <b>{{ item.idtipodoc == '1' ? 'DNI' : 'CE' }} {{ item.numdoc ?? '---' }}</b>
                                <br>
                                {{ item.nomcomppac ?? '' }}
                            </td>
                            <td style="width: 3%;">
                                <b>CELULAR: {{ item.celularpac }}</b><br>
                                {{ item.direccion ?? '---' }}
                            </td>
                            <td style="width: 5%;">
                                <b>UPS: {{ item.idupsdestino ?? '---' }}</b> <br>
                                {{ item.especialidad ?? '---' }}
                            </td>
                            <td style="width: 3%;">
                                <span class="badge bg-label-success">ACEPTADO</span>
                            </td>
                            <td style="width: 2%;" class="text-center" v-if="$canAny([
                                `${moduloActual}.tab.citas.referencia.visualizar.referencia`,
                                `${moduloActual}.tab.citas.referencia.registrar.cita`
                            ])">
                                <button class="btn btn-xs btn-icon btn-primary me-1"
                                    @click="onVisualizarReferencia(item)"
                                    v-if="$can(`${moduloActual}.tab.citas.referencia.visualizar.referencia`)">
                                    <i class="ti ti-pdf text-white"></i>
                                </button>
                                <button class="btn btn-xs btn-icon btn-primary me-1" @click="generarCita(item)"
                                    v-if="$can(`${moduloActual}.tab.citas.referencia.registrar.cita`)">
                                    <i class="ti ti-calendar text-white"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="alert alert-info mt-3 mb-0 text-center">No cuenta con referencias para mostrar.</div>
        </div>
    </div>

    <!-- Modal para generar la cita -->
    <ModalBase :is-visible="modalCitaVisible" @close="cerrarModal" header="Generar Cita Médica" size="modal-full"> 
        <CitarFormulario :paciente="pacienteSeleccionado" :tipoSeleccion="tipoSeleccion" :itemSeleccion="itemSeleccion"
            :IdFuenteFinanciamiento="IdFuenteFinanciamiento" :IdTipoFinanciamiento="IdTipoFinanciamiento"
            :citasPaciente="citasPendientes" :sis="true" @cerrarModal="cerrarModalCita" :conSuccessReferencia="true"
            @successReferencia="generacionModalCita" /> 
    </ModalBase>
    <ModalPDF ref="modalPDF" />
</template>
