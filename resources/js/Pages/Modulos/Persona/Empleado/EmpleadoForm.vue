<script setup>
import {ref, computed} from 'vue'
import axios from 'axios'
import {useAppStore} from "../../../../stores/useAppStore.js";
import BaseModal from '@/components/BaseModal.vue'
import BaseInput from "@/components/WInput/WInput.vue";
import BaseDatePicker from "@/components/WDatePicker/WDatePicker.vue";
import BaseCombo from "@/components/WSelect/WSelect.vue";
import WCheckbox from "@/components/WCheckbox/WCheckbox.vue";
import Select from "primevue/select";
import Tabs from "primevue/tabs";
import Tab from "primevue/tab";
import TabList from "primevue/tablist";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import MultiSelect from "primevue/multiselect";
import MyLib from '../../../../mixins/lib';
import ColegioEspecialidad from "./ColegioEspecialidad.vue";
import BaseMultiSelect from "@/components/BaseMultiSelect.vue";
import WButton from "@/components/WButton/WButton.vue";
import {showToastSuccess} from "../../../../utils/alert.js";

const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let appStore = useAppStore();
let isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let isModalLoading = ref(false);
let showModal = ref(false);
let debounceTimeout = null;
let refDialogColegioEspecialidad = ref()

let isViewMode = computed(() => props.viewRecord);
let loadingEstablecimientos = ref(false);
let loadingEmpleados = ref(false);
let loadingLugaresLaborales = ref(false);

let title = ref('');
let errors = ref({});
let form = ref({});
let establecimientos = ref([]);
let empleados = ref([]);
let lugaresLaborales = ref([]);
let especialidades = ref([]);
let colegioEspecialidades = ref([]);

let idCargo = ref(null);
let idRole = ref(null);
let idRol = ref(null);
let idTipoLugarLaboral = ref(null);
let idLugarLaboral = ref(null);
let idDepartamentoHospital = ref(null);
let idEspecialidad = ref(null);
let idMedico = ref(null);
let tieneUsuarioInitial = ref(false);
let tieneWebEspecialidadesInitial = ref(false);
let idWebEspecialidad = ref(null);

const initForm = () => {
    errors.value = {};
    form.value = {
        ApellidoPaterno: null,
        ApellidoMaterno: null,
        Nombres: null,
        IdTipoEmpleado: null,
        IdCondicionTrabajo: null,
        DNI: null,
        CodigoPlanilla: null,
        FechaIngreso: null,
        FechaAlta: null,
        UsuarioWeb: null,
        ClaveWeb: null,
        loginEstado: null,
        loginPC: null,
        FechaNacimiento: null,
        FechaNacimientoString: null,
        idTipoDestacado: 3,
        IdEstablecimientoExterno: null,
        HisCodigoDigitador: null,
        ReniecAutorizado: null,
        idTipoDocumento: 1,
        idSupervisor: null,
        esActivo: true,
        AccedeVWeb: null,
        ClaveVWeb: null,
        sexo: null,
        pais: null,
        idSexo: null,
        idEspecialidades: null,
        esProfesionalSalud: false,
        Colegiatura: null,
        LoteHIS: null,
        rne: null,
        idColegioHIS: '00',
        egresado: false,
        roles: [],
        usuario_roles: [],
        web_especialidades: [],
        cargos: [],
        lugaresTrabajos: [],
        especialidades: [],
        tieneUsuario: true,
        tieneWebEspecialidades: false,
        ImagenFirma: null,
        ImagenFoto: null,
        ImagenFirma64: null,
        ImagenFoto64: null,
        Usuario: null,
        Clave: null,
        BuscadoPorReniec: false,
    }
    idTipoLugarLaboral.value = 0;
    tieneUsuarioInitial.value = false;
}

