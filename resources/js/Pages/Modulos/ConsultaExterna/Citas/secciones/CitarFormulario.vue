<script setup>
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'

import { defineEmits } from 'vue';
import ModalPDF from '@/components/ModalPDF.vue';
import ToggleSwitch from 'primevue/toggleswitch';
import BaseCombo from "@/components/WSelect/WSelect.vue";
import CitaBotones from '@/components/citas/CitaBotones.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

import MotivoAnulacionDialog from '@/components/citas/MotivoAnulacionDialog.vue';

import Popover from 'primevue/popover';
import {
    verFormatoCita,
    imprimirFormatoCita,
    anularFormatoCita,
    confirmarYAnularCita,
    verFormatoFUA,
    generarLoteCITAS,
    listasRelacionadasPaciente
} from '@/services/ConsultaExterna/Citas/gestionCitas';

const emit = defineEmits(['cerrarModal', 'successReferencia']);

// Props
const motivoAnulacionRef = ref(null)
const props = defineProps({
    citasPaciente: {
        type: Array,
        default: () => []
    },
    citaAdicionales: {
        type: Array,
        default: () => []
    },
    paciente: Object,
    tipoSeleccion: String,
    itemSeleccion: Object,
    IdFuenteFinanciamiento: Number,
    IdTipoFinanciamiento: Number,
    IdEspecialidad: {
        type: [Number, String]
    },
    moduloActual: String,
    IdEspecialidades: {
        type: Array,
        default: () => []
    },
    sis: {
        type: Boolean,
        default: false
    },
    conSuccessReferencia: {
        type: Boolean,
        default: false
    }
})

const modalPDF = ref(null);
// Formulario y refs

let sis = ref(false);
let popoverTurno = ref()
let popoverSexo = ref()
let turnoSeleccionado = ref(null);
let sexoSeleccionado = ref(null);

let servicioSeleccionado = ref(null)
let servicio_id = ref(null)
let medico_id = ref(null)
let programacion_id = ref(null)
let isModalLoading = ref(false)
let EsAdicional = ref(0)
let EsAdicionalNroCupo = ref(0)
let Validador = ref(0)
let IdTipoConsultorio = ref(1)
let switchDesactivado = ref(false)
let switchIdTipoConsultorio = ref(false)
let multiples_citas = ref(false);

const form = ref({
    paciente: props.paciente,
    tipoSeleccion: props.tipoSeleccion,
    itemSeleccion: props.itemSeleccion,
    servicio_id: null,
    medico_id: null
})

let searchQueryCitas = ref('');
let servicios = ref([])
let serviciosFiltrados = ref([]);
let programacion = ref([])
let programacionFiltrados = ref([]);
let cupos = ref([])
let citaAdicionales = ref([]);
let citasPendientes = ref([]);
let fechaSeleccionadaCalendario = ref(null);

// Valor reactivo para el ítem seleccionado
let itemProMedicaServicio = ref(null);
let searchQuery = ref('');
let inputsearchQuery = ref(null);
let programacionMedicosServicios = ref([])

const ProgramacionCalendario = ref({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    events: [],
    locale: 'es',
    headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: ''
    },
    height: '40.5rem',
    validRange: {
        start: new Date(new Date().setHours(0, 0, 0, 0)).toISOString()
    },
    eventContent: function (arg) {
        const item = arg.event.extendedProps.data;
        return {
            html: `   <div style="text-align:center; font-size: 11px">
                            ${item.Horario}<br>
                            <small style="text-align:center; font-size: 12px"><b>${item.Cupos}</b></small>
                        </div>
                    `
        };
    },
    eventClick: async (info) => {
        const fecha = info.event.start;
        const dia = String(fecha.getDate()).padStart(2, '0');
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const anio = fecha.getFullYear();
        const fechaFormateada = `${dia}/${mes}/${anio}`;
        const headerEl = document.getElementById('pv_id_9_header');
        if (headerEl) {
            headerEl.innerText = `Generar Cita Médica - ${fechaFormateada} - ${props.paciente?.PrimerNombre} ${props.paciente?.ApellidoPaterno} ${props.paciente?.ApellidoMaterno}`;
        }

        const formatoFecha = fecha.toISOString().split('T')[0];
        itemProMedicaServicio.value = info.event.extendedProps?.data;
        Validador.value = info.event.extendedProps?.data?.Validador

        if (programacion_id.value === info.event.extendedProps?.data?.IdProgramacion) return;
        cupos.value = null
        await cargarCupos(info.event.extendedProps?.data?.IdProgramacion);
        programacion_id.value = info.event.extendedProps?.data?.IdProgramacion;
        // Si la fecha seleccionada es igual a la actual, no hagas nada
        if (fechaSeleccionadaCalendario.value === formatoFecha) return;
        fechaSeleccionadaCalendario.value = formatoFecha

    },
    dateClick: async (info) => {
        EsAdicional.value = 0
        const formatoFecha = info.dateStr;

        // Convertir a formato día/mes/año
        const [anio, mes, dia] = formatoFecha.split('-');
        const fechaFormateada = `${dia}/${mes}/${anio}`;
        const headerEl = document.getElementById('pv_id_9_header');
        if (headerEl) {
            headerEl.innerText = `Generar Cita Médica - ${fechaFormateada} - ${props.paciente?.PrimerNombre} ${props.paciente?.ApellidoPaterno} ${props.paciente?.ApellidoMaterno}`;
        }

        cupos.value = null
        programacion_id.value = null
        // Si la fecha seleccionada es igual a la actual, no hagas nada
        if (fechaSeleccionadaCalendario.value === formatoFecha) return;
        fechaSeleccionadaCalendario.value = formatoFecha
    },
    datesSet: (info) => {

        fechaSeleccionadaCalendario.value = null
        EsAdicional.value = 0

        medico_id.value = null
        itemProMedicaServicio.value = null
        servicioSeleccionado.value = null

        const currentDate = info.view.currentStart;
        const dia = String(currentDate.getDate()).padStart(2, '0');
        const mes = String(currentDate.getMonth() + 1).padStart(2, '0');
        const anio = currentDate.getFullYear();
        const fechaFormateada = `${dia}/${mes}/${anio}`;

        const headerEl = document.getElementById('pv_id_9_header');
        if (headerEl) {
            headerEl.innerText = `Generar Cita Médica - ${fechaFormateada} - ${props.paciente?.PrimerNombre} ${props.paciente?.ApellidoPaterno} ${props.paciente?.ApellidoMaterno}`;
        }

        const fechaInicio = `${anio}-${mes}-01`;
        cargarProgramacionMedicosServicios(fechaInicio)
    }
})

