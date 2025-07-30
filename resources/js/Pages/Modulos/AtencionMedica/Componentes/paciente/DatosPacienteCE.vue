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
            <Tab value="0" style="padding:0px !important">
                        <h5 class="fw-bold text-primary">
                            {{ cabecera.Paciente }} - {{ cabecera.Doc_Identidad }}
                        </h5>
                    </Tab>
        </TabList>

        <TabPanels>
            <TabPanel value="0">
                <div class="d-flex bg-white">
                    <!-- Foto -->
                    <img :src="fotoPaciente" :key="fotoPaciente" alt="Foto paciente" class="img-thumbnail shadow-sm"
                        style="width: 108px; height: 108px; object-fit: cover;" />

                    <!-- Información -->
                    <div class="ms-4 flex-grow-1 w-100">

                        <div class="row fs-6" style="font-size: 12px !important; text-transform: uppercase;">
                            <!-- Columna 1 -->
                            <div class="col-md-3 mb-3">
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li><span class="fw-bold">DOCUMENTO:</span> {{ cabecera.Doc_Identidad }}</li>
                                    <li><span class="fw-bold">HISTORIA CLÍNICA:</span> {{ cabecera.HistoriaClinica }}
                                    </li>
                                    <li><span class="fw-bold">SEXO:</span> {{ cabecera.Sexo }}</li>
                                    <li><span class="fw-bold">EDAD:</span> {{ cabecera.Edad }}</li>
                                    <li>
                                        <span class="fw-bold">F.NACIMIENTO:</span> {{ cabecera.FechaNacimiento }}
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
                                    <li><span class="fw-bold">
                                            Dirección:</span> {{ cabecera.Direccion || 'No registrado' }}</li>
                                    <li><span class="fw-bold">Estado:</span> {{ cabecera.EstadosColaCitas }}</li>
                                    <li>
                                        <span class="fw-bold">IDIOMA:</span> {{ 'IDIOMA' }}
                                    </li>
                                    <li>
                                        <span class="fw-bold">G.INSTRUCCIÓN:</span> {{ 'GRADO DE INSTRUCCIÓN' }}
                                    </li>
                                    <li>
                                        <span class="fw-bold">ESTADO CIVIL:</span> {{ 'ESTADO CIVIL' }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Columna 3 -->
                            <div class="col-md-3 mb-3 border-start ">
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li><span class="fw-bold">Origen:</span> {{ cabecera.OrigenCita ?? 'GALENOS' }}</li>
                                    <li><span class="fw-bold">Cuenta:</span> {{ cabecera.IdCuentaAtencion }}</li>
                                    <li><span class="fw-bold">Financiamiento:</span> {{ cabecera.FuenteFinanciamiento }}
                                    </li>
                                    <li><span class="fw-bold">Servicio:</span> {{ cabecera.Servicio }}</li>
                                    <li><span class="fw-bold">Teléfono:</span> {{ cabecera.Telefono }}</li>
                                    <li><span class="fw-bold">Correo:</span> {{ cabecera.Correo || 'No registrado' }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Columna 4 (Botones de acciones clínicas) -->
                            <div class="col-md-3 mb-3 border-start ">
                                <div class="row g-2">
                                    <div class="col-6 d-grid">
                                        <button class="btn btn-outline-warning w-100">
                                            <i class="bi bi-send me-1"></i> Ver Origen
                                        </button>
                                        <button class="btn btn-outline-primary w-100 mt-2">
                                            <i class="bi bi-folder2-open me-1"></i> Atenciones
                                        </button>
                                    </div>

                                    <div class="col-6 d-grid">
                                        <button class="btn btn-outline-danger w-100">
                                            <i class="bi bi-arrow-repeat me-1"></i> Referencias
                                        </button>
                                        <button class="btn btn-outline-info w-100 mt-2">
                                            <i class="bi bi-clipboard2-pulse me-1"></i> Interconsultas
                                        </button>
                                    </div>
                                </div> 
                            </div>  
                        </div>
                    </div>
                </div>
            </TabPanel>
        </TabPanels>
    </Tabs>
</template>
