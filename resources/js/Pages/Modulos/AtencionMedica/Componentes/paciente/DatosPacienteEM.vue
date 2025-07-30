<script setup>
import { ref, onMounted, computed } from 'vue';

import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});

let isViewMode = computed(() => props.viewRecord)
const fotoPaciente = ref('');

onMounted(() => {
    fotoPaciente.value = props.cabecera.IdTipoSexo === 1
        ? '/assets/img/sexo1.gif'
        : '/assets/img/sexo2.gif';
});
</script>

<template>
    <Tabs value="0">
        <TabList>
            <Tab value="0">Datos del Paciente</Tab>
        </TabList>

        <TabPanels>
            <TabPanel value="0">
                <div class="d-flex bg-white">
                    <!-- Foto -->
                    <img :src="fotoPaciente" :key="fotoPaciente" alt="Foto paciente" class="img-thumbnail shadow-sm"
                        style="width: 108px; height: 108px; object-fit: cover;" />

                    <!-- Información -->
                    <div class="ms-4 flex-grow-1 w-100">
                        <h5 class="fw-bold mb-3 text-primary fs-5">
                            {{ cabecera.Paciente }} - {{ cabecera.Doc_Identidad }}
                        </h5>

                        <div class="row fs-6" style="font-size: 12px !important; text-transform: uppercase;">
                            <!-- Columna 1 -->
                            <div class="col-md-3 mb-3">
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li><span class="fw-bold">HISTORIA CLÍNICA:</span> {{ cabecera.HistoriaClinica }}
                                    </li>
                                    <li><span class="fw-bold">SEXO:</span> {{ cabecera.Sexo }}</li>
                                    <li><span class="fw-bold">EDAD:</span> {{ cabecera.Edad }}</li>
                                    <li>
                                        <span class="fw-bold">NACIMIENTO:</span> {{ 'FECHA' }}
                                    </li>
                                    <li>
                                        <span class="fw-bold">OCUPACIÓN:</span> {{ 'OCUPACIÓN' }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Columna 2 -->
                            <div class="col-md-3 mb-3 border-start ps-3">
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li>
                                        <span class="fw-bold">RELIGIÓN:</span> {{ 'RELIGIÓN' }}
                                    </li>
                                    <li><span class="fw-bold">Estado:</span> {{ cabecera.EstadosColaCitas }}</li>
                                    <li>
                                        <span class="fw-bold">IDIOMA:</span> {{ 'IDIOMA' }}
                                    </li>
                                    <li>
                                        <span class="fw-bold">GRADO DE INSTRUCCIÓN:</span> {{ 'GRADO DE INSTRUCCIÓN' }}
                                    </li>
                                    <li>
                                        <span class="fw-bold">ESTADO CIVIL:</span> {{ 'ESTADO CIVIL' }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Columna 3 -->
                            <div class="col-md-3 mb-3 border-start ">
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li><span class="fw-bold">Teléfono:</span> {{ cabecera.Telefono }}</li>
                                    <li><span class="fw-bold">Correo:</span> {{ cabecera.Correo || 'No registrado' }}
                                    </li>
                                    <li><span class="fw-bold">Origen:</span> {{ cabecera.OrigenCita ?? 'GALENOS' }}</li>
                                    <li><span class="fw-bold">Financiamiento:</span> {{ cabecera.FuenteFinanciamiento }}
                                    </li>
                                    <li><span class="fw-bold">Servicio:</span> {{ cabecera.Servicio }}</li>
                                </ul>
                            </div>

                            <!-- Columna 4 (Botones de acciones clínicas) -->
                            <div class="col-md-3 mb-3 border-start ">
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li><span class="fw-bold">Cuenta:</span> {{ cabecera.IdCuentaAtencion }}</li>
                                    <li><span class="fw-bold">Prioridad:</span> {{ 'PRIORIDAD IV' }}</li>
                                    <li><span class="fw-bold">Motivo:</span> {{ 'ACCIDENTE LABORAL OTROS' }}</li>
                                    <li><span class="fw-bold">Fecha Triaje:</span> {{ '17/07/2025 - 10:08 am' }}
                                    </li>
                                    <li><span class="fw-bold">Fecha Atención:</span> {{ '17/07/2025 - 10:08 am' }}
                                    </li>
                                </ul>
                            </div>


                            <div class="col-md-6 border-start">
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li>
                                        <span class="fw-bold">Lugar de Nacimiento:</span>
                                        {{ 'LUGAR DE NACIMIENTO DEL PACIENTE' }}
                                    </li>
                                    <li><span class="fw-bold">
                                            Dirección Actual:</span> {{ cabecera.Direccion || 'No registrado' }}
                                    </li>
                                    <li>
                                        <span class="fw-bold text-">DATOS DE ACOMPAÑANTE / RESPONSABLE:
                                            {{ 'DATOS DE ACOMPAÑANTE' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </TabPanel>
        </TabPanels>
    </Tabs>
</template>