const actualizarEventosCalendario = () => {
    if (!servicio_id.value && !medico_id.value) {
        ProgramacionCalendario.value.events = [];
        return;
    }

    const turno = turnoSeleccionado.value;   // null, 'M' o 'T'
    const sexo = sexoSeleccionado.value;          // null, '1' o '2'

    // 1️⃣ Filtrado por servicio, médico y turno
    const eventosFiltrados = programacion.value.filter(item => {
        const coincideServicio = !servicio_id.value || item.IdServicio == servicio_id.value;
        const coincideMedico = !medico_id.value || item.IdMedico == medico_id.value;
        const coincideIdTipoConsultorio = !IdTipoConsultorio.value || item.IdTipoConsultorio == IdTipoConsultorio.value;
        const coincideTurno = !turno || item.Turno === turno;   // ← filtro de turno
        const coincideConIdSexo = !sexo || Number(item.IdSexo) === sexo;     // ← filtro de sexo
        return coincideServicio && coincideMedico && coincideTurno && coincideConIdSexo && coincideIdTipoConsultorio;
    });

    // 2️⃣ Mapeo a eventos del calendario
    ProgramacionCalendario.value.events = eventosFiltrados.map(item => {
        let color = '#0e9a4c';                       // Verde por defecto
        const [ocupados, total] = item.Cupos.split('/').map(Number);

        if (item.IdTipoConsultorio == 2) {
            color = ocupados >= total ? '#cb272c' : '#0e9a4c'; // Rojo lleno · Naranja con cupos
        } else {
            if (ocupados >= total) color = '#cb272c';          // Rojo lleno
        }

        return {
            title: '',
            start: item.Fecha,
            backgroundColor: color,
            borderColor: color,
            extendedProps: { data: item }
        };
    });
};

const cargarServicios = async (idMedico) => {
    sis.value = props.sis;
    let IdTipoServicio = 1;
    citasPendientes.value = props.citasPaciente;
    citaAdicionales.value = props.citaAdicionales;
    IdTipoConsultorio.value = 1
    switchIdTipoConsultorio.value = false
    switchDesactivado.value = false

    if (props.tipoSeleccion == 'Procedimiento') {
        IdTipoConsultorio.value = 2
        switchIdTipoConsultorio.value = true
        switchDesactivado.value = true
    } else if (props.tipoSeleccion == 'Programas') {
        IdTipoServicio = 6
    }

    try {
        const payload = props.tipoSeleccion === 'Programas'
            ? {
                IdMedico: null,
                IdEspecialidad: null,
                CodUps: null,
                IdTipoServicio: IdTipoServicio
            }
            : {
                IdMedico: idMedico,
                IdEspecialidad: props.IdEspecialidad,
                CodUps: props.itemSeleccion?.upsDestino,
                IdTipoServicio: IdTipoServicio
            };

        const { data } = await axios.post('/consulta-externa/citas/WebS_ProgramacionMedica_Servicios_BuscarFiltro', payload);
        servicios.value = data;

        serviciosFiltrados.value = servicios.value.filter(
            s => s.IdTipoConsultorio == IdTipoConsultorio.value
        );
    } catch (error) {
        console.error('Error al cargar servicios:', error);
    }

}

