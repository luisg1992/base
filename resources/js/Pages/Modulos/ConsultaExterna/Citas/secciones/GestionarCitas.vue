<script setup>
import { nextTick } from 'vue';
import { ref, computed, getCurrentInstance } from 'vue'

// Importación de PrimeVue Tabs
import Tabs from 'primevue/tabs'
import TabList from 'primevue/tablist'
import Tab from 'primevue/tab'
import TabPanels from 'primevue/tabpanels'
import TabPanel from 'primevue/tabpanel'

import axios from 'axios'
import InputText from 'primevue/inputtext';
import ToggleSwitch from 'primevue/toggleswitch';
import BaseCombo from "@/components/WSelect/WSelect.vue";
import CitaBotones from '@/components/citas/CitaBotones.vue'
import ModalPDF from '@/components/ModalPDF.vue';
import { verReferencia } from '../funciones/verReferencia.js';

import ModalBase from '@/components/BaseModal.vue'
import CitarFormulario from './CitarFormulario.vue'
import CitaControl from './RegistroAdicional/CitaControl.vue'
// import CitaProcedimiento from './RegistroAdicional/CitaProcedimiento.vue'
import CitaRiesgoQuirurgico from './RegistroAdicional/CitaRiesgoQuirurgico.vue'
import CitaProxima from './RegistroAdicional/CitaProxima.vue'


import PacienteNombres from '@/components/paciente/PacienteNombres.vue'
import PacienteFiliacionSIS from '@/components/paciente/PacienteFiliacionSIS.vue';
import PacienteRefconSinDocumento from '@/components/paciente/PacienteRefconSinDocumento.vue';
import DemandaInsatisfecha from './RegistroAdicional/CitaDemandaInsatisfecha.vue'
import {
    verFormatoCita,
    imprimirFormatoCita,
    confirmarYAnularCita,
    verFormatoFUA,
    listasRelacionadasPaciente
} from '@/services/ConsultaExterna/Citas/gestionCitas';
import MotivoAnulacionDialog from '@/components/citas/MotivoAnulacionDialog.vue'

const props = defineProps(['moduloActual']);
const { proxy } = getCurrentInstance();

const modalSIS = ref();
const modalBusquedaNombres = ref(false)
const modalREFCON = ref();
let modalCitaVisible = ref(false)
let pacienteSeleccionado = ref(null)
let tipoSeleccion = ref(null)
let itemSeleccion = ref(null)
let IdFuenteFinanciamiento = ref(1)
let IdTipoFinanciamiento = ref(1)
let IdEspecialidad = ref(null)
let activeTab = ref("0")


// Variables reactivas
let tiposDocumento = [
    { label: 'DNI', value: 1 },
    { label: 'CARNET DE EXTRANJERÍA', value: 2 },
    { label: 'HISTORIA CLÍNICA', value: -1 },
    { label: 'DOCUMENTO DE IDENTIDAD EXTRANJERO', value: 4 },
    { label: 'C.U.I', value: 7 },
    { label: 'CÓDIGO RECIÉN NACIDO', value: 5 },
    { label: 'PASAPORTE', value: 3 },
];
let tipoDocumento = ref(1);
let nroDocumento = ref('');
let inputDocumentoRef = ref(null);

const modalCitaControl = ref(null)
// const modalCitaProcedimiento = ref(null)
const modalDemandaInsatisfecha = ref(null)
const modalCitaRiesgoQuirurgico = ref(null)
const modalCitaProxima = ref(null)
let afiliacion = ref(false);
let nombres = ref(false);
let reniec = ref(false);
let sis = ref(false);
let refcon = ref(false);


// Datos del paciente
let idPaciente = ref(null)
let nombrePaciente = ref('')
let dniPaciente = ref('')
let hcPaciente = ref('')
let edadPaciente = ref('')
let sexoPaciente = ref('')
let IdTipoSexo = ref(null)
let correoPaciente = ref('')
let telefonoPaciente = ref('')
let fotoPaciente = ref(null)
let loadingFoto = ref(true)
let loadingDatosPaciente = ref(false)
let isModalLoading = ref(false)
let validacionCuentasPendientes = ref(false)
let validacionMensajeCuentasPendientes = ref(null)

// Listas de referencias
let referencias = ref([]);
let activeTabReferencias = ref(1);
let searchQueryReferencias = ref('');
let visibleReferencias = ref(false)
let visibleRegistrarNuevaCitaControl = ref(false)


let citasPendientes = ref([]);
let activeTabCitas = ref(1);
let searchQueryCitas = ref('');
let visibleCitas = ref(false)


let interconsultasPendientes = ref([]);
let activeTabInterconsultas = ref(1);
let searchQueryInterconsultas = ref('');
let visibleInterconsultas = ref(false)


let citaAdicionales = ref([]);
let citaControlPendientes = ref([]);
let activeTabCitaControl = ref(1);
let searchQueryCitaControl = ref('');
let visibleCitaControl = ref(false)

// Visualizar Referencia
let IdEspecialidades = ref([]);
const motivoAnulacionRef = ref(null)
const modalPDF = ref(null);

function calcularEdadCompleta(fechaNacimientoStr) {
    const fechaNacimiento = new Date(fechaNacimientoStr)
    const hoy = new Date()
    let años = hoy.getFullYear() - fechaNacimiento.getFullYear()
    let meses = hoy.getMonth() - fechaNacimiento.getMonth()
    let dias = hoy.getDate() - fechaNacimiento.getDate()

    if (dias < 0) {
        meses--
        dias += new Date(hoy.getFullYear(), hoy.getMonth(), 0).getDate()
    }
    if (meses < 0) {
        años--
        meses += 12
    }
    return { years: años, months: meses, days: dias }
}

function limpiarPaciente() {
    pacienteSeleccionado.value = null
    idPaciente.value = null
    nombrePaciente.value = ''
    dniPaciente.value = ''
    hcPaciente.value = ''
    edadPaciente.value = ''
    sexoPaciente.value = ''
    IdTipoSexo.value = null
    correoPaciente.value = ''
    telefonoPaciente.value = ''
    fotoPaciente.value = null
    loadingFoto.value = true
    loadingDatosPaciente.value = false

    // tipoSeleccion = null
    // itemSeleccion = null

    reniec.value = false
    sis.value = false
    refcon.value = false

    referencias.value = []
    searchQueryReferencias.value = '';
    activeTabReferencias.value = 1
    visibleReferencias.value = false

    citasPendientes.value = []
    searchQueryCitas.value = '';
    activeTabCitas.value = 1
    visibleCitas.value = false

    interconsultasPendientes.value = []
    searchQueryInterconsultas.value = '';
    activeTabInterconsultas.value = 1
    visibleInterconsultas.value = false

    citaAdicionales.value = []
    citaControlPendientes.value = []
    searchQueryCitaControl.value = '';
    activeTabCitaControl.value = 1
    visibleCitaControl.value = false
    visibleRegistrarNuevaCitaControl.value = false

    IdEspecialidad.value = null
    afiliacion.value = false

    validacionCuentasPendientes.value = false
    validacionMensajeCuentasPendientes.value = null
}

