<script	setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import ModalFormulario from './FormularioProgramacionImagenologia.vue'
import BaseCombo from '@/components/BaseCombo.vue';

//Importamos FullCalendar
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

// Props
const props = defineProps({
    citasPaciente: {
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
    }
})

const form = ref({
    paciente: props.paciente,
    tipoSeleccion: props.tipoSeleccion,
    itemSeleccion: props.itemSeleccion,
    puntoCarga_id: null,
    //servicio_id: null,
    medico_id: null
})

let fechaSeleccionadaCalendario = ref(null);
let refDialogForm = ref()
let resource = ref('imagenologia/programacion-imagenologia');

//Control de estado del modal
let recordId = ref(null);
let puntoCargaId = ref(null);
let viewRecord = ref(false);

let puntoCarga = ref([])
//let serviciosFiltrados = ref([]);
let programacion = ref([])
let puntoCarga_id = ref(null)

//Configuracion calendario
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
            html: ` <div style="text-align:center; font-size: 11px" idProgramacion=${item.IdProgramacion}>
                        CUPOS PROGRAMADOS<br>
                        <small><b>${item.Cupos}</b></small>
                    </div>
                `
        };
    },
    dateClick: async (info) => {

        if(!form.value.puntoCarga_id){
            showAlert('ADVERTENCIA', 'Debe seleccionar un punto de carga', 'warning');
            return;
        }

        const formatoFecha = info.dateStr;

        // Convertir a formato día/mes/año
        const [anio, mes, dia] = formatoFecha.split('-');
        fechaSeleccionadaCalendario.value = formatoFecha

        const el = info.dayEl.querySelector('[idProgramacion]');
        if(el){
            clickView(el.getAttribute('idProgramacion'), form.value.puntoCarga_id);
        }else{  
            clickCreate(form.value.puntoCarga_id);
        }

        //alert(info.dateStr);
    }
});

const clickCreate = (idPc) => {
    //recordId.value = id
    puntoCargaId.value = idPc
    //viewRecord.value = true
    //modalVisible.value = true
    refDialogForm.value.openDialog()
}

const clickView = (id, idPc) => {
    recordId.value = id
    puntoCargaId.value = idPc
    //viewRecord.value = true
    //modalVisible.value = true
    refDialogForm.value.openDialog()
}

const cargarPuntosCarga = async () => {
    try {
        const { data } = await axios.post('/imagenologia/programacion-imagenologia/FactPuntosCargaFiltrar', {
            LcFiltro: "IdPuntoCarga in (2,3,11,20,21,22,23,24,25,27,28,39)"
        })
        puntoCarga.value = data      

    } catch (error) {
        console.error('Error al cargar puntos de carga:', error)
    }
}

const actualizarEventosCalendario = () => {

        const eventosFiltrados = programacion.value.filter(item => {
            const coincidePuntoCarga = !puntoCarga_id.value || item.IdPuntoCarga == puntoCarga_id.value;
        return coincidePuntoCarga;
    });

        ProgramacionCalendario.value.events = eventosFiltrados.map(item => {
        //let color = '#0e9a4c';                       // Verde por defecto
        //const [ocupados, total] = item.Cupos.split('/').map(Number);

       /* if (item.IdTipoConsultorio == 2) {
            color = ocupados >= total ? '#cb272c' : '#0e9a4c'; // Rojo lleno · Naranja con cupos
        } else {
            if (ocupados >= total) color = '#cb272c';          // Rojo lleno
        }*/

        return {
            title: 'Turno',
            start: item.Fecha,
            extendedProps: { data: item }
        };
    });
}

const cargarProgramacion =  async () => {
    try {
        const { data } = await axios.post('/imagenologia/programacion-imagenologia/WebS_ProgramacionImagenologia_Lista_BuscarFiltro', {
            IdPuntoCarga: form.value.puntoCarga_id
            //LcFiltro: "IdPuntoCarga in (2,3,11,20,21,22,23,24,25,27,28,39)"
        })
        programacion.value = data     
        actualizarEventosCalendario(); 

    } catch (error) {
        console.error('Error al cargar programación:', error)
    }
}

const OnChangePuntoCarga = (IdPuntoCarga) => {
    form.value.puntoCarga_id = IdPuntoCarga
    cargarProgramacion();
}

//Inicial
onMounted(()=> {
    cargarPuntosCarga(null)
    //cargarProgramacion()
    //actualizarEventosCalendario()
})

</script>

<template>
     <div class="card">
        <div class="card-body">
            <div class="row text-uppercase">
                <div class="col-12 col-md-12 col-lg-3">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="mt-2">
                        <BaseCombo :modelValue="form.puntoCarga_id" :options="puntoCarga" optionLabel="Descripcion"
                            placeholder="SELECCIONE UN PUNTO DE CARGA" optionValue="IdPuntoCarga" @update:modelValue="OnChangePuntoCarga" :filter="true" />
                    </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-8">
                    <div class="mb-3">
                        <FullCalendar :options="ProgramacionCalendario" />
                    </div>
                </div>
            </div>
        </div>
     </div>
    
        <ModalFormulario ref="refDialogForm" :record-id="recordId" :punto-carga-id="puntoCargaId" :fecha="fechaSeleccionadaCalendario" :resource="resource" :view-record="viewRecord" />
</template>