const cargarCupos = async (programacion_id) => {
    isModalLoading.value = true
    try {
        const { data } = await axios.post('/consulta-externa/citas/WebS_CitasCupos_BuscarFiltro', {
            IdProgramacion: programacion_id
        })
        cupos.value = data

        //EVALUA SI TENEMOS O NO CUPOS DISPONIBLES
        const hayDisponibles = data.some(cupo => cupo.Estado === 'Disponible')
        EsAdicional.value = hayDisponibles ? 0 : 1
        if (Array.isArray(data)) {
            EsAdicionalNroCupo.value = data.length + 1;
        }
        //EVALUA SI TENEMOS O NO CUPOS DISPONIBLES

        isModalLoading.value = false
    } catch (error) {
        isModalLoading.value = false
    }
}
const cargarProgramacionMedicosServicios = async (Fecha) => {
    try {
        EsAdicional.value = 0;
        let IdTipoServicio = 1;
        const fechaEnvio = Fecha ?? new Date().toISOString().split('T')[0];

        const payload = props.tipoSeleccion === 'Programas'
            ? {
                Fecha: null,
                IdServicio: null,
                IdMedico: null,
                IdEspecialidad: null,
                CodUps: null,
                IdTipoServicio: 6
            }
            : {
                Fecha: fechaEnvio,
                IdServicio: null,
                IdMedico: null,
                IdEspecialidad: props.IdEspecialidad,
                CodUps: props.itemSeleccion?.upsDestino,
                IdTipoServicio: IdTipoServicio
            };

        const { data } = await axios.post('/consulta-externa/citas/WebS_ProgramacionMedica_Lista_BuscarFiltro', payload);

        if (data.length > 0) {
            programacionMedicosServicios.value = data;
            programacion.value = data;

            programacionFiltrados.value = programacionMedicosServicios.value.filter(
                s => s.IdTipoConsultorio == IdTipoConsultorio.value
            );
            actualizarEventosCalendario();
        } else {
            programacionMedicosServicios.value = null;
            programacionFiltrados.value = null;
            programacion.value = null;
            cupos.value = null;
            programacion_id.value = null;
        }
    } catch (error) {
        console.error('Error al cargar programación:', error);
    }
};


const actualizarListadoProgramacion = () => {
    const query = searchQuery.value.toLowerCase();
    const selectedMedico = form.value.medico_id;
    const selectedServicio = form.value.servicio_id;
    const fechaSeleccionada = fechaSeleccionadaCalendario.value;
    const turno = turnoSeleccionado.value;          // null, 'M', 'T'
    const sexo = sexoSeleccionado.value;           // null, 1, 2

    if (!Array.isArray(programacionFiltrados.value)) return [];

    return programacionFiltrados.value.filter(item => {
        const coincideConTexto =
            item.Medico.toLowerCase().includes(query) ||
            item.Servicio.toLowerCase().includes(query) ||
            item.FechaOrdenada.toLowerCase().includes(query);

        const coincideConMedico = !selectedMedico || item.IdMedico == selectedMedico;
        const coincideConServicio = !selectedServicio || item.IdServicio == selectedServicio;
        const coincideConFecha = !fechaSeleccionada || item.Fecha === fechaSeleccionada;
        const coincideConTurno = !turno || item.Turno === turno;
        const coincideConSexo = !sexo || Number(item.IdSexo) === sexo;

        return (
            coincideConTexto &&
            coincideConMedico &&
            coincideConServicio &&
            coincideConFecha &&
            coincideConTurno &&
            coincideConSexo
        );
    });
};

const programacionFiltrada = computed(actualizarListadoProgramacion);

const seleccionarProgramacionMedicosServicios = (progMedSer) => {
    itemProMedicaServicio.value = progMedSer;
    servicioSeleccionado.value = itemProMedicaServicio.value ? itemProMedicaServicio.value.TerapiaTipo : null;

    if (servicioSeleccionado.value == 1 || servicioSeleccionado.value == 2) {
        multiples_citas.value = true
    } else {
        multiples_citas.value = false
    }

    if (programacion_id.value == itemProMedicaServicio.value.IdProgramacion) return;

    servicio_id.value = itemProMedicaServicio.value.IdServicio
    medico_id.value = itemProMedicaServicio.value.IdMedico

    actualizarEventosCalendario()
    cargarCupos(itemProMedicaServicio.value.IdProgramacion);
    programacion_id.value = itemProMedicaServicio.value.IdProgramacion
    Validador.value = itemProMedicaServicio.value.Validador
};

// Manejadores de selección
const onChangeServicio = (idServicio) => {
    EsAdicional.value = 0
    const servicio = servicios.value.find(s => s.IdServicio === idServicio);
    servicioSeleccionado.value = servicio ? servicio.TerapiaTipo : null;
    if (servicioSeleccionado.value == 1 || servicioSeleccionado.value == 2) {
        multiples_citas.value = true
    } else {
        multiples_citas.value = false
    }

    form.value.servicio_id = idServicio
    servicio_id.value = idServicio

    cupos.value = null
    itemProMedicaServicio.value = null;
    programacion_id.value = null

    actualizarEventosCalendario()
    actualizarListadoProgramacion();
}

const verCita = async (IdCita) => {
    isModalLoading.value = true;

    const resultado = await verFormatoCita(IdCita);
    isModalLoading.value = false;
    if (resultado?.success && modalPDF.value) {
        showAlert("OPERACIÓN REALIZADA", "LA CITA FUE GENERADA DE FORMA EXITOSA", "success");
        modalPDF.value.openModal(resultado.pdf_base64, 'CITA MÉDICA', 'base64');
    } else {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", resultado?.mensaje || "LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTNÚE CON EL PROCESO DESEADO.", "warning");
    }
};