async function actualizarDatosPaciente(datos) {
    try {
        loadingDatosPaciente.value = false

        loadingFoto.value = true
        const defaultFoto = datos.IdTipoSexo == 1
            ? '../../assets/img/sexo1.gif'
            : '../../assets/img/sexo2.gif'
        fotoPaciente.value = defaultFoto

        IdTipoSexo.value = datos.IdTipoSexo
        const edadCompleta = calcularEdadCompleta(datos.FechaNacimiento)

        nombrePaciente.value = `${datos.ApellidoPaterno || ''} ${datos.ApellidoMaterno || ''} ${datos.PrimerNombre || ''} ${datos.SegundoNombre || ''} ${datos.TercerNombre || ''}`.trim();
        dniPaciente.value = `DNI: ${datos.NroDocumento}`
        hcPaciente.value = `HC: ${datos.NroHistoriaClinica ?? 'SIN DATOS'}`
        edadPaciente.value = `EDAD: ${edadCompleta.years} A, ${edadCompleta.months} M, ${edadCompleta.days} D`
        sexoPaciente.value = `SEXO: ${datos.IdTipoSexo == '1' ? 'MASCULINO' : 'FEMENINO'}`
        correoPaciente.value = datos.Email ?? ''

        const telefonoLimpio = (datos.Telefono ?? '').replace(/\s+/g, '');
        telefonoPaciente.value = telefonoLimpio.length === 9 ? telefonoLimpio : '';

        tipoDocumento.value = Number(datos.IdDocIdentidad)
        nroDocumento.value = datos.NroDocumento

        idPaciente.value = datos.IdPaciente ?? null

        loadingDatosPaciente.value = true
        loadingFoto.value = false
        visibleRegistrarNuevaCitaControl.value = true

        await ejecutarValidacionCuentasPendientes();

    } catch (e) {
        loadingFoto.value = false
        loadingDatosPaciente.value = true
    }
}

const buscarPacienteAdmisionCitas = async () => {
    limpiarPaciente()
    nroDocumento.value = nroDocumento.value.replace(/\s+/g, '');
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
            pacienteSeleccionado.value = response?.data.data
            await actualizarDatosPaciente(response?.data.data)
            reniec.value = true
            /*SI ES EXTRANJERO NO PASA POR RENIEC*/

            if (proxy?.$can?.(`${props.moduloActual}.tab.gestionar.citas.busqueda.por.servicios.api`)) {
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
                const parametros = {
                    tipodocumento: tipoDocumento.value,
                    numerodocumento: nroDocumento.value,
                    consultareniec: true,
                    tipopersona: 0,
                }
                await obtenerDatosSis(parametros);
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
            } else {
                // /*CONSULTA DATOS DE REFERENCIAS*/
                const parametrosRefcon = {
                    tipodocumento: tipoDocumento.value,
                    numerodocumento: nroDocumento.value,
                    consultasis: true,
                    generarlistareferencias: true,
                    consultaCheck: false,
                }
                await consultaReferenciasPacienteCitas(parametrosRefcon);
                // /*CONSULTA DATOS DE REFERENCIAS*/
            }
        } else {
            if (proxy?.$can?.(`${props.moduloActual}.tab.gestionar.citas.busqueda.por.servicios.api`)) {
                reniec.value = false
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
                const parametros = {
                    tipodocumento: tipoDocumento.value,
                    numerodocumento: nroDocumento.value,
                    consultareniec: false,
                    tipopersona: 0,
                }
                await obtenerDatosSis(parametros);
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
            } else {
                showAlert(
                    "OPERACIÓN CANCELADA",
                    "EL PACIENTE CONSULTADO NO FUE UBICADO EN NUESTRA BASE DE DATOS, POR FAVOR VERIFIQUE EL DOCUMENTO INGRESADO",
                    "warning"
                );
            }
        }
    } catch (error) {
        if (error.response) {
            showAlert('ERROR', error.response.data.message || 'Error al buscar paciente.', 'error');
        } else {
            showAlert('ERROR', error.message || 'Error de conexión.', 'error');
        }
    }
}

const ejecutarBuscarPacienteAdmisionCitas = async () => {
    await buscarPacienteAdmisionCitas();
    if (validacionCuentasPendientes.value) {
        showAlert(
            "VALIDACIÓN REALIZADA",
            validacionMensajeCuentasPendientes.value,
            "warning",
            true,
            true
        );
    }
};

const ejecutarValidacionCuentasPendientes = async () => {
    validacionCuentasPendientes.value = false
    const { data } = await axios.post('/consulta-externa/citas/WebS_Validar_Cuentas_Pendientes', {
        IdPaciente: idPaciente.value
    });
    if (data.success) {
        validacionCuentasPendientes.value = true
        validacionMensajeCuentasPendientes.value = data.mensaje
    }
};

async function obtenerDatosSis(parametros = {}) {
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
            sis.value = true
            if (parametros.consultareniec == false) {
                reniec.value = true
                if (response?.data.data) {
                    pacienteSeleccionado.value = response?.data.data
                    await actualizarDatosPaciente(response?.data.data);
                }
            }

            if (response?.data.contingencia == 'S') {
                /*SI VIENE DEL CONSUMO DEL SIS DE CONTINGENCIA, TIENE QUE LLAMAR A UN MODAL PARA REGISTRAR DATOS ADICIONALES*/
            }

            // /*CONSULTA DATOS DE REFERENCIAS*/
            const parametrosRefcon = {
                tipodocumento: parametros.tipodocumento,
                numerodocumento: parametros.numerodocumento,
                consultasis: true,
                generarlistareferencias: true,
                consultaCheck: false,
            }
            await consultaReferenciasPacienteCitas(parametrosRefcon);
            // /*CONSULTA DATOS DE REFERENCIAS*/

        } else {

            showAlert("CONSULTA REALIZADA", response?.data.descripcion, 'warning');
            if (response?.data.codigo != '14' || response?.data.codigo != 14) {
                if (idPaciente.value) {
                    // /*CONSULTA DATOS DE REFERENCIAS*/
                    const parametrosRefcon = {
                        tipodocumento: parametros.tipodocumento,
                        numerodocumento: parametros.numerodocumento,
                        consultasis: false,
                        generarlistareferencias: true,
                        consultaCheck: false,
                    }
                    await consultaReferenciasPacienteCitas(parametrosRefcon);
                    // /*CONSULTA DATOS DE REFERENCIAS*/
                } else {
                    showAlert(
                        "CONSULTA REALIZADA",
                        "EL PACIENTE NO PUDO SER UBICADO NI EN RENIEC NI EN EL SIS, VERIFIQUE EL INGRESO DE SU INFORMACIÓN",
                        "error"
                    );
                }
            } else {
                if (idPaciente.value) {
                    IdFuenteFinanciamiento.value = 1
                    IdTipoFinanciamiento.value = 1
                    sis.value = false
                    refcon.value = false
                    referencias.value = [];

                    await cargarListasRelacionadasPaciente();
                    if (validacionCuentasPendientes.value == false) {
                        await generarCitaParticular();
                    }
                } else {
                    showAlert(
                        "CONSULTA REALIZADA",
                        "EL PACIENTE NO PUDO SER UBICADO NI EN RENIEC NI EN EL SIS, VERIFIQUE EL INGRESO DE SU INFORMACIÓN",
                        "error"
                    );
                }
            }
        }
    } catch (error) {
        console.error('Error al obtener datos del SIS:', error);
        return null;
    }
}

