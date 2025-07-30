<script setup>
import { ref, reactive } from 'vue'
import axios from 'axios'
import Dialog from 'primevue/dialog'
import { useAppStore } from '@/stores/useAppStore'
import BaseCombo from '@/components/BaseCombo.vue'
import ModalLoader from '@/components/ModalLoader.vue'


let appStore = useAppStore();
const visible = ref(false)
let tiposDocumento = [
    { label: 'DNI', value: 1 },
    { label: 'CARNET DE EXTRANJERÍA', value: 2 },
    { label: 'HISTORIA CLÍNICA', value: -1 },
    { label: 'DOCUMENTO DE IDENTIDAD EXTRANJERO', value: 4 },
    { label: 'C.U.I', value: 7 },
    { label: 'CÓDIGO RECIÉN NACIDO', value: 5 },
    { label: 'PASAPORTE', value: 3 },
];

let isModalLoading = ref(false)
let datosCargadosPaciente = ref(false)

const form = reactive({
    TipoDocumento: 1,
    NumeroDocumento: '',
    NombrePaciente: '',
    Celular: '',
    IdEspecialidad: null,
    IdMotivo: null,
    IdPaciente: null,
    EnvioSMS: 0
})

const openDialog = () => {
    form.TipoDocumento = 1
    form.NumeroDocumento = ''
    form.NombrePaciente = ''
    form.Celular = ''
    form.IdEspecialidad = null
    form.IdMotivo = 1
    form.IdPaciente = null
    form.EnvioSMS = 0
    visible.value = true
    
    datosCargadosPaciente.value = false
}

const consultarPaciente = async () => {
    isModalLoading.value = true
    datosCargadosPaciente.value = false
    if (!form.TipoDocumento) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE TIPO DE DOCUMENTO ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.',
            'warning', false, true
        );
    }
    form.NumeroDocumento = form.NumeroDocumento.replace(/\s+/g, '');

    if (form.TipoDocumento === 1 && form.NumeroDocumento.length !== 8) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL NÚMERO DE DOCUMENTO DEBE TENER 8 CARACTERES.',
            'warning', false, true
        );
    }

    try {
        const response = await axios.post('/personas/persona/PacienteBuscarTipoAndDocumento', {
            nombremodulo: 'ADMISIÓN Y CITAS',
            tipodocumento: form.TipoDocumento,
            numerodocumento: form.NumeroDocumento.replace(/\s+/g, ''),
            tipopersona: 0,
        })

        if (response.data.success) {
            isModalLoading.value = false
            const paciente = response.data.data;
            const telefonoLimpio = (paciente.Celular || '').replace(/\D/g, '');
            form.Celular = telefonoLimpio.length === 9 ? telefonoLimpio : '';
            form.NombrePaciente = `${paciente.ApellidoPaterno || ''} ${paciente.ApellidoMaterno || ''} ${paciente.PrimerNombre || ''} ${paciente.SegundoNombre || ''} ${paciente.TercerNombre || ''}`.trim();
            form.IdPaciente = paciente.IdPaciente
            datosCargadosPaciente.value = true
        } else {
            await obtenerDatosSis();
        }

    } catch (error) {
        console.error(error);
        showAlert('ERROR', 'Ocurrió un error al buscar el paciente.', 'error', false, true);
    }
};

async function obtenerDatosSis() {
    try {
        form.EstablecimientoSalud = null
        showAlert("VERIFICANDO AFILIACIÓN SIS ...", "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD", "warning", true);
        const response = await axios.post('/core/servicios/obtenerDatosSisCompletos', {
            numerodocumento: form.NumeroDocumento.replace(/\s+/g, ''),
            tipodocumento: form.TipoDocumento,
            consultareniec: false,
            tipopersona: 0,
            modulo_origen: 'admicion_ce',
        });

        if (response?.data.respuesta == 1) {
            if (response?.data.data) {
                isModalLoading.value = false

                const paciente = response.data.data;
                const telefonoLimpio = (paciente.Celular || '').replace(/\D/g, '');
                form.Celular = telefonoLimpio.length === 9 ? telefonoLimpio : '';
                form.NombrePaciente = `${paciente.ApellidoPaterno || ''} ${paciente.ApellidoMaterno || ''} ${paciente.PrimerNombre || ''} ${paciente.SegundoNombre || ''} ${paciente.TercerNombre || ''}`.trim();
                form.IdPaciente = paciente.IdPaciente

                datosCargadosPaciente.value = true
            }

            showAlert(
                "CONSULTA REALIZADA",
                "LA CONSULTA FUE REALIZADA DE FORMA EXITOSA",
                "success"
            );

        } else {
            showAlert("CONSULTA REALIZADA", "EL PACIENTE NI PUDO SER UBICADO NI EN RENIEC NI EN EL SIS, POR FAVOR REGISTRAR AL PACIENTE MANUALMENTE DESDE GALENOS", 'warning');
        }
    } catch (error) {
        console.error('Error al obtener datos del SIS:', error);
        return null;
    }
}