const imprimirCita = async (IdCita, validarImpresion) => {
    await imprimirFormatoCita(IdCita, validarImpresion);
};

const notificarCita = (IdCita) => {
    console.log('Notificar cita', IdCita);
    // Lógica para enviar SMS o notificación
};

const verFUA = async (IdCita) => {
    isModalLoading.value = true;
    const resultado = await verFormatoFUA(IdCita);
    isModalLoading.value = false;

    if (resultado?.success && modalPDF.value) {
        showAlert("OPERACIÓN REALIZADA", "LA CITA FUE GENERADA DE FORMA EXITOSA", "success");
        modalPDF.value.openModal(resultado.pdf_base64, 'CITA MÉDICA', 'base64');
    } else {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", resultado?.mensaje || "LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTNÚE CON EL PROCESO DESEADO.", "warning");
    }
};

const anularCita = async (IdCita, IdProgramacion) => {
    const respuesta = await confirmarYAnularCita(IdCita, motivoAnulacionRef);
    if (respuesta) {
        citasPendientes.value = citasPendientes.value.filter(cita => cita.IdCita !== IdCita);

        let externalIds = JSON.parse(localStorage.getItem('external_ids_generados') || '[]');
        externalIds = externalIds.filter(item => item.IdCita !== IdCita);
        localStorage.setItem('external_ids_generados', JSON.stringify(externalIds));

        const id = IdProgramacion ?? programacion_id.value;
        if (id) {
            cargarCupos(id);
        }
        showAlert("OPERACIÓN REALIZADA", "LA CITA FUE ANULADA DE FORMA EXITOSA", "success");
    } else {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", respuesta?.mensaje || "LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTNÚE CON EL PROCESO DESEADO.", "warning");
    }
};