const procesarDatosREFCONSinDocumento = async (parametros = {}) => {
    if (!parametros.nroHistoriaClinica) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO NÚMERO DE HISTORIA CLÍNICA ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.',
            'warning', false, true
        );
    }

    try {
        showAlert("VERIFICANDO DATOS EN RENIEC ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        const response = await axios.post('/personas/persona/PacienteBuscarTipoAndDocumento', {
            nombremodulo: 'ADMISIÓN Y CITAS',
            tipodocumento: '-1',
            numerodocumento: parametros.nroHistoriaClinica,
            tipopersona: 0,
        })

        /*VALIDAMOS LA RESPUESTA DE RENIEC SEA EXITOSA O LA CONSULTA A LA BD*/
        if (response.data.success) {
            /*SI ES EXTRANJERO NO PASA POR RENIEC*/
            pacienteSeleccionado.value = response?.data.data
            await actualizarDatosPaciente(response?.data.data)
            reniec.value = true
            /*SI ES EXTRANJERO NO PASA POR RENIEC*/
            await consultaReferenciasPacienteCitas(parametros)
        } else {
            showAlert(
                "OPERACIÓN CANCELADA",
                "EL PACIENTE CONSULTADO NO FUE UBICADO EN NUESTRA BASE DE DATOS, POR FAVOR VERIFIQUE EL DOCUMENTO INGRESADO",
                "warning", true, true
            );
        }
    } catch (error) {
        if (error.response) {
            showAlert('ERROR', error.response.data.message || 'Error al buscar paciente.', 'error', true, true);
        } else {
            showAlert('ERROR', error.message || 'Error de conexión.', 'error', true, true);
        }
    }
}

