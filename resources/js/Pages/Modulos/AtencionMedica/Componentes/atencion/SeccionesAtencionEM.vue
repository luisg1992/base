<script setup>
import { computed } from 'vue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

import Anamnesis from './secciones/Anamnesis/Anamnesis.vue';
import FuncionesVitales from './secciones/FuncionesVitales/FuncionesVitales.vue';
import ExamenClinico from './secciones/ExamenClinico/ExamenClinico.vue';
import Diagnostico from './secciones/Diagnostico/Diagnostico.vue';
import FarmaciaEM from './secciones/FarmaciaEM/FarmaciaEM.vue';
import PlanTrabajoEM from './secciones/PlanTrabajoEM/PlanTrabajoEM.vue';
import Procedimientos from './secciones/Diagnostico/Procedimientos.vue';
import ReevaluacionPlanTrabajo from './secciones/ReevaluacionPlanTrabajo/ReevaluacionPlanTrabajo.vue';
import AltaMedicaEM from './secciones/AltaMedicaEM/AltaMedicaEM.vue';

const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
});
let isViewMode = computed(() => props.viewRecord)
/*
IdTipoServicio = 1 - CONSULTA EXTERNA
IdTipoServicio = 2 - EMERGENCIA
IdTipoServicio = 3 - HOSPITALIZACIÓN
*/
</script>
<template>
    <Tabs value="0">
        <TabList>
            <Tab value="0"><b class="text-primary">Anamanesis Enfermedad Actual</b></Tab>
            <Tab value="1"><b class="text-primary">Funciones Vitales</b></Tab>
            <Tab value="2"><b class="text-primary">Exámen Clínico</b></Tab>
            <Tab value="3"><b class="text-primary">Diagnósticos</b></Tab>
            <Tab value="4"><b class="text-primary">Tratamiento Inmediato</b></Tab>
            <Tab value="5"><b class="text-primary">Plan de Trabajo Médico</b></Tab>
            <Tab value="6"><b class="text-primary">Reevaluación Plan de Trabajo</b></Tab>
            <Tab value="7"><b class="text-primary">Reevaluación Alta Médica</b></Tab>
        </TabList>

        <TabPanels>
            <TabPanel value="0">
                <Anamnesis :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </TabPanel>

            <TabPanel value="1">
                <FuncionesVitales :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </TabPanel>

            <TabPanel value="2">
                <ExamenClinico :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </TabPanel>

            <TabPanel value="3">
                <Diagnostico :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </TabPanel>

            <TabPanel value="4">
                <FarmaciaEM :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </TabPanel>

            <TabPanel value="5">
                <PlanTrabajoEM :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </TabPanel>

            <TabPanel value="6">
                <ReevaluacionPlanTrabajo :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
                <br>   
                <Diagnostico :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
                <br>   
            </TabPanel>

            <TabPanel value="7">
                <AltaMedicaEM :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </TabPanel>
        </TabPanels>
    </Tabs>
</template>