const generarCita = async (cupo, consultar = true) => {
    if (consultar) {
        const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA?`;
        const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
        if (!confirmado) return;
    }

    isModalLoading.value = true;

    const formData = {
        idpaciente: props.paciente.IdPaciente,
        horacita: cupo?.HoraInicio ?? null,
        horafincita: cupo?.HoraFin ?? null,
        idfuentefinanciamiento: props.IdFuenteFinanciamiento,
        IdTipoFinanciamiento: props.IdTipoFinanciamiento,
        idprogramacion: programacion_id.value,
        TipoOrigenCita: props.tipoSeleccion,
        EsAdicional: EsAdicional.value,
        Validador: Validador.value,
        multiplesCitas: multiples_citas.value,
        NumCupo: EsAdicional.value == 1 ? (EsAdicionalNroCupo.value) ?? null : cupo?.NumCupo ?? null
    };

    const vienePreQuirurgico = props.IdEspecialidades && props.IdEspecialidades.length > 0;

    if (props.tipoSeleccion === 'Referencia') {
        formData.IdOrigenCita = props.itemSeleccion?.idReferencia;
        formData.dataReferencia = props.itemSeleccion;
    } else if (props.tipoSeleccion === 'ReferenciaGalen') {
        formData.IdOrigenCita = props.itemSeleccion?.idReferencia;
    } else if (props.tipoSeleccion === 'Interconsulta' || props.tipoSeleccion === 'InterconsultaRQ') {

        if (vienePreQuirurgico) {
            const especialidadEncontrada = props.IdEspecialidades.find(
                (esp) => esp.IdEspecialidad == props.IdEspecialidad
            );

            if (especialidadEncontrada) {
                formData.IdOrigenCita = especialidadEncontrada.IdInterconsulta;
            }
        } else {
            formData.IdOrigenCita = props.itemSeleccion?.IdInterconsulta;
        }

    } else if (props.tipoSeleccion === 'CitaControl') {
        formData.IdOrigenCita = props.itemSeleccion?.IdCitaControl;
    } else if (props.tipoSeleccion === 'CitaProxima') {
        formData.IdOrigenCita = props.itemSeleccion?.IdCitaPorInterconsulta;
    } else if (props.tipoSeleccion === 'Procedimiento') {
        formData.IdOrigenCita = props.itemSeleccion?.IdCita;
    }

    try {
        const { data } = await axios.post('/consulta-externa/citas/WebS_InsertarCita', formData);
        isModalLoading.value = false;
        if (data.success) {
            if (!multiples_citas.value) {

                imprimirCita(data.IdCita, false);
                if (!vienePreQuirurgico) {
                    if (props.conSuccessReferencia) {
                        emit('successReferencia', props.itemSeleccion);
                    } else {
                        emit('cerrarModal', data);
                    }
                } else {
                    const id = programacion_id.value;
                    if (id) {
                        await cargarCupos(id);
                    }
                    await cargarListasRelacionadasPaciente();
                }

            } else {
                // showAlert("VALIDACIÓN REALIZADA", data.mensaje, "success");
                isModalLoading.value = true
                const externalId = await imprimirFormatoCita(data.IdCita, false, true);
                if (externalId) {
                    const externalIds = JSON.parse(localStorage.getItem('external_ids_generados') || '[]');
                    externalIds.push({
                        IdCita: data.IdCita,
                        externalId: externalId
                    });
                    localStorage.setItem('external_ids_generados', JSON.stringify(externalIds));
                }

                const id = programacion_id.value;
                if (id) {
                    await cargarCupos(id);
                }
                await cargarListasRelacionadasPaciente();
            }
        } else {
            if (data.IdCita) {
                const mensaje = data.mensaje + ` ¿DESEA ANULAR LA CITA RELACIONADA PARA PODER GENERAR LA NUEVA CITA?`;
                const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
                if (!confirmado) return;

                const respuesta = await anularFormatoCita(data.IdCita, false, 'POR CITA ADICIONAL SOLICITADO POR EL PACIENTE');
                if (respuesta?.success) {
                    citasPendientes.value = citasPendientes.value.filter(cita => cita.IdCita !== data.IdCita);
                    generarCita(cupo, false);
                } else {
                    showAlert("LA SOLICITUD NO PUDO SER PROCESADA", respuesta.mensaje, "warning");
                }
            } else {
                showAlert("OPERACIÓN CANCELADA", data.mensaje, "warning", false, true);
            }
        }
    } catch (error) {
        isModalLoading.value = false;
        showAlert("OPERACIÓN CANCELADA", error.message || 'Ocurrió un error', "warning");
    }
};


const clearCalendar = () => {
    searchQuery.value = ''
    fechaSeleccionadaCalendario.value = null
    form.value.servicio_id = null
    form.value.medico_id = null
    servicio_id.value = null
    medico_id.value = null
    turnoSeleccionado.value = null
    sexoSeleccionado.value = null
    EsAdicional.value = 0
};

async function obtenerDatosSis() {
    try {
        showAlert("VERIFICANDO AFILIACIÓN SIS ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        const response = await axios.post('/core/servicios/obtenerDatosSisCompletos', {
            numerodocumento: props.paciente.NroDocumento,
            tipodocumento: props.paciente.IdDocIdentidad,
            consultareniec: true,
            tipopersona: 0,
            modulo_origen: 'admicion_ce',
        });

        if (response?.data.respuesta == 1) {
            sis.value = true
            showAlert(
                "CONSULTA REALIZADA",
                "LA CONSULTA FUE REALIZADA DE FORMA EXITOSA",
                "success"
            );
        } else {
            sis.value = false
            showAlert(
                "VALIDACIÓN REALIZADA",
                "LA VALIDACIÓN FUE REALIZADA SIN ÉXITO, POR FAVOR VERIFIQUE EL NPÚMERO DE DOCUMENTO INGRESADO",
                "warning"
            );
        }
    } catch (error) {
        console.error('Error al obtener datos del SIS:', error);
        return null;
    }
}
// Función genérica que observa un ref y lanza alerta si se activa
async function onToggle(name, event) {
    const value = event.target.checked;
    if (name == 'MULTIPLES_CITAS') {
        multiples_citas.value = value;
        if (value) {
            isModalLoading.value = true
            await cargarListasRelacionadasPaciente();
        }
    } else if (name == 'TIPO_CONSULTORIO') {

        form.value.servicio_id = null
        servicio_id.value = null
        fechaSeleccionadaCalendario.value = null
        EsAdicional.value = 0

        let tipo = 1;
        if (value) {
            tipo = 2;
        } else {
            tipo = 1;
        }
        IdTipoConsultorio.value = tipo;
        serviciosFiltrados.value = servicios.value.filter(
            s => s.IdTipoConsultorio == IdTipoConsultorio.value
        );

        programacionFiltrados.value = programacionMedicosServicios.value.filter(
            s => s.IdTipoConsultorio == IdTipoConsultorio.value
        );
        actualizarEventosCalendario();
    } else if (name == 'SIS') {
        if (value === true) {
            obtenerDatosSis();
        }
    }
}

const cargarListasRelacionadasPaciente = async () => {
    try {
        const resultado = await listasRelacionadasPaciente(props.paciente.IdPaciente, 'CITAS');
        citasPendientes.value = resultado.CitasPendientes;
        isModalLoading.value = false
    } catch (error) {
        console.error('Error al cargar programación:', error);
        isModalLoading.value = false
    }
};

const filteredCitas = computed(() => {
    return citasPendientes.value.filter((cita) => {
        const searchText = searchQueryCitas.value.toLowerCase();
        return (
            cita.Servicio.toLowerCase().includes(searchText) ||
            cita.Medico.toLowerCase().includes(searchText) ||
            cita.IdCuentaAtencion.toLowerCase().includes(searchText)
        );
    });
});

// Inicial
onMounted(() => {
    cargarServicios(null)
    limpiarExternalIdsCache()
})

const limpiarExternalIdsCache = () => {
    localStorage.removeItem('external_ids_generados');
};
const clearSearchCitas = () => {
    searchQueryCitas.value = '';
};
const clearSearchProgramacion = () => {
    searchQuery.value = '';
    inputsearchQuery.value?.focus();
};

/*POPPER DE TURNO*/
const toggleTurno = (event) => {
    popoverTurno.value.toggle(event)
}

const seleccionarTurno = (turno) => {
    popoverTurno.value.hide();
    turnoSeleccionado.value = turno
    actualizarEventosCalendario()
};

const iconoTurno = computed(() => {
    if (turnoSeleccionado.value === 'M') return 'ti ti-cloud';
    if (turnoSeleccionado.value === 'T') return 'ti ti-sun';
    return 'ti ti-sun-moon'; // Default: Todos
});

const claseBotonTurno = computed(() => {
    if (turnoSeleccionado.value === 'M') return 'btn-icon btn-outline-info waves-effect';
    if (turnoSeleccionado.value === 'T') return 'btn-icon btn-outline-warning  waves-effect';
    return 'btn-icon btn-outline-primary waves-effect'; // Default: Todos
});
/*POPPER DE TURNO*/


/*POPPER DE SEXO*/
const toggleSexo = (event) => {
    popoverSexo.value.toggle(event)
}

const seleccionarSexo = (sexo) => {
    popoverSexo.value.hide();
    sexoSeleccionado.value = sexo
    actualizarEventosCalendario()
};

const iconoSexo = computed(() => {
    if (sexoSeleccionado.value === 1) return 'ti ti-man';
    if (sexoSeleccionado.value === 2) return 'ti ti-woman';
    return 'ti ti-friends';
});

const claseBotonSexo = computed(() => {
    if (sexoSeleccionado.value === 1) return 'btn-icon btn-outline-success waves-effect';
    if (sexoSeleccionado.value === 2) return 'btn-icon btn-outline-info waves-effect';
    return 'btn-icon btn-outline-primary waves-effect';
});
/*POPPER DE SEXO*/

const generarImpresionGrupalCitas = async () => {
    const datos = JSON.parse(localStorage.getItem('external_ids_generados') || '[]');
    if (!Array.isArray(datos) || datos.length === 0) {
        showAlert(
            "OPERACIÓN CANCELADA",
            "SE HA VERIFICADO QUE NO CUENTA CON CITAS AGRUPADAS, POR FAVOR VERIFIQUE SU INFORMACIÓN",
            "warning",
            false,
            true
        );
        return;
    }

    const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR LA IMPRESIÓN MASIVA DE LAS CITAS GENERADAS?`;
    const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
    if (!confirmado) return;

    // 2. Construyes los arrays
    const externalIds = datos.map(item => item.externalId);
    const idCitaIds = datos.map(item => item.IdCita);

    // 3. Otros campos que quieras enviar
    const formData = {
        Lote: externalIds,
        Grupo: idCitaIds,
        Formato: 'ticket'
    };
    await generarLoteCITAS(formData);
    emit('cerrarModal', null);
};

const generarImpresionResumenCitas = async () => {
    const datos = JSON.parse(localStorage.getItem('external_ids_generados') || '[]');
    if (!Array.isArray(datos) || datos.length === 0) {
        showAlert(
            "OPERACIÓN CANCELADA",
            "SE HA VERIFICADO QUE NO CUENTA CON CITAS AGRUPADAS, POR FAVOR VERIFIQUE SU INFORMACIÓN",
            "warning",
            false,
            true
        );
        return;
    }

    const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR LA IMPRESIÓN DEL RESUMEN DE LAS CITAS GENERADAS?`;
    const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
    if (!confirmado) return;

    // Construir los arrays
    const idCitaIds = datos.map(item => item.IdCita);

    const formData = {
        Lote: null,
        Grupo: idCitaIds,
        Formato: 'ticket'
    };

    await generarLoteCITAS(formData);

    // Confirmar si se desea generar una copia
    const copiar = await showAlertConfirmacion(
        'COPIA DE IMPRESIÓN',
        '¿DESEA GENERAR UNA COPIA ADICIONAL DEL RESUMEN DE CITAS?',
        'question'
    );

    if (copiar) {
        await generarLoteCITAS(formData);
    }

    emit('cerrarModal', null);
};

</script>

<template>
    <ModalLoader v-if="isModalLoading" />
    <div class="d-flex justify-content-end align-items-center mb-3">
        <label for="sis" class="mb-0 me-2">VOLVER A CONSULTAR SIS</label>
        <ToggleSwitch v-model="sis" @change="onToggle('SIS', $event)" />
    </div>
    <div class="row text-uppercase">
        <!-- Sección 1 -->
        <div class="col-12 col-md-12 col-lg-3">
            <!-- Contenedor principal con separación -->
            <div class="d-flex justify-content-between align-items-center w-100">

                <!-- IZQUIERDA: Switch de Procedimientos -->
                <div class="d-flex align-items-center gap-2">
                    <label for="IdTipoConsultorio" class="mb-0">PROCEDIMIENTOS</label>
                    <ToggleSwitch v-model="switchIdTipoConsultorio" :disabled="switchDesactivado"
                        @change="onToggle('TIPO_CONSULTORIO', $event)" />
                </div>

                <!-- DERECHA: Botones -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Botón de limpiar -->
                    <button type="button" class="btn btn-icon btn-outline-danger waves-effect" @click="clearCalendar">
                        <span class="ti ti-filter-off ti-md"></span>
                    </button>

                    <!-- Botón de seleccionar turno -->
                    <button type="button" class="btn" :class="claseBotonTurno" @click="toggleTurno">
                        <span :class="`${iconoTurno} ti-md`"></span>
                    </button>

                    <!-- Popover de turno -->
                    <Popover ref="popoverTurno">
                        <div class="flex flex-row gap-2 p-2">
                            <button type="button" class="btn btn-icon btn-outline-primary waves-effect me-1"
                                title="Todos" @click="seleccionarTurno(null)">
                                <span class="ti ti-sun-moon ti-md"></span>
                            </button>
                            <button type="button" class="btn btn-icon btn-outline-info me-1" title="Mañana"
                                @click="seleccionarTurno('M')">
                                <span class="ti ti-cloud ti-md"></span>
                            </button>
                            <button type="button" class="btn btn-icon btn-outline-warning" title="Tarde"
                                @click="seleccionarTurno('T')">
                                <span class="ti ti-sun ti-md"></span>
                            </button>
                        </div>
                    </Popover>

                    <!-- Botón de seleccionar sexo -->
                    <button type="button" class="btn" :class="claseBotonSexo" @click="toggleSexo">
                        <span :class="`${iconoSexo} ti-md`"></span>
                    </button>

                    <!-- Popover de sexo -->
                    <Popover ref="popoverSexo">
                        <div class="flex flex-row gap-2 p-2">
                            <button type="button" class="btn btn-icon btn-outline-primary waves-effect me-1"
                                title="Ambos" @click="seleccionarSexo(null)">
                                <span class="ti ti-friends ti-md"></span>
                            </button>
                            <button type="button" class="btn btn-icon btn-outline-success waves-effect me-1"
                                title="Hombre" @click="seleccionarSexo(1)">
                                <span class="ti ti-man ti-md"></span>
                            </button>
                            <button type="button" class="btn btn-icon btn-outline-info waves-effect" title="Mujer"
                                @click="seleccionarSexo(2)">
                                <span class="ti ti-woman ti-md"></span>
                            </button>
                        </div>
                    </Popover>
                </div>
            </div>


            <div class="mt-2">
                <BaseCombo :modelValue="form.servicio_id" :options="serviciosFiltrados" optionLabel="Servicio"
                    placeholder="SELECCIONE UN SERVICIO" optionValue="IdServicio" @update:modelValue="onChangeServicio"
                    :filter="true" />
            </div>

            <div class="mt-2">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" placeholder="BÚSQUEDA POR MÉDICO, FECHA, SERVICIO ..."
                        v-model="searchQuery" ref="inputsearchQuery" />
                    <button class="btn btn-outline-danger" @click="clearSearchProgramacion">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Calendario -->
            <div class="mt-3">
                <div style="max-height: 32.4rem; overflow-y: auto;">
                    <div class="mb-2 d-flex justify-content-between align-items-center"
                        style="position: sticky; top: 0; background: white; z-index: 10;">
                        <!-- MULTIPLES CITAS Switch-->
                        <div class="d-flex align-items-center gap-2 ms-auto"
                            v-if="servicioSeleccionado && servicioSeleccionado != 0">
                            <label for="multiples_citas" class="mb-0">MULTIPLES CITAS</label>
                            <ToggleSwitch v-model="multiples_citas" @change="onToggle('MULTIPLES_CITAS', $event)" />
                        </div>
                    </div>


                    <ul class="list-group">
                        <template v-for="(progMedSer, index) in programacionFiltrada" :key="index">
                            <li class="list-group-item" @click="seleccionarProgramacionMedicosServicios(progMedSer)"
                                :class="{ 'active': itemProMedicaServicio === progMedSer }"
                                style="font-weight: 620 !important; font-size: 10.5px !important;">
                                <span class="me-2">{{ index + 1 }}.</span> {{ progMedSer.Medico }}<br />
                                <b class="text-primary">{{ progMedSer.Servicio }}</b>
                                <div class="text-end text-success" style="font-size: 11px !important;">
                                    {{ progMedSer.FechaOrdenada }} ({{ progMedSer.Horario }} <b class="text-danger"> -
                                        CUPOS {{ progMedSer.Cupos }}</b> )
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sección 2 -->
        <div class="col-12 col-md-12 col-lg-4">
            <div class="mb-3">
                <FullCalendar :options="ProgramacionCalendario" />
            </div>
        </div>

        <!-- Sección 3 -->
        <div class="col-12 col-md-12 col-lg-3">
            <div>
                <div class="text-center" v-if="itemProMedicaServicio" style="font-size: 13px;">
                    <div><b>{{ itemProMedicaServicio.Medico }} - ({{
                        itemProMedicaServicio.FechaOrdenada
                            }})</b><br><b>{{ itemProMedicaServicio.Servicio }}</b></div>
                </div>
                <div :style="{
                    maxHeight: '38rem',
                    overflowY: 'auto'
                }">
                    <ul class="list-group">
                        <template v-for="(cupo, index) in cupos" :key="index">
                            <li class="list-group-item" style="font-weight: 600 !important; font-size: 11px !important;"
                                :class="cupo.IdCita ? 'list-group-item-danger' : 'list-group-item-success'">
                                <template v-if="cupo.Estado === 'Ocupado'">
                                    <span v-if="cupo.HoraTGrupal">
                                        {{ cupo.NumCupo }}. (HG. {{ cupo.HoraTGrupal }})<br>{{ cupo.Paciente }}
                                    </span>
                                    <span v-else>
                                        {{ cupo.NumCupo }}. {{ cupo.Turno }} ({{ cupo.HoraInicio }} - {{ cupo.HoraFin
                                        }})<br>{{ cupo.Paciente }}
                                    </span>
                                </template>
                                <template v-else>
                                    <span v-if="cupo.HoraTGrupal">
                                        {{ cupo.NumCupo }}. (HG. {{ cupo.HoraTGrupal }})
                                    </span>
                                    <span v-else>
                                        {{ cupo.NumCupo }}. {{ cupo.Turno }} ({{ cupo.HoraInicio }} - {{ cupo.HoraFin
                                        }})
                                    </span>
                                </template>
                                <div class="text-end" style="font-size: 11px !important;">
                                    <template v-if="cupo.Estado === 'Ocupado'">
                                        <CitaBotones :cita="cupo" :onVerCita="verCita" :onImprimirCita="imprimirCita"
                                            :onNotificarCita="notificarCita" :onVerFUA="verFUA"
                                            :IdFuenteFinanciamiento="IdFuenteFinanciamiento" :onAnularCita="anularCita"
                                            :moduloActual="moduloActual" />
                                    </template>

                                    <template v-else>
                                        <button v-if="cupo.Estado !== 'Bloqueado'"
                                            class="btn btn-sm btn-icon btn-primary" @click="generarCita(cupo)">
                                            <i class="ti ti-calendar text-white"></i>
                                        </button>
                                    </template>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>

                <button class="btn btn-danger mt-2" style="width: 100%;" v-if="EsAdicional == 1"
                    @click="generarCita(cupo)">
                    <i class="fas fa-user-plus"></i> PACIENTE ADICIONAL
                </button>
            </div>
        </div>


        <!-- Sección 4 -->
        <div class="col-12 col-md-12 col-lg-2">
            <!-- Buscador citaAdicionales -->
            <div class="mb-2" v-if="citasPendientes.length">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Buscar Cita..." v-model="searchQueryCitas" />
                    <button class="btn btn-outline-danger" @click="clearSearchCitas">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div :style="{ maxHeight: '6rem', overflowY: 'auto' }" v-if="citaAdicionales.length" class="mb-2">
                <ul class="list-group">
                    <template v-for="(cita, index) in citaAdicionales" :key="cita.IdSolicitudCitaAdicionalCE">
                        <li class="list-group-item"
                            style="font-size: 0.7rem !important; border-color: #7367f0 !important;">
                            <!-- Encabezado -->
                            <div class="d-flex justify-content-between mb-1">
                                <strong class="text-dark">
                                    FECHA DE ADICIONAL: {{ cita.Fecha }}
                                </strong>
                            </div>

                            <!-- Contenido + Acciones alineadas horizontalmente -->
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    CONSULTORIO: <b class="text-dark">{{ cita.Consultorio }}</b> <br>
                                    MÉDICO: <b class="text-dark">{{ cita.Medico }}</b>
                                </div>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>
            <div :style="{ maxHeight: '38rem', overflowY: 'auto' }">
                <div class="row" v-if="multiples_citas">
                    <div class="col-md-6">
                        <button class="btn btn-danger w-100" @click="generarImpresionGrupalCitas">
                            <i class="fas fa-print me-1"></i> IMP.MASIVA
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-primary w-100" @click="generarImpresionResumenCitas">
                            <i class="fas fa-file-alt me-1"></i> IMP.RESUMEN
                        </button>
                    </div>
                </div>


                <ul class="list-group">
                    <template v-for="(cita, index) in filteredCitas" :key="cita.IdCita">
                        <li class="list-group-item"
                            style="font-size: 0.7rem !important; border-color: #28c76f !important;">

                            <!-- Encabezado -->
                            <div class="justify-content-between mb-1">
                                <strong class="text-dark">
                                    CUENTA: {{ cita.IdCuentaAtencion }} - FECHA: {{ cita.Fecha }}
                                    <span v-if="cita.Validar == 1"> - ({{ cita.HoraInicio }} - {{ cita.HoraFin
                                        }})</span>
                                </strong>
                            </div>

                            <!-- Contenido + Acciones al mismo nivel -->
                            <div class=" justify-content-between align-items-start">
                                <div>
                                    SER: {{ cita.Servicio }} <br>
                                    MÉD: {{ cita.Medico }}
                                </div>
                                <div class="text-end ms-3">
                                    <CitaBotones :cita="cita" :onVerCita="verCita" :onImprimirCita="imprimirCita"
                                        :onNotificarCita="notificarCita" :onVerFUA="verFUA" :onAnularCita="anularCita"
                                        :moduloActual="moduloActual" :IdFuenteFinanciamiento="IdFuenteFinanciamiento" />
                                </div>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>
    <ModalPDF ref="modalPDF" />
    <!-- Componente del diálogo -->
    <MotivoAnulacionDialog ref="motivoAnulacionRef" />
</template>