async function consultaReferenciasPacienteCitas(parametros = {}) {
    referencias.value = [];

    showAlert("VERIFICANDO REFERENCIA ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);

    const response = await axios.post('/core/servicios/consultaReferenciaPaciente', {
        numerodocumento: parametros.numerodocumento ?? nroDocumento.value,
        tipodocumento: parametros.tipodocumento ?? tipoDocumento.value,
        consultasis: parametros.consultasis ?? sis.value,
        generarlistareferencias: parametros.generarlistareferencias ?? true,
        IdTipoServicio: 1,
        IdPaciente: idPaciente.value,
        modulo_origen: 'admicion_ce',
    });

    if (!parametros.consultaCheck) {
        await cargarListasRelacionadasPaciente();
    }

    const data = response?.data || {};
    const referenciasRefcon = data.referencia ?? [];
    const referenciasGalenos = data.referenciaGalenos?.data ?? [];

    if (referenciasRefcon.length > 0 || referenciasGalenos.length > 0) {

        visibleReferencias.value = true;
        sis.value = true;
        refcon.value = true;

        IdFuenteFinanciamiento.value = 3;
        IdTipoFinanciamiento.value = 2;

        // 🔹 Normalizar el texto quitando espacios
        const normalizarReferencia = (texto) => texto?.replace(/\s+/g, '') ?? '';

        // 🔹 Obtener referencias Refcon válidas
        const listaRefcon = referenciasRefcon
            .filter(ref => !(ref.descUpsDestino?.toUpperCase().includes("EMER")))
            .map(ref => {
                const fechaEnvio = ref.fechaEnvio ?? '';
                let dia = '', mes = '', anio = '';

                if (fechaEnvio.length === 8) {
                    anio = fechaEnvio.substring(0, 4);
                    mes = fechaEnvio.substring(4, 6);
                    dia = fechaEnvio.substring(6, 8);
                } else if (fechaEnvio.includes('-')) {
                    const partes = fechaEnvio.split('-');
                    [anio, mes, dia] = partes.length === 3 ? partes : ['', '', ''];
                }

                return {
                    ...ref,
                    fechaFormateada: `${dia}/${mes}/${anio}`,
                    anio,
                    Tipo: 'Refcon'
                };
            });

        // 🔹 Crear un Set con referencias únicas de RefCon
        const referenciasRefconSet = new Set(
            listaRefcon.map(ref => normalizarReferencia(ref.numeroReferencia))
        );

        // 🔹 Filtrar Galenos que no estén en RefCon
        const listaGalenosFiltrada = referenciasGalenos
            .filter(ref => {
                const nroRef = normalizarReferencia(ref.NroReferencia);
                return !referenciasRefconSet.has(nroRef);
            })
            .map(ref => ({
                idReferencia: ref.IdReferenciaGalenos,
                numeroReferencia: ref.NroReferencia,
                especialidad: ref.Especialidad,
                codigoEspecialidad: ref.IdEspecialidad,
                Tipo: ref.Tipo ?? 'Galenos',
            }));

        // 🔹 Combinar listas (primero RefCon, luego Galenos sin duplicados)
        referencias.value = [...listaRefcon, ...listaGalenosFiltrada];

        // 🔹 Actualizar datos del paciente si es necesario
        if (parametros.consultasis === false && data.data) {
            pacienteSeleccionado.value = data.data;
            await actualizarDatosPaciente(data.data);
        }

        showAlert(
            "CONSULTA REALIZADA",
            "LA CONSULTA FUE REALIZADA DE FORMA EXITOSA",
            "success"
        );
    } else {
        // Configurar financiamiento si no hay referencias
        if (parametros.consultasis || sis.value) {
            IdFuenteFinanciamiento.value = 3;
            IdTipoFinanciamiento.value = 2;
        } else {
            IdFuenteFinanciamiento.value = 1;
            IdTipoFinanciamiento.value = 1;
        }

        refcon.value = false;

        showAlert(
            "CONSULTA REALIZADA",
            "EL PACIENTE CONSULTADO NO CUENTA CON REFERENCIAS ACTIVAS",
            "warning"
        );

        if (validacionCuentasPendientes.value == false) {
            if (!parametros.consultasis && !sis.value) {
                generarCitaParticular();
            } else if (parametros.consultasis && !sis.value) {
                generarCitaParticular();
            }
        }
    }
}

const clearSearchReferencias = () => {
    searchQueryReferencias.value = '';
};

const filteredreferencias = computed(() => {
    const searchText = searchQueryReferencias.value.toLowerCase();
    return referencias.value.filter((referencia) => {
        return (
            (referencia.numeroReferencia?.toLowerCase() ?? '').includes(searchText) ||
            (referencia.descUpsDestino?.toLowerCase() ?? '').includes(searchText) ||
            (referencia.especialidad?.toLowerCase() ?? '').includes(searchText)
        );
    });
});

const clearSearchCitas = () => {
    searchQueryCitas.value = '';
};
const clearSearchDocumento = () => {
    nroDocumento.value = '';
    inputDocumentoRef.value?.focus();
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

const clearSearchInterconsultas = () => {
    searchQueryInterconsultas.value = '';
};

const filteredInterconsultas = computed(() => {
    return interconsultasPendientes.value.filter((interconsulta) => {
        const searchText = searchQueryInterconsultas.value.toLowerCase();
        return (
            interconsulta.EspecialidadDestino.toLowerCase().includes(searchText) ||
            interconsulta.ServicioOrigen.toLowerCase().includes(searchText) ||
            interconsulta.MedicoOrigen.toLowerCase().includes(searchText)
        );
    });
});


const clearSearchCitaControl = () => {
    searchQueryCitaControl.value = '';
};
const filteredCitaControl = computed(() => {
    return citaControlPendientes.value.filter((cita) => {
        const searchText = searchQueryCitaControl.value.toLowerCase();
        return (
            cita.EspecialidadDestino.toLowerCase().includes(searchText) ||
            cita.MedicoOrigen.toLowerCase().includes(searchText)
        );
    });
});

// Importas y configuras tu composable como ya tienes:
const { visualizarReferencia } = verReferencia({
    openPDFModal: (url, title, type) => {
        modalPDF.value.openModal(url, title, type);
    }
});

function onVisualizarReferencia(refItem) {
    visualizarReferencia(refItem);
}

// Función para generar la cita del paciente
async function citarPaciente(refItem, tipo) {
    if (tipo == 'Referencia') {
        IdEspecialidad.value = null
        IdFuenteFinanciamiento.value = 3
        IdTipoFinanciamiento.value = 2
    } else if (tipo == 'Particular') {
        IdEspecialidad.value = null
        IdFuenteFinanciamiento.value = 1
        IdTipoFinanciamiento.value = 1
    } else if (tipo == 'Interconsulta') {
        IdEspecialidad.value = refItem.IdEspecialidad
    } else if (tipo == 'ReferenciaGalen') {
        IdEspecialidad.value = refItem.codigoEspecialidad
    } else if (tipo == 'CitaControl') {
        IdEspecialidad.value = refItem.IdEspecialidad
    } else if (tipo == 'Procedimiento') {
        IdEspecialidad.value = null
    }

    /*VALIDACIÓN DE ACCESO MEDIANTE LAS ESPECIALIDADES ASIGNADAS*/
    if (tipo !== 'Particular') {
        const idEsp = IdEspecialidad.value;
        const codUps = refItem?.upsDestino;

        // Validar si al menos uno de los dos valores existe y no es null/undefined/0/''
        if (idEsp ?? codUps) {
            const { data } = await axios.post('/consulta-externa/citas/WebS_Validar_EmpleadosEspecialidades', {
                IdEspecialidad: idEsp ?? null,
                CodUps: codUps ?? null
            });

            if (!data.success) {
                showAlert("ACCESO DENEGADO", data.mensaje, "warning", false, true);
                return;
            }
        }
    }
    /*VALIDACIÓN DE ACCESO MEDIANTE LAS ESPECIALIDADES ASIGNADAS*/
    if (IdEspecialidad) {
        itemSeleccion.value = refItem ? refItem : null
        tipoSeleccion.value = tipo
        modalCitaVisible.value = true
    } else {
        showAlert("VALIDACIÓN REALIZADA", "LA ESPECIALIDAD NO SE ENCUENTRA DEFINIDA, POR FAVOR VERIFIQUE SU SELECCIÓN O PRESIONE F5 PARA VOLVER A INTENTAR LA CONSULTA SOLICITADA.", "warning", false, true);
    }
}

const cerrarModal = async (valor = false) => {
    isModalLoading.value = true;

    IdEspecialidad.value = null
    itemSeleccion.value = null
    tipoSeleccion.value = null

    modalCitaVisible.value = false
    isModalLoading.value = false;
    IdEspecialidades.value = []
    // if (valor) {
    //     await cargarListasRelacionadasPaciente();
    // }
}

const cerrarModalCita = async (data) => {
    isModalLoading.value = true;
    IdEspecialidad.value = null
    itemSeleccion.value = null
    tipoSeleccion.value = null
    modalCitaVisible.value = false

    await cargarListasRelacionadasPaciente();
    if (data) {
        if (parseInt(data.ImprimirFua) === 1 && proxy?.$can?.(`${props.moduloActual}.registrar.fua.de.paciente.adicional`)) {
            isModalLoading.value = false;
            await verFUA(data.IdCita);
        } else {
            isModalLoading.value = false;
        }
    }
}

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

async function generarCitaParticular() {
    const opcion = await showMultiplesOpciones({
        title: "¿DESEAS REALIZAR ALGUNA OPERACIÓN?",
        text: "SE HA DETECTADO QUE EL PACIENTE NO CUENTA CON SEGURO SIS, POR FAVOR SELECCIONE UNA DE LAS OPCIONES QUE DESEA REALIZAR.",
        icon: "warning",
        confirmText: "CONSULTA SIS",
        cancelText: "CITA PARTICULAR",
        denyText: "CANCELAR",
        // extraText: "CONSULTA REFCON" // nuevo botón
    });

    switch (opcion) {
        case 'confirm': // CONSULTA SIS
            const parametros = {
                tipodocumento: tipoDocumento.value,
                numerodocumento: nroDocumento.value,
                consultareniec: true,
                tipopersona: 0,
            }
            await obtenerDatosSis(parametros);
            break;

        case 'cancel': // CITA PARTICULAR
            IdFuenteFinanciamiento.value = 1;
            IdTipoFinanciamiento.value = 1;
            citarPaciente('', 'Particular');
            break;

        case 'deny': // CANCELAR
            showAlert(
                "OPERACIÓN CANCELADA",
                "LA OPERACIÓN FUE CANCELADA. POR FAVOR CONTINÚE CON EL PROCESO DESEADO.",
                "warning"
            );
            break;

        default:
            break;
    }
}

const procesarDatosSIS = async (datos, tipoPaciente) => {
    afiliacion.value = false
    if (datos) {
        if (tipoPaciente) {
            IdFuenteFinanciamiento.value = 3
            IdTipoFinanciamiento.value = 2
            sis.value = true
        } else {
            IdFuenteFinanciamiento.value = 1
            IdTipoFinanciamiento.value = 1
            sis.value = false
        }

        tipoDocumento.value = Number(datos.IdDocIdentidad);
        nroDocumento.value = datos.NroDocumento

        reniec.value = true
        pacienteSeleccionado.value = datos
        await actualizarDatosPaciente(datos);

        // /*CONSULTA DATOS DE REFERENCIAS*/
        const parametrosRefcon = {
            tipodocumento: tipoDocumento.value,
            numerodocumento: nroDocumento.value,
            consultasis: true,
            generarlistareferencias: true,
            consultaCheck: false,
        }
        consultaReferenciasPacienteCitas(parametrosRefcon);
        // /*CONSULTA DATOS DE REFERENCIAS*/

    } else {
        IdFuenteFinanciamiento.value = 1
        IdTipoFinanciamiento.value = 1
        sis.value = false
        showAlert(
            "OPERACIÓN CANCELADA",
            "LA OPERACIÓN FUE CANCELADA. NO SE HA UBCICADO AL PACIENTE, POR FAVOR VUELVA A INTENTAR LA BÚSQUEDA MEDIANTE SU FILIACIÓN..",
            "warning"
        );
    }

    // Aquí puedes hacer la consulta real al backend con Axios
};

// Función genérica que observa un ref y lanza alerta si se activa
async function onToggle(name, event) {
    const value = event.target.checked;
    if (name == 'AFILIACION') {
        afiliacion.value = value
        if (value) {
            modalSIS.value.openDialog();
        } else {
            modalSIS.value.closeDialog();
        }
    }

    if (name == 'NOMBRES') {
        nombres.value = value
        if (value) {
            modalBusquedaNombres.value = true
        } else {
            modalBusquedaNombres.value = false
        }
    }

    if (value) {
        if (value === true) {
            if (name == 'RENIEC') {

            } else if (name == 'SIS') {
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
                const parametros = {
                    tipodocumento: tipoDocumento.value,
                    numerodocumento: nroDocumento.value,
                    consultareniec: true,
                    tipopersona: 0,
                }
                await obtenerDatosSis(parametros);
                /*TRAS VALIDAR LA RESPUESTA DE RENIEC, VALIDAMOS EL SIS*/
            } else if (name == 'REFCON') {
                if (sis.value) {
                    // /*CONSULTA DATOS DE REFERENCIAS*/
                    const parametrosRefcon = {
                        tipodocumento: tipoDocumento.value,
                        numerodocumento: nroDocumento.value,
                        consultasis: true,
                        generarlistareferencias: true,
                        consultaCheck: true,
                    }
                    await consultaReferenciasPacienteCitas(parametrosRefcon);
                    // /*CONSULTA DATOS DE REFERENCIAS*/
                } else {
                    await nextTick();
                    refcon.value = false;
                }
            }
        }
    }
}

const cargarListasRelacionadasPaciente = async () => {
    try {
        const resultado = await listasRelacionadasPaciente(idPaciente.value, 'TODOS');
        if (resultado) {
            visibleCitas.value = true
            visibleInterconsultas.value = true
            visibleCitaControl.value = true
            citasPendientes.value = resultado.CitasPendientes;
            interconsultasPendientes.value = resultado.Interconsultas;
            citaControlPendientes.value = resultado.CitaControl;
            citaAdicionales.value = resultado.CitaAdicional;
        }
    } catch (error) {
        console.error('Error al cargar programación:', error);
    }
};

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
        modalPDF.value.openModal(resultado.pdf_base64, 'FORMATO FUA', 'base64');
    } else {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", resultado?.mensaje || "LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTNÚE CON EL PROCESO DESEADO.", "warning");
    }
};

