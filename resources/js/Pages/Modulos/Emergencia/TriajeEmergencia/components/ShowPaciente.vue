<script setup>

import {ref, watch} from "vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import WInput from "@/components/WInput/WInput.vue";
import {useAppStore} from '@/stores/useAppStore';
import axios from "axios";
import ModalFormulario from "../../AdmisionEmergencia/AdmisionEmergenciaNN.vue";

const props = defineProps({
    paciente: Object,
    accion: String,
    viewRecord: Boolean,
})
const emit = defineEmits(['success', 'nueva-busqueda']);

let appStore = useAppStore();
let loading = ref(false);
let loadingFoto = ref(false);
let pacienteSeleccionado = ref();
let tiposDocumento = ref([]);
let inputDocumentoRef = ref(null);
let tipoDocumento = ref(1);
let nroDocumento = ref();
let form = ref({});
let busquedaInterna = ref(false); // Nueva bandera
let isUpdate = ref(false);

const limpiarPaciente = () => {
    loadingFoto.value = false;
    loading.value = false;
    pacienteSeleccionado.value = null;
    form.value = {
        id: null,
        nombre: '',
        dni: '',
        hc: '',
        edad: '',
        sexo: '',
        IdTipoSexo: null,
        correo: '',
        telefono: '',
        foto: null,
        years: null,
        months: null,
        days: null,
        IdDocIdentidad: null,
        NroDocumento: null,
        ApellidoPaterno: null,
        ApellidoMaterno: null,
        PrimerNombre: null,
        SegundoNombre: null,
        TercerNombre: null,
        FechaNacimiento: null,
        IdFuenteFinanciamiento: null
    }
}

function actualizarDatosPaciente() {
    // initForm()
    try {
        let datos = props.paciente
        loadingFoto.value = true
        loading.value = false
        form.value.foto = null
        const defaultFoto = datos.IdTipoSexo == 1
            ? '../../../assets/img/sexo1.gif'
            : '../../../assets/img/sexo2.gif'
        form.value.foto = defaultFoto ?? null
        form.value.IdTipoSexo = datos.IdTipoSexo

        const edadCompleta = calcularEdadCompleta(datos.FechaNacimiento)

        form.value.nombre = `${datos.ApellidoPaterno || ''} ${datos.ApellidoMaterno || ''} ${datos.PrimerNombre || ''} ${datos.SegundoNombre || ''} ${datos.TercerNombre || ''}`
        form.value.dni = `DNI: ${datos.NroDocumento}`
        form.value.hc = `HC: ${datos.NroHistoriaClinica ?? 'SIN DATOS'}`
        form.value.edad = `EDAD: ${edadCompleta.years} A, ${edadCompleta.months} M, ${edadCompleta.days} D`
        form.value.sexo = `SEXO: ${datos.IdTipoSexo === '1' ? 'MASCULINO' : 'FEMENINO'}`
        form.value.years = edadCompleta.years
        form.value.months = edadCompleta.months
        form.value.days = edadCompleta.days
        form.value.correo = datos.Email ?? ''
        form.value.telefono = datos.Telefono ?? ''
        form.value.id = datos.IdPaciente ?? null

        form.value.IdDocIdentidad = datos.IdDocIdentidad
        form.value.NroDocumento = datos.NroDocumento
        form.value.ApellidoPaterno = datos.ApellidoPaterno
        form.value.ApellidoMaterno = datos.ApellidoMaterno
        form.value.PrimerNombre = datos.PrimerNombre
        form.value.SegundoNombre = datos.SegundoNombre ?? null
        form.value.TercerNombre = datos.TercerNombre ?? null
        form.value.FechaNacimiento = datos.FechaNacimiento ?? null

    } catch (e) {
        console.error('Error cargando imagen:', e)
    } finally {
        loadingFoto.value = false
        loading.value = true
    }
}

function calcularEdadCompleta(fechaNacimientoStr) {
    const fechaNacimiento = new Date(fechaNacimientoStr)
    const hoy = new Date()
    let anios = hoy.getFullYear() - fechaNacimiento.getFullYear()
    let meses = hoy.getMonth() - fechaNacimiento.getMonth()
    let dias = hoy.getDate() - fechaNacimiento.getDate()

    if (dias < 0) {
        meses--
        dias += new Date(hoy.getFullYear(), hoy.getMonth(), 0).getDate()
    }
    if (meses < 0) {
        anios--
        meses += 12
    }
    return {years: anios, months: meses, days: dias}
}

const clearSearchDocumento = () => {
    nroDocumento.value = '';
    inputDocumentoRef.value?.focus();
};