const generarDemandaInsatisfecha = async () => {
    if (!form.IdPaciente) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'RECUERDE QUE TIENE QUE REALIZAR LA CONSULTA DEL PACIENTE PARA PODER UBICARLO DENTRO DE LA BASE DE DATOS DE PACIENTES.',
            'warning', false, true
        );
    }

    if (!form.TipoDocumento) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL CAMPO DE TIPO DE DOCUMENTO ES NECESARIO PARA PODER REALIZAR LA BÚSQUEDA DEL PACIENTE.',
            'warning', false, true
        );
    }

    if (form.TipoDocumento === 1 && form.NumeroDocumento.length !== 8) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'EL NÚMERO DE DOCUMENTO DEBE TENER 8 CARACTERES.',
            'warning', false, true
        );
    }

    if (!form.NombrePaciente) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'ES NECESARIO QUE PUEDA REALIZAR LA BÚSQUEDA DEL PACIENTE, RECUERDE INGRESAR EL TIPO Y NÚMERO DE DOCUMENTO.',
            'warning', false, true
        );
    }

    if (!form.Celular) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'ES NECESARIO QUE PUEDA ASIGNAR UN NÚMERO DE CELULAR VÁLIDO PARA EL CONTACTO NECESARIO CON EL PACIENTE.',
            'warning', false, true
        );
    }

    // Elimina espacios y valida el formato
    const celularLimpio = form.Celular.replace(/\s+/g, '');
    const esValido = /^[9]\d{8}$/.test(celularLimpio);
    if (!esValido) {
        return showAlert(
            'VALIDACIÓN DE CELULAR',
            'EL NÚMERO DE CELULAR DEBE CONTENER 9 DÍGITOS, INICIAR CON EL NÚMERO 9 Y NO DEBE CONTENER LETRAS NI ESPACIOS.',
            'warning', false, true
        );
    }

    if (!form.IdEspecialidad) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'SELECCIONE UNA ESPACIALIDAD VÁLIDA PARA EL REGISTRO CORRESPONDIENTE.', 'warning', false, true);
        return;
    }

    if (!form.IdMotivo) {
        showAlert('VALIDACIÓN DE CAMPO REALIZADO', 'SELECCIONE UN MOTIVO DE LA DEMANDA INSATISFECHA VÁLIDA PARA EL REGISTRO CORRESPONDIENTE.', 'warning', false, true);
        return;
    }

    const mensaje = `¿ESTÁ SEGURO QUE DESEA REALIZAR EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA?`;
    const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
    if (!confirmado) return;

    isModalLoading.value = true;

    const formData = {
        TipoDocumento: form.TipoDocumento,
        NumeroDocumento: form.NumeroDocumento.replace(/\s+/g, ''),
        IdEspecialidad: form.IdEspecialidad,
        IdMotivo: form.IdMotivo,
        Celular: form.Celular,
        IdPaciente: form.IdPaciente,
        EnvioSMS: form.EnvioSMS,
        OrigenRegistro: 'ADMISIÓN DE CONSULTA EXTERNA'
    };

    try {
        const { data } = await axios.post('/consulta-externa/citas/InsertarCitaDemandaInsatisfecha', formData);
        isModalLoading.value = false;

        if (data.success) {
            visible.value = false;
            showAlert("VALIDACIÓN REALIZADA", data.message, "success");
        } else {
            showAlert("OPERACIÓN CANCELADA", data.message, "warning", false, true);
        }
    } catch (error) {
        isModalLoading.value = false;
        showAlert("OPERACIÓN CANCELADA", error.response?.data?.message || error.message || 'Ocurrió un error', "error");
    }
}

const onClose = () => {
    visible.value = false
}

defineExpose({ openDialog })
</script>
<template>
    <Dialog v-model:visible="visible" modal header="DEMANDA INSATISFECHA" :style="{ width: '500px' }" @hide="onClose">
        <ModalLoader v-if="isModalLoading" />
        <div class="row g-3">
            <div class="col-md-6">
                <label><b>TIPO DOCUMENTO:</b></label>
                <BaseCombo v-model="form.TipoDocumento" :options="tiposDocumento" optionLabel="label"
                    optionValue="value" />
            </div>

            <div class="col-md-6">
                <label><b>N° DOCUMENTO:</b></label>
                <div class="input-group">
                    <input type="text" class="form-control" v-model="form.NumeroDocumento" autocomplete="off"
                        @keyup.enter="consultarPaciente" />
                    <button class="btn btn-primary" type="button" @click="consultarPaciente">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-12" v-if="datosCargadosPaciente">
                <label><b>NOMBRE DEL PACIENTE:</b></label>
                <input type="text" class="form-control" v-model="form.NombrePaciente" readonly />
            </div>

            <div class="col-md-12" v-if="datosCargadosPaciente">
                <label><b>CELULAR:</b></label>
                <input type="text" class="form-control" v-model="form.Celular" />
            </div>

            <div class="col-md-12 mt-2" v-if="datosCargadosPaciente">
                <BaseCombo v-model="form.IdEspecialidad" :options="appStore.citaAtencionInterconsultaEspecialidades"
                    label="Especialidad" option-label="label" option-value="id" :filter="true"
                    placeholder="Seleccione una Especialidad" />
            </div>

            <div class="col-md-12" v-if="datosCargadosPaciente">
                <BaseCombo v-model="form.IdMotivo" :options="appStore.citaDemandaInsatisfecha"
                    label="Motivo Demanda Insatisfecha" option-label="label" option-value="id" :filter="true"
                    placeholder="Seleccione un Motivo" />
            </div>
        </div>

        <div class="mt-4 text-end" v-if="datosCargadosPaciente">
            <button class="btn btn-primary" @click="generarDemandaInsatisfecha">
                <i class="pi pi-save"></i> REGISTRAR DEMANDA INSATISFECHA
            </button>
        </div>
    </Dialog>
</template>