const anularCita = async (IdCita) => {
    const respuesta = await confirmarYAnularCita(IdCita, motivoAnulacionRef);
    if (respuesta) {
        citasPendientes.value = citasPendientes.value.filter(cita => cita.IdCita !== IdCita);
        showAlert("OPERACIÓN REALIZADA", "LA CITA FUE ANULADA DE FORMA EXITOSA", "success");
    } else {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", respuesta?.mensaje || "LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTNÚE CON EL PROCESO DESEADO.", "warning");
    }
};

async function registrarNuevaCitaControl() {
    isModalLoading.value = true
    if (!tipoDocumento.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE TIPO DE DOCUMENTO ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.',
            'warning', false, true
        );
    }

    if (tipoDocumento.value === 1 && nroDocumento.value.length !== 8) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL NÚMERO DE DOCUMENTO DEBE TENER 8 CARACTERES.',
            'warning', false, true
        );
    }

    if (!idPaciente.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'REALICE LA CONSULTA DE DATOS NUEVAMENTE, EL PACIENTE NO FUE UBICADO EN EL SISTEMA.',
            'warning', false, true
        );
    }

    try {
        const { data } = await axios.post('/consulta-externa/citas/WebS_PacienteConsultarCitaControl', {
            IdPaciente: idPaciente.value
        });

        if (data.success) {
            isModalLoading.value = false
            if (!pacienteSeleccionado.value) {
                return showAlert(
                    'VALIDACIÓN DE CAMPO REALIZADO',
                    'PARA PODER REGISTRAR UNA CITA CONTROL ES NECESARIA LA BÚSQUEDA DE UN PACIENTE, INGRESE UN DOCUMENTO VÁLIDO O VERIFIQUE SU INFORMACIÓN',
                    'warning', false, true
                );
            }

            const mensaje = `${data.mensaje ?? ''} ¿ESTÁ SEGURO QUE DESEA REALIZAR EL REGISTRO SOLICITADO?`;
            const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');

            if (confirmado) {
                modalCitaControl.value.openDialog(data.mensaje, data.IdCuentaAtencion);
            }
        } else {
            isModalLoading.value = false
            showAlert(
                'VALIDACIÓN DE CAMPO REALIZADO', data.mensaje, 'warning', false, true);

            if (data.IdEstadoVincula === 1) {
                const { IdEspecialidad, IdProximaCita } = data;

                if (IdEspecialidad && IdProximaCita) {
                    onCitaControlGenerada({ IdEspecialidad, IdProximaCita });
                } else {
                    showAlert(
                        'VALIDACIÓN DE CAMPO REALIZADO',
                        'LOS CAMPOS IdEspecialidad Y IdProximaCita SON REQUERIDOS, PERO NO ESTÁN RETORNANDO PARA EL REGISTRO CORRESPONDIENTE, COMUNÍQUESE CON INFORMÁTICA.',
                        'warning', false, true
                    );
                }
            }
        }
    } catch (error) {
        isModalLoading.value = false
        showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO', error.message ?? 'OCURRIÓ UN ERROR AL CONSULTAR LA CITA CONTROL.', 'warning', false, true);
    }
}

const onGenerarCitaProcedimiento = async (citaRelacionada) => {

    if (!citaRelacionada) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'LA INTERCONSULTA NO CUENTA CON UNA CITA RELACIONADA, POR FAVOR CONSULTE OTRA INTERCONSULTA DESEADA.',
            'warning', false, true
        );
    }

    if (!citaRelacionada.IdCita) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'SE HA VALIDADO QUE LA SELECCIÓN DE CITA NO FUE CORRECTA (FALTA EL CAMPO DE CITA), VERIFIQUE E INTENTE NUEVAMENTE.', 'warning', true, true);
        return;
    }

    IdEspecialidades.value = []
    tipoSeleccion.value = 'Procedimiento';

    const esquemaBase = {
        IdCita: citaRelacionada.IdCita ?? null,
    };
    const datosFinales = Object.assign(
        {},
        esquemaBase,
        typeof responseData === 'object' && responseData !== null ? responseData : {}
    );
    itemSeleccion.value = datosFinales;
    IdEspecialidad.value = null;
    modalCitaVisible.value = true;
};

async function registrarNuevoRiesgoQuirurgico() {
    if (!tipoDocumento.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE TIPO DE DOCUMENTO ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.',
            'warning', false, true
        );
    }

    if (tipoDocumento.value === 1 && nroDocumento.value.length !== 8) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL NÚMERO DE DOCUMENTO DEBE TENER 8 CARACTERES.',
            'warning', false, true
        );
    }

    if (!idPaciente.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'REALICE LA CONSULTA DE DATOS NUEVAMENTE, EL PACIENTE NO FUE UBICADO EN EL SISTEMA.',
            'warning', false, true
        );
    }

    modalCitaRiesgoQuirurgico.value.openDialog();
}

async function consultarReferenciaSinDocumento() {
    modalREFCON.value.openDialog();
}

async function registrarNuevaCitaProxima(itemInterconsulta) {
    if (!tipoDocumento.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE TIPO DE DOCUMENTO ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.',
            'warning', false, true
        );
    }

    if (tipoDocumento.value === 1 && nroDocumento.value.length !== 8) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL NÚMERO DE DOCUMENTO DEBE TENER 8 CARACTERES.',
            'warning', false, true
        );
    }

    if (!idPaciente.value) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'REALICE LA CONSULTA DE DATOS NUEVAMENTE, EL PACIENTE NO FUE UBICADO EN EL SISTEMA.',
            'warning', false, true
        );
    }

    modalCitaProxima.value.openDialog(itemInterconsulta);
}

async function consultarDemandaInsatisfecha() {
    modalDemandaInsatisfecha.value.openDialog();
}

const onCitaControlGenerada = (responseData) => {
    IdEspecialidades.value = []
    IdEspecialidad.value = responseData.IdEspecialidad
    const esquemaBase = {
        IdCitaControl: responseData.IdProximaCita ?? null,
    };
    const datosFinales = Object.assign(
        {},
        esquemaBase,
        typeof responseData === 'object' && responseData !== null ? responseData : {}
    );
    itemSeleccion.value = datosFinales;
    tipoSeleccion.value = 'CitaControl';
    modalCitaVisible.value = true;
};

const onCitaRiesgoQuirurgicoGenerada = (especialidades) => {
    IdEspecialidad.value = null;
    itemSeleccion.value = null;
    tipoSeleccion.value = 'InterconsultaRQ';
    IdEspecialidades.value = especialidades;
    modalCitaVisible.value = true;
};