// const buscarPaciente = async () => {
//     // isUpdate.value = true
//     loading.value = false;
//     const {data} = await axios.get(`/personas/pacientes/record/${props.pacienteId}`);
//     const edadCompleta = calcularEdadCompleta(data.data.FechaNacimientoString)
//     form.value.nombre = `${data.data.ApellidoPaterno || ''} ${data.data.ApellidoMaterno || ''}
//                          ${data.data.PrimerNombre || ''} ${data.data.SegundoNombre || ''} ${data.data.TercerNombre || ''}`;
//     form.value.dni = `DNI: ${data.data.NroDocumento}`;
//     form.value.hc = `HC: ${data.data.NroHistoriaClinica ?? 'SIN DATOS'}`;
//     form.value.edad = `EDAD: ${edadCompleta.years} A, ${edadCompleta.months} M, ${edadCompleta.days} D`;
//     form.value.sexo = `SEXO: ${data.data.IdTipoSexo === 1 ? 'MASCULINO' : 'FEMENINO'}`;
//     const defaultFoto = data.data.IdTipoSexo === 1
//         ? '../../../assets/img/sexo1.gif'
//         : '../../../assets/img/sexo2.gif';
//     form.value.foto = defaultFoto ?? null;
//     form.value.correo = data.data.Email ?? '';
//     form.value.telefono = data.data.Telefono ?? '';
//     form.value.id = props.pacienteId;
//     loading.value = true;
// }

// limpiarPaciente();

watch(
    () => props.paciente,
    async (newId, oldId) => {
        if (newId) {
            console.log('watch paciente', newId, oldId);
            limpiarPaciente();
            actualizarDatosPaciente();
        }
        // if (busquedaInterna.value) {
        //     console.log('watch pacienteId', 'no busca');
        //     busquedaInterna.value = false;
        //     return;
        // }
        // // Solo busca si el id realmente cambió
        // if (newId && newId !== oldId) {
        //     console.log('watch pacienteId', 'busca');
        //     await buscarPaciente(newId);
        // }
    },
    {immediate: true}
);
</script>

<template>
    <div class="row">
        <!--        <div class="col-xl-4 col-lg-5 col-md-12" v-if="accion === 'registrar'">-->
        <!--            <div class="p-3 border rounded" style="border: 1px solid #7367f0!important; height: 125px;">-->
        <!--                <BaseCombo v-model="tipoDocumento"-->
        <!--                           :options="tiposDocumento"-->
        <!--                           optionLabel="label"-->
        <!--                           optionValue="value"-->
        <!--                           label="BUSCAR POR:" :disabled="viewRecord"/>-->
        <!--                <div class="d-flex align-items-center mt-2">-->
        <!--                    <div class="input-group">-->
        <!--                        <input type="text"-->
        <!--                               class="form-control"-->
        <!--                               ref="inputDocumentoRef"-->
        <!--                               placeholder="NÚMERO DE BÚSQUEDA"-->
        <!--                               aria-label="Text input"-->
        <!--                               :disabled="viewRecord"-->
        <!--                               v-model="nroDocumento"-->
        <!--                               autocomplete="off"-->
        <!--                               autofocus-->
        <!--                               @keydown.enter="buscarPacienteTriajeEmergencia">-->
        <!--                        <button class="btn btn-primary waves-effect" type="button" id="btnConsultarPacienteCita"-->
        <!--                                :disabled="viewRecord" name="btnConsultarPacienteCita"-->
        <!--                                style=" height: 2rem !important;"-->
        <!--                                @click="buscarPacienteTriajeEmergencia">-->
        <!--                            <span class="ti ti-search" style="font-size: 17px;"></span>-->
        <!--                        </button>-->
        <!--                        <button class="btn btn-danger" @click="clearSearchDocumento">-->
        <!--                            <i class="fas fa-times"></i>-->
        <!--                        </button>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!-- Perfil paciente -->
        <div class="col-xl col-lg-4 col-md-12">
            <div class="p-3 border rounded" style="border: 1px solid #7367f0!important;">
                <div class="d-flex align-items-start">
                    <div class="me-3 position-relative">
                        <img :src="form.foto" :key="form.foto" alt="Foto paciente"
                             class="img-fluid rounded"
                             style="width: 98px; height: 98px;"/>
                        <div v-if="loadingFoto"
                             class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-light rounded">
                            <div class="spinner-border text-primary" role="status"
                                 style="width: 1.5rem; height: 1.5rem;"></div>
                        </div>
                    </div>
                    <div style="font-size: 0.7rem; text-transform: uppercase;">
                        <label><b>INFORMACIÓN GENERAL:</b></label><br/>
                        <strong class="text-success">{{ form.nombre }}</strong><br/>
                        <strong>{{ form.dni }}</strong><br/>
                        <strong>{{ form.hc }}</strong><br/>
                        <strong>{{ form.edad }}</strong><br/>
                        <strong>{{ form.sexo }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contacto -->
        <div class="col-xl-3 col-lg-6 col-md-12">
            <div class="p-3 border rounded" style="border: 1px solid #7367f0!important;">
                <label><b>DATOS DE CONTACTO:</b></label>
                <div class="mb-2">
                    <w-input v-model="form.correo"
                             placeholder="CORREO ELECTRÓNICO"
                             class="w-full"
                             :disabled="viewRecord || (accion !== 'registrar')"/>
                </div>
                <div class="flex gap-2">
                    <w-input v-model="form.telefono"
                             placeholder="NÚMERO DE CELULAR"
                             class="flex-1"
                             :disabled="viewRecord || (accion !== 'registrar')"/>
                </div>
            </div>
        </div>

        <!--        <ModalFormulario ref="refDialogNNForm"-->
        <!--                         :resource="resource"-->
        <!--                         @success="successNoIdentificado"/>-->
    </div>
</template>
