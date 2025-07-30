<script	setup>
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import {useForm} from '@inertiajs/vue3'
import { defineEmits } from 'vue';
import BaseModal from '@/components/BaseModal.vue'

// FullCalendar
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';


const props = defineProps({
    recordId: String,
    puntoCargaId: String,
    viewRecord: Boolean,
    resource: String
})

let isDialogOpen = ref(false);
let isSaving = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let toastRef = ref();

let isViewMode = computed(() => props.viewRecord)
let errors = ref({});
const form = useForm({
    Descripcion: '',
    Estado: 1
})

let programacion = ref([])
let fechaSeleccionadaCalendario = ref(null);

const handleOpen = async () => {
    errors.value = {};
    form.reset()
    form.clearErrors()
    cargarProgramacion()

    /*if (props.recordId) {
        try {
            isModalLoading.value = true;                        
            showModal.value = false;
            isSaving.value = true;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form, data.data)

        } catch (error) {
            if (error.status === 403) {
                toastRef.value.showError(error.response.data.message, true);
                closeDialog();
            }
        } finally {
            isSaving.value = false;
            showModal.value = true;
            isModalLoading.value = false;
        }
    } else {
        showModal.value = true;
    }*/
}

const openDialog = () => {
    isDialogOpen.value = true;
}

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

        const formatoFecha = info.dateStr;

        // Convertir a formato día/mes/año
        const [anio, mes, dia] = formatoFecha.split('-');
        fechaSeleccionadaCalendario.value = formatoFecha

        const mensajeConfirmacion = `¿ESTÁ SEGURO QUE DESEA PROGRAMAR LA CITA PARA EL  ${formatoFecha}?`;
        const confirmado = await showAlertConfirmacion(
            'VALIDACION DE DATOS REALIZADOS', mensajeConfirmacion,
            'warning'
        );

        if(confirmado){
            const accion = props.recordId ? 'ACTUALIZADO' : 'REALIZADO';
                const mensaje = `EL REGISTRO FUE ${accion} DE FORMA EXITOSA`;
                showAlert('PROCESO REALIZADO EXITOSAMENTE', mensaje, 'success');

                isDialogOpen.value = false;
        }
    }
});

const cargarProgramacion =  async () => {
    try {
        const { data } = await axios.post('/imagenologia/programacion-imagenologia/WebS_ProgramacionImagenologia_Lista_BuscarFiltro', {
            IdPuntoCarga: props.puntoCargaId
        })
        console.log(data);
        console.log(props.puntoCargaId);
        programacion.value = data     
        actualizarEventosCalendario(); 

    } catch (error) {
        console.error('Error al cargar programación:', error)
    }
}

const actualizarEventosCalendario = () => {

        const eventosFiltrados = programacion.value.filter(item => {
            const coincidePuntoCarga = !props.puntoCargaId || item.IdPuntoCarga == props.puntoCargaId;
        return coincidePuntoCarga;
    });

        ProgramacionCalendario.value.events = eventosFiltrados.map(item => {

        return {
            title: 'Turno',
            start: item.Fecha,
            extendedProps: { data: item }
        };
    });
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}


defineExpose({openDialog})
</script>
        

<template>
    <BaseModal
        :isVisible="isDialogOpen"
        :recordId="props.recordId"
        :viewRecord="isViewMode"
        :isSaving="isSaving"
        @open="handleOpen"
        @close="closeDialog"
        size="modal-xl"
    >
    <div class="row">
        <div class="col-9">
            <FullCalendar :options="ProgramacionCalendario" />
        </div>
    </div>
        
    </BaseModal>
</template>