const onCitaProximaGenerada = async (itemInterconsulta) => {
    if (!itemInterconsulta.success) {
        try {
            const formData = {
                IdInterconsulta: itemInterconsulta.IdInterconsulta
            };

            const { data } = await axios.post('/consulta-externa/citas/WebS_CitaProximaCE_UpdateEstado', formData);
            if (data.success) {
                IdEspecialidades.value = []
                tipoSeleccion.value = 'Interconsulta';

                const esquemaBase = {
                    IdInterconsulta: itemInterconsulta.IdInterconsulta ?? null,
                };
                console.log(itemInterconsulta);
                const datosFinales = Object.assign(
                    {},
                    esquemaBase,
                    typeof responseData === 'object' && responseData !== null ? responseData : {}
                );
                itemSeleccion.value = datosFinales;
                IdEspecialidad.value = itemInterconsulta.IdEspecialidad;
                modalCitaVisible.value = true;
            } else {
                showAlert("OPERACIÓN CANCELADA", 'SE HA VERIFICADO QUE LA INTERCONSULTA SELECCIONADA NO PUEDE SER PROCESADA, CONSULTE CON SOPORTE DE INFORMÁTICA.', "warning");
            }
        } catch (error) {
            showAlert("OPERACIÓN CANCELADA", error.message || 'Ocurrió un error', "warning");
        }
    } else {
        IdEspecialidades.value = []
        tipoSeleccion.value = 'CitaProxima';
        const esquemaBase = {
            IdCitaPorInterconsulta: itemInterconsulta.IdInterconsulta ?? null,
        };
        const datosFinales = Object.assign(
            {},
            esquemaBase,
            typeof responseData === 'object' && responseData !== null ? responseData : {}
        );
        itemSeleccion.value = datosFinales;
        IdEspecialidad.value = itemInterconsulta.IdEspecialidad;
        modalCitaVisible.value = true;
    }
};

const cerrarModalNombres = () => {
    modalBusquedaNombres.value = true
    nombres.value = false
}

const seleccionarPaciente = (paciente) => {
    tipoDocumento.value = Number(paciente.IdDocIdentidad);
    nroDocumento.value = paciente.NroDocumento
    modalBusquedaNombres.value = false
    nombres.value = false
}

</script>