const handleOpen = async () => {
    initForm();
    title.value = 'Nuevo empleado';

    if (props.recordId) {
        try {
            isModalLoading.value = true;
            showModal.value = false;
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form.value, data.data)

            const [year, month, day] = form.value.FechaNacimientoString.split('-');
            form.value.FechaNacimiento = new Date(year, month - 1, day);
            tieneUsuarioInitial.value = form.value.tieneUsuario;
            title.value = 'Editar empleado'
        } catch (error) {
            if (error.status === 403) {

                closeDialog();
            }
        } finally {
            showModal.value = true;
            isModalLoading.value = false;
        }
    } else {
        showModal.value = true;
    }
}

const fetchOptionsEstablecimientos = async (query) => {
    if (!query) {
        establecimientos.value = [];
        return;
    }
    loadingEstablecimientos.value = true;
    try {
        const response = await axios.post('/filtrar_catalogo_establecimientos', {buscar: query});
        establecimientos.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingEstablecimientos.value = false;
    }
}

const onFilterEstablecimientos = (event) => {
    if (event.value.length > 3) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsEstablecimientos(event.value);
        }, 500);
    }
}

const fetchOptionsEmpleados = async (query) => {
    if (!query) {
        empleados.value = [];
        return;
    }
    loadingEmpleados.value = true;
    try {
        const response = await axios.post('/filtrar_persona_empleados', {buscar: query});
        empleados.value = response.data;
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        loadingEmpleados.value = false;
    }
}

const onFilterEmpleados = (event) => {
    if (event.value.length > 3) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetchOptionsEmpleados(event.value);
        }, 500);
    }
}

const agregarCargo = () => {
    if (idCargo.value !== null) {
        let cargo = appStore.personaTipoCargos.find(row => row.id === idCargo.value);
        form.value.cargos.push({
            IdCargo: cargo.id,
            Nombre: cargo.label,
        })
        idCargo.value = null;
    }
}

const removerCargo = (index) => {
    form.value.cargos.splice(index, 1);
}

const agregarRole = () => {
    if (idRole.value !== null) {
        let rol = appStore.seguridadRoles.find(row => row.id === idRole.value);
        form.value.roles.push({
            IdRol: rol.id,
            Nombre: rol.label,
        })
        idRole.value = null;
    }
}

const removerRole = (index) => {
    form.value.roles.splice(index, 1);
}

const agregarUsuarioRole = () => {
    if (idRol.value !== null) {
        let rol = appStore.seguridadRole.find(row => row.id === idRol.value);
        form.value.usuario_roles.push({
            id: rol.id,
            name: rol.label,
        })
        idRol.value = null;
    }
}

const removerUsuarioRole = (index) => {
    form.value.usuario_roles.splice(index, 1);
}

const agregarLugarTrabajo = () => {
    if (idTipoLugarLaboral.value !== null && idLugarLaboral.value !== null) {
        let tipoLugarLaboral = appStore.configuracionTipoLugaresLaborales.find(row => row.value === idTipoLugarLaboral.value);
        let lugarLaboral = lugaresLaborales.value.find(row => row.value === idLugarLaboral.value);
        form.value.lugaresTrabajos.push({
            IdLaboraArea: idTipoLugarLaboral.value,
            Area: tipoLugarLaboral.label,
            IdLaboraSubArea: idLugarLaboral.value,
            SubArea: lugarLaboral.label,
        })
        idTipoLugarLaboral.value = 0;
        idLugarLaboral.value = null;
    }
}

const removerLugarTrabajo = (index) => {
    form.value.lugaresTrabajos.splice(index, 1);
}

const changeTipoLugarLaboral = async () => {
    lugaresLaborales.value = [];
    if (idTipoLugarLaboral.value !== 0) {
        loadingLugaresLaborales.value = true;
        try {
            const response = await axios.post(`/filtrar_configuracion_lugares_laborales_por_tipo`, {
                tipo: idTipoLugarLaboral.value
            });
            lugaresLaborales.value = response.data;
        } catch (error) {
            console.error('Error al cargar opciones:', error);
        } finally {
            loadingLugaresLaborales.value = false;
        }
    }
}