<template>
    <ModalLoader v-if="isModalLoading" />

    <div class="row">
        <div class="col-xl-12 text-end d-flex justify-content-end gap-2 mb-4">
            <button class="btn btn-outline-warning btn-sm" @click="consultarDemandaInsatisfecha()">
                DEMANDA INSATISFECHA
            </button>

            <button class="btn btn-outline-primary btn-sm" @click="consultarReferenciaSinDocumento()">
                REFERENCIA SIN DOCUMENTO
            </button>

            <button class="btn btn-outline-primary btn-sm" v-if="visibleRegistrarNuevaCitaControl"
                @click="citarPaciente('', 'Programas')">
                PROGRAMAS
            </button>

            <button class="btn btn-outline-primary btn-sm"
                v-if="visibleRegistrarNuevaCitaControl && $can(`${moduloActual}.tab.gestionar.citas.registrar.cita.riesgo.quirurgico`)"
                @click="registrarNuevoRiesgoQuirurgico()">
                RIESGO QUIRÚRGICO
            </button>

            <button class="btn btn-outline-primary btn-sm"
                v-if="visibleRegistrarNuevaCitaControl && $can(`${moduloActual}.tab.gestionar.citas.registrar.cita.control`)"
                @click="registrarNuevaCitaControl()">
                REGISTRAR CITA CONTROL
            </button>

        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-12">
            <div class="p-3 border rounded" style="border: 1px solid #7367f0!important; height: 135px;">
                <div class="d-flex align-items-center mb-1"
                    v-if="$can(`${moduloActual}.tab.gestionar.citas.busqueda.por.servicios.api`)"
                    style="width: 100%; justify-content: space-between;">

                    <div class="d-flex align-items-center">
                        <label class="mb-0 me-1">NOMBRES</label>
                        <ToggleSwitch v-model="nombres" @change="onToggle('NOMBRES', $event)" />
                    </div>

                    <div class="d-flex align-items-center">
                        <label class="mb-0 me-1">AFILIACIÓN</label>
                        <ToggleSwitch v-model="afiliacion" @change="onToggle('AFILIACION', $event)" />
                    </div>
                </div>


                <div class="d-flex align-items-center justify-content-between" v-else>
                    <label class="mb-0">BUSCAR DE PACIENTE</label>
                </div>
                <BaseCombo v-model="tipoDocumento" :options="tiposDocumento" optionLabel="label" optionValue="value" />
                <div class="d-flex align-items-center mt-2">

                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="NÚMERO DE BÚSQUEDA" aria-label="Text input"
                            v-model="nroDocumento" ref="inputDocumentoRef" autocomplete="off"
                            @keydown.enter="ejecutarBuscarPacienteAdmisionCitas">
                        <button class="btn btn-primary waves-effect" type="button" id="btnCosultarPacienteCita"
                            name="btnCosultarPacienteCita" style=" height: 2rem !important;"
                            @click="ejecutarBuscarPacienteAdmisionCitas">
                            <span class="ti ti-search" style="font-size: 17px;"></span>
                        </button>
                        <button class="btn btn-danger" @click="clearSearchDocumento">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perfil paciente -->
        <div v-if="loadingDatosPaciente" class="col-xl-4 col-lg-6 col-md-12">
            <div class="p-3 border rounded" style="border: 1px solid #7367f0!important;">
                <div class="d-flex align-items-start">
                    <div class="me-3 position-relative">
                        <img :src="fotoPaciente" :key="fotoPaciente" alt="Foto paciente" class="img-fluid rounded"
                            style="width: 108px; height: 108px;" />
                        <div v-if="loadingFoto"
                            class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-light rounded">
                            <div class="spinner-border text-primary" role="status"
                                style="width: 1.5rem; height: 1.5rem;"></div>
                        </div>
                    </div>
                    <div style="font-size: 0.7rem; text-transform: uppercase;">
                        <label><b>INFORMACIÓN GENERAL:</b></label><br />
                        <strong class="text-success">{{ nombrePaciente }}</strong><br />
                        <strong>{{ dniPaciente }}</strong><br />
                        <strong>{{ hcPaciente }}</strong><br />
                        <strong>{{ edadPaciente }}</strong><br />
                        <strong>{{ sexoPaciente }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contacto -->
        <div v-if="loadingDatosPaciente" class="col-xl-3 col-lg-6 col-md-12">
            <div class="p-3 border rounded" style="border: 1px solid #7367f0!important;">
                <label><b>CORREO:</b></label>
                <InputText v-model="correoPaciente" placeholder="CORREO ELECTRÓNICO" class="w-full" />
                <label><b>CELULAR:</b></label>
                <div class="flex gap-2">
                    <InputText v-model="telefonoPaciente" placeholder="NÚMERO DE CELULAR" class="flex-1" />
                </div>
            </div>
        </div>

        <!-- Servicios web -->
        <div v-if="loadingDatosPaciente && $can(`${moduloActual}.tab.gestionar.citas.busqueda.por.servicios.api`)"
            class="col-xl-2 col-lg-6 col-md-12">
            <div class="p-3 border rounded " style="border: 1px solid #7367f0!important;">
                <div class="d-flex align-items-center justify-content-between mb-3 mt-2">
                    <label for="reniec" class="mb-0">RENIEC</label>
                    <ToggleSwitch v-model="reniec" @change="onToggle('RENIEC', $event)" />
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <label for="sis" class="mb-0">SIS</label>
                    <ToggleSwitch v-model="sis" @change="onToggle('SIS', $event)" />
                </div>
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label for="refcon" class="mb-0">REFCON</label>
                    <ToggleSwitch v-model="refcon" @change="onToggle('REFCON', $event)" />
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 col-md-12 col-lg-6">
            <ul class="nav nav-tabs d-flex align-items-center w-100" role="tablist" v-if="visibleReferencias">
                <!-- TAB 1 -->
                <li class="nav-item" role="presentation" v-if="referencias.length">
                    <button class="nav-link" :class="{ active: activeTabReferencias === 1 }"
                        @click="activeTabReferencias = 1" type="button">
                        REFERENCIAS
                        <span class="badge bg-danger rounded-circle ms-2">{{ referencias.length }}</span>
                    </button>
                </li>

                <li class="ms-auto w-40" v-if="referencias.length">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Buscar..."
                            v-model="searchQueryReferencias" />
                        <button class="btn btn-outline-danger" @click="clearSearchReferencias">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </li>
            </ul>

            <div v-if="visibleReferencias">
                <div v-show="referencias.length > 0 && activeTabReferencias === 1"
                    class="tab-pane show active table-responsive">
                    <div style="max-height: 12.5rem; overflow-y: auto;">
                        <ul class="list-group mt-2">
                            <template v-for="(ref, index) in filteredreferencias" :key="ref.idReferencia">
                                <li class="list-group-item"
                                    style="font-size: 0.7rem !important; border-color: #28c76f !important;">

                                    <!-- Encabezado -->
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong class="text-dark">
                                            <span v-if="ref.Tipo == 'Refcon'">REFERENCIA: {{ ref.numeroReferencia
                                                }}</span>
                                            <span v-else="ref.Tipo">REFERENCIA GALENOS: {{ ref.numeroReferencia
                                                }}</span>
                                            <span v-if="ref.fechaFormateada"> - {{ ref.fechaFormateada }}</span>
                                        </strong>
                                        <span :class="obtenerClaseEstado(ref.estado)">{{ ref.estado }}</span>
                                    </div>

                                    <!-- Contenido + Botones -->
                                    <div class="d-flex justify-content-between align-items-start">
                                        <!-- Información principal -->
                                        <div>
                                            <span v-if="ref.upsDestino">
                                                <strong>
                                                    UPS: <b class="text-primary">{{ ref.upsDestino }}</b>
                                                </strong> {{ ref.descUpsDestino }}<br>
                                            </span>
                                            <b>ESPECIALIDAD:</b> {{ ref.especialidad }}
                                        </div>

                                        <!-- Botones -->
                                        <div class="text-end ms-3" style="width: 10rem;">
                                            <!-- Botón Visualizar -->
                                            <button type="button" class="btn btn-sm btn-icon btn-primary me-1"
                                                title="Visualizar" @click="onVisualizarReferencia(ref)"
                                                v-if="ref.upsDestino && $can(`${moduloActual}.tab.gestionar.citas.visualizar.referencia`)">
                                                <i class="ti ti-pdf text-white"></i>
                                            </button>

                                            <!-- Botón Citar -->
                                            <button
                                                v-if="((ref.Tipo == 'Refcon' && (ref.estado === 'PACIENTE CITADO' || ref.estado === 'PACIENTE RECIBIDO' || ref.estado === 'ACEPTADO')
                                                    && $can(`${moduloActual}.tab.gestionar.citas.registrar.cita`) && !validacionCuentasPendientes))"
                                                type="button" class="btn btn-sm btn-icon btn-success"
                                                title="Generar Cita" @click="citarPaciente(ref, 'Referencia')">
                                                <i class="ti ti-calendar text-white"></i>
                                            </button>

                                            <!-- Botón Citar -->
                                            <button
                                                v-if="ref.Tipo == 'Galenos' && $can(`${moduloActual}.tab.gestionar.citas.registrar.cita`) && !validacionCuentasPendientes"
                                                type="button" class="btn btn-sm btn-icon btn-success"
                                                title="Generar Cita" @click="citarPaciente(ref, 'ReferenciaGalen')">
                                                <i class="ti ti-calendar text-white"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-6" v-if="interconsultasPendientes.length">
                <ul class="nav nav-tabs d-flex align-items-center w-100" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTabInterconsultas === 1 }"
                            @click="activeTabInterconsultas = 1" type="button">
                            INTERCONSULTAS <span class="badge bg-danger rounded-circle ms-2">{{
                                interconsultasPendientes.length
                            }}</span>
                        </button>
                    </li>

                    <li class="ms-auto w-40" v-if="interconsultasPendientes.length">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" placeholder="Buscar..."
                                v-model="searchQueryInterconsultas" />
                            <button class="btn btn-outline-danger" @click="clearSearchInterconsultas">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </li>
                </ul>

                <div>
                    <div v-show="activeTabInterconsultas === 1" class="tab-pane show active table-responsive">
                        <div style="max-height: 13rem; overflow-y: auto;">
                            <ul class="list-group mt-2">
                                <template v-for="(inter, index) in filteredInterconsultas" :key="inter.IdInterconsulta">
                                    <li class="list-group-item"
                                        style="font-size: 0.7rem !important; border-color: #28c76f !important;">

                                        <!-- Encabezado -->
                                        <div class="d-flex justify-content-between mb-1">
                                            <b class="text-dark">
                                                SOLICITUD: {{ inter.FechaSolicitud }} - DESTINO: {{
                                                    inter.EspecialidadDestino }}
                                            </b>
                                            <span v-if="inter.IdEstado != 5"
                                                :class="obtenerClaseEstado(inter.Estado)">{{ inter.Estado }}</span>
                                            <span v-if="inter.IdEstado == 5"
                                                :class="obtenerClaseEstado('CITADO')">Citado</span>
                                        </div>

                                        <!-- Contenido + Botón alineados horizontalmente -->
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                SERVICIO ORIGEN: {{ inter.ServicioOrigen }} <br>
                                                MÉDICO: {{ inter.MedicoOrigen }}
                                                <template v-if="inter.IdEstado == 4">
                                                    <br>
                                                    <b class="text-danger">OBSERVACIÓN: {{ inter.ObservacionAuditor
                                                        }}</b>
                                                </template>
                                            </div>
                                            <div class="text-end ms-3"
                                                v-if="$can(`${moduloActual}.tab.gestionar.citas.registrar.cita`) && (inter.IdEstado == 3 || inter.IdEstado == 5) && !validacionCuentasPendientes">
                                                <button v-if="inter.IdEstado != 5" type="button"
                                                    class="btn btn-sm btn-icon btn-success" title="Generar Cita"
                                                    @click="citarPaciente(inter, 'Interconsulta')">
                                                    <i class="ti ti-calendar text-white"></i>
                                                </button>
                                                <button v-if="inter.IdEstado == 5" type="button"
                                                    class="btn btn-sm btn-icon btn-primary" title="Generar Cita"
                                                    @click="registrarNuevaCitaProxima(inter)">
                                                    <i class="ti ti-reload text-white"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6" v-if="citaControlPendientes.length">
                <ul class="nav nav-tabs d-flex align-items-center w-100" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTabCitaControl === 1 }"
                            @click="activeTabCitaControl = 1" type="button">
                            CITA CONTROL
                            <span class="badge bg-danger rounded-circle ms-2">{{ citaControlPendientes.length }}</span>
                        </button>
                    </li>

                    <li class="ms-auto w-40" v-if="citaControlPendientes.length">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" placeholder="Buscar..."
                                v-model="searchQueryCitaControl" />
                            <button class="btn btn-outline-danger" @click="clearSearchCitaControl">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </li>
                </ul>

                <div v-show="activeTabCitaControl === 1" class="tab-pane show active table-responsive">
                    <div style="max-height: 15.1rem; overflow-y: auto;">
                        <ul class="list-group mt-2">
                            <template v-for="(inter, index) in filteredCitaControl" :key="inter.IdCitaControl">
                                <li class="list-group-item"
                                    style="font-size: 0.7rem !important; border-color: #28c76f !important;">

                                    <!-- Encabezado -->
                                    <div class="d-flex justify-content-between mb-1">
                                        <b class="text-dark"> DESTINO: {{ inter.EspecialidadDestino }} </b>
                                        <span :class="obtenerClaseEstado('Aprobado')">Aprobado</span>
                                    </div>

                                    <!-- Contenido + Botón en la misma fila -->
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            FECHA: {{ inter.FechaPropuesta }} <br>
                                            ORIGEN: {{ inter.ServicioOrigen }} <br>
                                            MÉDICO: {{ inter.MedicoOrigen }}
                                        </div>

                                        <div class="text-end ms-3"
                                            v-if="$can(`${moduloActual}.tab.gestionar.citas.registrar.cita`) && !validacionCuentasPendientes">
                                            <button type="button" class="btn btn-sm btn-icon btn-success"
                                                title="Generar Cita" @click="citarPaciente(inter, 'CitaControl')">
                                                <i class="ti ti-calendar text-white"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-12 col-md-12 col-lg-6">
            <ul class="nav nav-tabs d-flex align-items-center w-100" role="tablist" v-if="citaAdicionales.length">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" type="button">
                        ADICIONAL <span class="badge bg-danger rounded-circle ms-2">{{ citaAdicionales.length }}</span>
                    </button>
                </li>
            </ul>
            <div v-if="citaAdicionales.length">
                <div class="tab-pane show active table-responsive">
                    <div style="max-height: 35rem; overflow-y: auto;">
                        <ul class="list-group mt-2">
                            <template v-for="(cita, index) in citaAdicionales" :key="cita.IdSolicitudCitaAdicionalCE">
                                <li class="list-group-item"
                                    style="font-size: 0.7rem !important; border-color: #28c76f !important;">
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
                </div>
            </div>

            <ul class="nav nav-tabs d-flex align-items-center w-100" role="tablist" v-if="citasPendientes.length">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{ active: activeTabCitas === 1 }" @click="activeTabCitas = 1"
                        type="button"> CITAS <span class="badge bg-danger rounded-circle ms-2">{{ citasPendientes.length
                        }}</span>
                    </button>
                </li>

                <li class="ms-auto w-40" v-if="citasPendientes.length">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Buscar..." v-model="searchQueryCitas" />
                        <button class="btn btn-outline-danger" @click="clearSearchCitas">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </li>
            </ul>
            <div>
                <div v-show="activeTabCitas === 1" class="tab-pane show active table-responsive">
                    <div style="max-height: 35rem; overflow-y: auto;">
                        <ul class="list-group mt-2">
                            <template v-for="(cita, index) in filteredCitas" :key="cita.IdCita">
                                <li class="list-group-item"
                                    style="font-size: 0.7rem !important; border-color: #28c76f !important;">

                                    <!-- Encabezado -->
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong class="text-dark">
                                            CUENTA: {{ cita.IdCuentaAtencion }} - FECHA: {{ cita.Fecha }}
                                            <span v-if="cita.Validar == 1"> - ({{ cita.HoraInicio }} - {{ cita.HoraFin
                                                }})</span>
                                        </strong>
                                    </div>

                                    <!-- Contenido + Acciones alineadas horizontalmente -->
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            SERVICIO: {{ cita.Servicio }} <br>
                                            MÉDICO: {{ cita.Medico }}
                                        </div>
                                        <div class="text-end ms-3">
                                            <CitaBotones :cita="cita" :onVerCita="verCita" :validarProcedimiento="true"
                                                :onGenerarCitaProcedimiento="onGenerarCitaProcedimiento"
                                                :validacionCuentasPendientes="validacionCuentasPendientes"
                                                :onImprimirCita="imprimirCita" :onNotificarCita="notificarCita"
                                                :onVerFUA="verFUA" :onAnularCita="anularCita"
                                                :moduloActual="moduloActual"
                                                :IdFuenteFinanciamiento="IdFuenteFinanciamiento" />
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para generar la cita -->
    <ModalBase :is-visible="modalCitaVisible" @close="cerrarModal" header="Generar Cita Médica" size="modal-full">
        <div class="card" v-if="IdEspecialidades.length > 0">
            <Tabs v-model:value="activeTab">
                <TabList>
                    <Tab v-for="(esp, index) in IdEspecialidades" :key="esp.IdEspecialidad" :value="index.toString()">
                        {{ esp.Especialidad }}
                    </Tab>
                </TabList>
                <TabPanels>
                    <TabPanel v-for="(esp, index) in IdEspecialidades" :key="esp.IdEspecialidad + '-panel'"
                        :value="index.toString()">
                        <CitarFormulario :paciente="pacienteSeleccionado" :tipoSeleccion="tipoSeleccion"
                            :itemSeleccion="itemSeleccion" :IdFuenteFinanciamiento="IdFuenteFinanciamiento"
                            :IdTipoFinanciamiento="IdTipoFinanciamiento" :citasPaciente="citasPendientes"
                            :citaAdicionales="citaAdicionales" :IdEspecialidad="esp.IdEspecialidad" :sis="sis"
                            :IdEspecialidades="IdEspecialidades" :moduloActual="moduloActual"
                            @cerrarModal="cerrarModalCita" />
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>

        <div v-else>
            <CitarFormulario :paciente="pacienteSeleccionado" :tipoSeleccion="tipoSeleccion"
                :itemSeleccion="itemSeleccion" :IdFuenteFinanciamiento="IdFuenteFinanciamiento"
                :IdTipoFinanciamiento="IdTipoFinanciamiento" :citasPaciente="citasPendientes"
                :citaAdicionales="citaAdicionales" :IdEspecialidad="IdEspecialidad" :moduloActual="moduloActual"
                :IdEspecialidades="IdEspecialidades" :sis="sis" @cerrarModal="cerrarModalCita" />
        </div>
    </ModalBase>

    <!-- <CitaProcedimiento ref="modalCitaProcedimiento" @success="onCitaProcedimientoGenerada" /> -->
    <DemandaInsatisfecha ref="modalDemandaInsatisfecha" />

    <CitaControl ref="modalCitaControl" :IdPaciente="idPaciente" :itemSeleccion="itemSeleccion"
        @success="onCitaControlGenerada" />

    <CitaRiesgoQuirurgico ref="modalCitaRiesgoQuirurgico" :IdPaciente="idPaciente" :itemSeleccion="itemSeleccion"
        @success="onCitaRiesgoQuirurgicoGenerada" />

    <CitaProxima ref="modalCitaProxima" @success="onCitaProximaGenerada" />

    <ModalPDF ref="modalPDF" />
    <MotivoAnulacionDialog ref="motivoAnulacionRef" />
    <PacienteFiliacionSIS ref="modalSIS" @enviarDatos="procesarDatosSIS" @cerrado="afiliacion = false" />

    <PacienteNombres :visible="modalBusquedaNombres" @cerrar="cerrarModalNombres" @seleccionar="seleccionarPaciente" />
    <PacienteRefconSinDocumento ref="modalREFCON" @enviarDatos="procesarDatosREFCONSinDocumento"
        @cerrado="afiliacion = false" />
</template>