const changeDepartamentoHospital = () => {
    idEspecialidad.value = null;
    especialidades.value = appStore.configuracionEspecialidades.filter(row => row.IdDepartamento === idDepartamentoHospital.value);
}

const agregarEspecialidad = () => {
    if (idEspecialidad.value !== null) {
        let especialidad = appStore.configuracionEspecialidades.find(row => row.id === idEspecialidad.value);
        form.value.especialidades.push({
            IdEspecialidad: especialidad.id,
            Nombre: especialidad.label,
            idEstado: true,
        })
        idDepartamentoHospital.value = null;
        idEspecialidad.value = null;
    }
}

const removerEspecialidad = (index) => {
    form.value.especialidades.splice(index, 1);
}

const copiarEspecialidades = () => {
    form.value.especialidades = [];
    let medico = appStore.personaMedicos.find(row => row.id === idMedico.value);
    form.value.especialidades = medico.especialidades;
    idMedico.value = null;
}

const buscarColegiatura = async () => {
    isModalLoading.value = true;
    await axios.post(`/${props.resource}/consultar-colegiatura`, {
        ApellidoPaterno: form.value.ApellidoPaterno,
        ApellidoMaterno: form.value.ApellidoMaterno,
        Nombres: form.value.Nombres,
        idColegioHIS: form.value.idColegioHIS
    })
        .then(response => {
            let res = response.data;
            if (res.success) {
                if (res.data.length > 1) {
                    colegioEspecialidades.value = res.data;
                    refDialogColegioEspecialidad.value.openDialog();
                } else {
                    form.value.Colegiatura = res.data[0].colegiatura;
                    form.value.rne = res.data[0].codigo;
                }
            } else {
                return showAlert('ERROR DURANTE EL PROCESO', res.mensaje, 'error');
            }
        })
        .finally(() => {
            isModalLoading.value = false;
        })
}

const successColegioEspecialidad = (data) => {
    form.value.Colegiatura = data.colegiatura;
    form.value.rne = data.codigo;
}

const buscarDni = async () => {
    isModalLoading.value = true;
    try {
        const {data} = await axios.post('/reniec/avanzado/consultar', {dni: form.value.DNI});
        if (data.success) {
            form.value.ApellidoPaterno = data.data.ApellidoPaterno;
            form.value.ApellidoMaterno = data.data.ApellidoMaterno;
            form.value.Nombres = data.data.Nombres;
            form.value.idSexo = Number(data.data.Sexo);
            form.value.sexo = Number(data.data.Sexo);

            const [day, month, year] = data.data.FechaNacimiento.split('/');
            form.value.FechaNacimiento = new Date(year, month - 1, day);

            form.value.ImagenFoto64 = data.data.ImagenFoto
            form.value.ImagenFirma64 = data.data.ImagenFirma
            form.value.BuscadoPorReniec = true;
        }
    } catch (error) {
        console.error('Error al cargar opciones:', error);
    } finally {
        isModalLoading.value = false;
    }
}

const onSubmit = async () => {

    if (form.value.DNI === '' || form.value.DNI === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo DNI es requerido', 'error');
    }

    if (form.value.ApellidoPaterno === '' || form.value.ApellidoPaterno === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Apellido Paterno es requerido', 'error');
    }

    if (form.value.ApellidoMaterno === '' || form.value.ApellidoMaterno === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Apellido Materno es requerido', 'error');
    }

    if (form.value.Nombres === '' || form.value.Nombres === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Nombres es requerido', 'error');
    }

    if (form.value.FechaNacimiento === '' || form.value.FechaNacimiento === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Fecha de Nacimiento es requerido', 'error');
    }

    if (form.value.CodigoPlanilla === '' || form.value.CodigoPlanilla === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo Codigo de Planilla es requerido', 'error');
    }

    if (form.value.IdTipoEmpleado === '' || form.value.IdTipoEmpleado === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo tipo de empleado es requerido', 'error');
    }

    if (form.value.IdCondicionTrabajo === '' || form.value.IdCondicionTrabajo === null) {
        return showAlert('ERROR DURANTE EL PROCESO', 'El campo condición de trabajo es requerido', 'error');
    }

    if (form.value.tieneUsuario) {
        if (form.value.Usuario === '' || form.value.Usuario === null) {
            return showAlert('ERROR DURANTE EL PROCESO', 'El campo usuario es requerido', 'error');
        }

        if ((form.value.Clave === '' || form.value.Clave === null) && !props.recordId) {
            return showAlert('ERROR DURANTE EL PROCESO', 'El campo clave es requerido', 'error');
        }

        if (form.value.usuario_roles.length === 0) {
            return showAlert('ERROR DURANTE EL PROCESO', 'Debe registrar por lo menos un rol', 'error');
        }
    }

    if (form.value.tieneWebEspecialidades) {
        if (form.value.web_especialidades.length === 0) {
            return showAlert('ERROR DURANTE EL PROCESO', 'Se requiere al menos una especialidad', 'error');
        }
    }

    if (form.value.esProfesionalSalud) {
        if ((form.value.Colegiatura === '' || form.value.Colegiatura === null)) {
            return showAlert('ERROR DURANTE EL PROCESO', 'El campo colegiatura es requerido', 'error');
        }
        if ((form.value.rne === '' || form.value.rne === null)) {
            return showAlert('ERROR DURANTE EL PROCESO', 'El campo rne es requerido', 'error');
        }
        if ((form.value.idColegioHIS === '' || form.value.idColegioHIS === null)) {
            return showAlert('ERROR DURANTE EL PROCESO', 'El campo colegio profesional es requerido', 'error');
        }
    }

    const accionConfirmacion = props.recordId ? 'LA ACTUALIZACIÓN SOLICITADA, PAR EL REGISTRO SELECCIONADO' : 'EL REGISTRO SOLICITADO, PARA LA INFORMACIÓN INGRESADA';
    const mensajeConfirmacion = `¿ESTÁ SEGURO QUE DESEA REALIZAR ${accionConfirmacion}?`;
    const confirmado = await showAlertConfirmacion(
        'VALIDACIÓN DE DATOS REALIZADOS', mensajeConfirmacion,
        'warning'
    );


    form.value.FechaNacimientoString = MyLib.formatDateToYMD(form.value.FechaNacimiento);

    // Si el usuario confirma, procedemos con el guardado
    if (confirmado) {
        loadingSubmit.value = true;

        await axios.post(`/${props.resource}`, form.value)
            .then(response => {
                const data = response.data;
                if(data.success) {
                    emit('success');
                    closeDialog();
                    showToastSuccess(data.mensaje)
                } else {
                    showToastError(data.mensaje)
                }
            })
            .catch(error => {
                if (error.response.status === 422) {
                    errors.value = error.response.data.errors
                }
            })
            .finally(() => {
                loadingSubmit.value = false;
            })
    }
};

const changeTieneUsuario = () => {
    if (form.value.tieneUsuario && (form.value.Usuario === '' || form.value.Usuario === null)) {
        form.value.Usuario = MyLib.generateUser(form.value.Nombres, form.value.ApellidoPaterno)
    }
}

const agregarWebEspecialidad = () => {
    console.log(idWebEspecialidad.value);

    if (idWebEspecialidad.value.length > 0) {
        idWebEspecialidad.value.forEach(id => {
            let webEspecialidad = appStore.citaAtencionInterconsultaEspecialidades.find(row => row.id === id);
            form.value.web_especialidades.push({
                id: webEspecialidad.id,
                name: webEspecialidad.label,
            })
        })
        // let webEspecialidad = appStore.citaAtencionInterconsultaEspecialidades.find(row => row.id === idWebEspecialidad.value);
        // form.value.web_especialidades.push({
        //     id: webEspecialidad.id,
        //     name: webEspecialidad.label,
        // })
        idWebEspecialidad.value = null;
    }
}

const removerWebEspecialidad = (index) => {
    form.value.web_especialidades.splice(index, 1);
}

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :recordId="form.id"
               :viewRecord="isViewMode"
               @close="closeDialog"
               @open="handleOpen"
               :loading="isModalLoading"
               size="modal-lg">
        <Tabs value="0">
            <TabList>
                <Tab value="0">Datos</Tab>
                <Tab value="1">Cargos</Tab>
                <Tab value="2">Roles Galenos</Tab>
                <Tab value="3">Lugares de trabajo</Tab>
                <Tab value="4" v-if="form.esProfesionalSalud">Medico</Tab>
                <Tab value="5" v-if="form.tieneUsuario">Seguridad</Tab>
                <Tab value="6" v-if="form.tieneWebEspecialidades">Especialidades</Tab>
            </TabList>
            <TabPanels style="padding: 16px 0">
                <TabPanel value="0">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <base-combo label="Tipo de documento"
                                        v-model="form.idTipoDocumento"
                                        :options="appStore.personaTipoDocumentosIdentidad"
                                        :show-clear="false"
                                        :disabled="isViewMode"></base-combo>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.DNI"
                                       label="Número de documento"
                                       :disabled="isViewMode"
                                       :show-button-icon="!isViewMode"
                                       @click-button="buscarDni"
                                       :maxlength="8"
                                       :error="errors.DNI"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.ApellidoPaterno"
                                       label="Apellido paterno"
                                       :disabled="isViewMode"
                                       :error="errors.ApellidoPaterno"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.ApellidoMaterno"
                                       label="Apellido materno"
                                       :disabled="isViewMode"
                                       :error="errors.ApellidoMaterno"/>
                        </div>
                        <div class="col-12 col-sm-6">
                            <BaseInput v-model="form.Nombres"
                                       label="Nombres"
                                       :disabled="isViewMode"
                                       :error="errors.Nombres"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseDatePicker v-model="form.FechaNacimiento"
                                            label="Fecha de nacimiento"
                                            :disabled="isViewMode"
                                            :error="errors.FechaNacimiento"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <base-combo label="Sexo"
                                        v-model="form.idSexo"
                                        :options="appStore.personaTipoSexos"
                                        :disabled="isViewMode"
                                        :show-clear="false"></base-combo>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.CodigoPlanilla"
                                       label="Código de planilla"
                                       :disabled="isViewMode"
                                       :error="errors.CodigoPlanilla"/>
                        </div>
                        <div class="col-12 col-md-6">
                            <base-combo label="Tipo de empleado"
                                        v-model="form.IdTipoEmpleado"
                                        :options="appStore.personaTipoEmpleados"
                                        :disabled="isViewMode"
                                        :show-clear="false"
                                        filter></base-combo>
                        </div>
                        <div class="col-12 col-md-6">
                            <base-combo label="Condición de trabajo"
                                        v-model="form.IdCondicionTrabajo"
                                        :options="appStore.personaTipoCondicionesTrabajo"
                                        :disabled="isViewMode"
                                        :show-clear="false"
                                        filter></base-combo>
                        </div>
                        <div class="col-12 col-md-6">
                            <base-combo label="Tipo destacado"
                                        v-model="form.idTipoDestacado"
                                        :options="appStore.personaTipoDestacados"
                                        :disabled="isViewMode"
                                        :show-clear="false"></base-combo>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Cs, Ps externo donde labora</label>
                            <Select v-model="form.IdEstablecimientoExterno"
                                    :options="establecimientos"
                                    option-label="label"
                                    option-value="value"
                                    filter
                                    filterPlaceholder="Buscar..."
                                    @filter="onFilterEstablecimientos"
                                    :loading="loadingEstablecimientos"
                                    placeholder="Seleccione una opción"
                                    :showClear="true"
                                    class="w-full"
                                    size="small"
                                    style="width: 100%;"
                                    :disabled="isViewMode"
                                    :autoFilterFocus="true"></Select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Supervisor</label>
                            <Select v-model="form.idSupervisor"
                                    :options="empleados"
                                    option-label="label"
                                    option-value="value"
                                    filter
                                    filterPlaceholder="Buscar..."
                                    @filter="onFilterEmpleados"
                                    :loading="loadingEmpleados"
                                    placeholder="Seleccione una opción"
                                    :showClear="true"
                                    class="w-full"
                                    size="small"
                                    style="width: 100%;"
                                    :disabled="isViewMode"
                                    :autoFilterFocus="true"></Select>
                        </div>
                        <div class="col-6">
                            <w-checkbox v-model="form.esProfesionalSalud"
                                           :disabled="isViewMode"
                                           label="Es profesional de salud">
                            </w-checkbox>
                        </div>
                        <div class="col-6">
                            <w-checkbox v-model="form.tieneUsuario"
                                           :disabled="isViewMode || tieneUsuarioInitial"
                                           @update:model-value="changeTieneUsuario"
                                           label="Tiene usuario">
                            </w-checkbox>
                        </div>
                        <div class="col-6">
                            <w-checkbox v-model="form.tieneWebEspecialidades"
                                           :disabled="isViewMode || tieneWebEspecialidadesInitial"
                                           label="Tiene especialidades">
                            </w-checkbox>
                        </div>
                        <div class="col-6">
                            <w-checkbox v-model="form.esActivo"
                                           :disabled="isViewMode"
                                           label="Activo">
                            </w-checkbox>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel value="1">
                    <div class="row">
                        <div class="col-12" v-if="!isViewMode">
                            <base-combo label="Asignar cargos"
                                        v-model="idCargo"
                                        :options="appStore.personaTipoCargos"
                                        :show-clear="false"
                                        :show-button="!isViewMode"
                                        :disabled="isViewMode"
                                        @click-button="agregarCargo"
                                        filter></base-combo>
                        </div>
                        <div class="col-12" style="margin-top: 16px"
                             v-if="form && form.cargos && form.cargos.length > 0">
                            <table class="table table-sm table-hover">
                                <tbody>
                                <tr v-for="(c, index) in form.cargos">
                                    <td>
                                        <div class="salto-de-linea" style="width: 260px">
                                            {{ c.Nombre }}
                                        </div>
                                    </td>
                                    <td style="text-align: right">
                                        <button @click="removerCargo(index)"
                                                class="btn btn-danger btn-sm"
                                                v-if="!isViewMode">
                                            <i class="ti ti-trash"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel value="2">
                    <div class="row">
                        <div class="col-12" v-if="!isViewMode">
                            <base-combo label="Asignar roles"
                                        v-model="idRole"
                                        :options="appStore.seguridadRoles"
                                        :show-clear="false"
                                        :show-button="!isViewMode"
                                        :disabled="isViewMode"
                                        @click-button="agregarRole"
                                        filter></base-combo>
                        </div>
                        <div class="col-12" style="margin-top: 16px" v-if="form && form.roles && form.roles.length > 0">
                            <table class="table table-sm table-hover">
                                <tbody>
                                <tr v-for="(c, index) in form.roles">
                                    <td>
                                        <div class="salto-de-linea" style="width: 260px">
                                            {{ c.Nombre }}
                                        </div>
                                    </td>
                                    <td style="text-align: right">
                                        <button @click="removerRole(index)"
                                                class="btn btn-danger btn-sm"
                                                v-if="!isViewMode">
                                            <i class="ti ti-trash"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel value="3">
                    <div class="row">
                        <template v-if="!isViewMode">
                            <div class="col-12 col-md-5">
                                <base-combo label="Asignar lugares de trabajo"
                                            v-model="idTipoLugarLaboral"
                                            :options="appStore.configuracionTipoLugaresLaborales"
                                            :disabled="isViewMode"
                                            @update:model-value="changeTipoLugarLaboral"
                                            filter></base-combo>
                            </div>
                            <div class="col-12 col-md-7" style="display: flex; align-items: flex-end;">
                                <base-combo v-model="idLugarLaboral"
                                            :options="lugaresLaborales"
                                            :show-clear="false"
                                            :show-button="!isViewMode"
                                            :disabled="isViewMode"
                                            @click-button="agregarLugarTrabajo"
                                            filter></base-combo>
                            </div>
                        </template>
                        <div class="col-12" style="margin-top: 16px"
                             v-if="form && form.lugaresTrabajos && form.lugaresTrabajos.length > 0">
                            <table class="table table-sm table-hover">
                                <thead>
                                <tr>
                                    <th>Area</th>
                                    <th>SubArea</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(c, index) in form.lugaresTrabajos">
                                    <td>
                                        <div class="salto-de-linea" style="width: 260px">
                                            {{ c.Area }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="salto-de-linea" style="width: 260px">
                                            {{ c.SubArea }}
                                        </div>
                                    </td>
                                    <td style="text-align: right">
                                        <button @click="removerLugarTrabajo(index)"
                                                class="btn btn-danger btn-sm"
                                                v-if="!isViewMode">
                                            <i class="ti ti-trash"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel value="4" v-if="form.esProfesionalSalud">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.Colegiatura"
                                       label="Colegiatura"
                                       :disabled="isViewMode"
                                       :error="errors.Colegiatura"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.LoteHIS"
                                       label="Lote HIS"
                                       :disabled="isViewMode"
                                       :error="errors.LoteHIS"/>
                        </div>
                        <div class="col-12 col-sm-6">
                            <base-combo label="Colegio profesional"
                                        v-model="form.idColegioHIS"
                                        :options="appStore.configuracionHisColegios"
                                        :show-clear="false"
                                        :show-button="!isViewMode"
                                        :disabled="isViewMode"
                                        @click-button="buscarColegiatura"
                                        filter></base-combo>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.rne"
                                       label="RNE"
                                       :disabled="isViewMode"
                                       :error="errors.rne"/>
                        </div>
                        <div class="col-12 col-sm-3" style="display: flex;align-items: flex-end;">
                            <w-checkbox v-model="form.egresado"
                                           :disabled="isViewMode"
                                           label="Egresado">
                            </w-checkbox>
                        </div>
                        <div class="col-12" v-if="!isViewMode">
                            <base-combo label="Copiar especialidades"
                                        v-model="idMedico"
                                        :options="appStore.personaMedicos"
                                        :show-clear="false"
                                        :show-button="!isViewMode"
                                        :disabled="isViewMode"
                                        @click-button="copiarEspecialidades"
                                        filter></base-combo>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <template v-if="!isViewMode">
                                    <div class="col-12 col-sm-6">
                                        <base-combo label="Departamento"
                                                    v-model="idDepartamentoHospital"
                                                    :options="appStore.configuracionDepartamentosHospital"
                                                    :show-clear="false"
                                                    :disabled="isViewMode"
                                                    @update:model-value="changeDepartamentoHospital"
                                                    filter></base-combo>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <base-combo label="Especialidad"
                                                    v-model="idEspecialidad"
                                                    :options="especialidades"
                                                    :show-clear="false"
                                                    :show-button="!isViewMode"
                                                    :disabled="isViewMode"
                                                    @click-button="agregarEspecialidad"
                                                    filter></base-combo>
                                    </div>
                                </template>
                                <div class="col-12" style="margin-top: 16px"
                                     v-if="form && form.especialidades && form.especialidades.length > 0">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(c, index) in form.especialidades">
                                            <td>
                                                <div class="salto-de-linea" style="width: 260px">
                                                    {{ c.Nombre }}
                                                </div>
                                            </td>
                                            <td>
                                                <w-checkbox v-model="c.idEstado"
                                                               :disabled="isViewMode"></w-checkbox>
                                            </td>
                                            <td style="text-align: right">
                                                <button @click="removerEspecialidad(index)"
                                                        class="btn btn-danger btn-sm"
                                                        v-if="!isViewMode">
                                                    <i class="ti ti-trash"></i></button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel value="5" v-if="form.tieneUsuario">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.Usuario"
                                       label="Usuario"
                                       :disabled="isViewMode"
                                       :error="errors.Usuario"/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <BaseInput v-model="form.Clave"
                                       label="Clave"
                                       :disabled="isViewMode"
                                       :error="errors.Clave"/>
                        </div>
                        <div class="col-12" v-if="!isViewMode">
                            <base-combo label="Asignar roles"
                                        v-model="idRol"
                                        :options="appStore.seguridadRole"
                                        :show-clear="false"
                                        :show-button="!isViewMode"
                                        @click-button="agregarUsuarioRole"
                                        filter></base-combo>
                        </div>
                        <div class="col-12" style="margin-top: 16px"
                             v-if="form && form.usuario_roles && form.usuario_roles.length > 0">
                            <table class="table table-sm table-hover">
                                <tbody>
                                <tr v-for="(c, index) in form.usuario_roles">
                                    <td>
                                        <div class="salto-de-linea" style="width: 260px">
                                            {{ c.name }}
                                        </div>
                                    </td>
                                    <td style="text-align: right">
                                        <button @click="removerUsuarioRole(index)"
                                                class="btn btn-danger btn-sm"
                                                v-if="!isViewMode">
                                            <i class="ti ti-trash"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel value="6" v-if="form.tieneWebEspecialidades">
                    <div class="row">
                        <div class="col-12" v-if="!isViewMode">
                            <base-multi-select label="Asignar especialidades"
                                               v-model="idWebEspecialidad"
                                               :options="appStore.citaAtencionInterconsultaEspecialidades"
                                               :show-button="!isViewMode"
                                               @click-button="agregarWebEspecialidad"
                                               filter/>

                            <!--                            <base-combo label="Asignar especialidades"-->
                            <!--                                        v-model="idWebEspecialidad"-->
                            <!--                                        :options="appStore.citaAtencionInterconsultaEspecialidades"-->
                            <!--                                        :show-clear="false"-->
                            <!--                                        :show-button="!isViewMode"-->
                            <!--                                        @click-button="agregarWebEspecialidad"-->
                            <!--                                        filter></base-combo>-->
                        </div>
                        <div class="col-12" style="margin-top: 16px"
                             v-if="form && form.web_especialidades && form.web_especialidades.length > 0">
                            <table class="table table-sm table-hover">
                                <tbody>
                                <tr v-for="(c, index) in form.web_especialidades">
                                    <td>
                                        <div class="salto-de-linea" style="width: 260px">
                                            {{ c.name }}
                                        </div>
                                    </td>
                                    <td style="text-align: right">
                                        <button @click="removerWebEspecialidad(index)"
                                                class="btn btn-danger btn-sm"
                                                v-if="!isViewMode">
                                            <i class="ti ti-trash"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </TabPanel>
            </TabPanels>
        </Tabs>
        <div class="d-flex justify-content-end mt-4 gap-2">
            <w-button type="secondary"
                     label="Cerrar"
                     text
                     @click="closeDialog"
                     :disabled="loadingSubmit"/>
            <w-button type="primary"
                     :label="`${loadingSubmit?'Guardando...' : 'Guardar'}`"
                     @click="onSubmit"
                     v-if="!isViewMode"
                     :disabled="loadingSubmit"/>

            <colegio-especialidad ref="refDialogColegioEspecialidad"
                                  :especialidades="colegioEspecialidades"
                                  @success="successColegioEspecialidad"/>
        </div>
    </BaseModal>
</template>
