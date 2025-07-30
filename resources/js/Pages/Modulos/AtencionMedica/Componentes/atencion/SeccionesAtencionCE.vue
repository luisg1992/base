<script setup>
import { ref, computed, watch } from 'vue'

// PrimeVue Tabs 
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'

// Secciones
import Anamnesis from './secciones/Anamnesis/Anamnesis.vue'
import Diagnostico from './secciones/Diagnostico/Diagnostico.vue'
import Procedimientos from './secciones/Diagnostico/Procedimientos.vue'
import SeccionApoyoDX from './secciones/ApoyoDX/SeccionApoyoDX.vue'
import PlanTrabajo from './secciones/PlanTrabajo/PlanTrabajo.vue'
import DestinoAtencion from './secciones/DestinoAtencion/DestinoAtencion.vue'

// Props
const props = defineProps({
    cabecera: Object,
    diagnosticos: {
        type: Array,
        required: true
    },
    productos_farmacia: {
        type: Array,
        required: true
    },
    productos_apoyo_dx: {
        type: Array,
        required: true
    },
    procedimientos_cpt: {
        type: Array,
        required: true
    },
    interconsultas: {
        type: Array,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean,
    usoCatalogoDX: Boolean
})

const isViewMode = computed(() => props.viewRecord)
const usoCatalogoDX = computed(() => props.usoCatalogoDX)
const activeTab = ref(0)
const anamnesisRef = ref(null)
const planTrabajoRef = ref(null)

// Guardar Anamnesis si se cambia de tab 
watch(activeTab, async (newVal, oldVal) => {
    // Guardar Anamnesis
    if (oldVal === 0 && anamnesisRef.value?.hasChanges) {
        const result = await anamnesisRef.value.actualizarAnamnesis();
        if (result?.success && result.data) {
            Object.assign(props.cabecera, result.data);
        }
    }

    // Guardar Plan de Trabajo
    if (oldVal === 3 && planTrabajoRef.value?.hasChanges) {
        const result = await planTrabajoRef.value.actualizarAnamnesis();
        if (result?.success && result.data) {
            Object.assign(props.cabecera, result.data);
        }
    }
});

</script>

<template>
    <TabView v-model:activeIndex="activeTab">
        <TabPanel header="1.Anamnesis">
            <Anamnesis ref="anamnesisRef" :cabecera="cabecera" :viewRecord="isViewMode"
                :IdTipoServicio="IdTipoServicio" />
        </TabPanel>

        <TabPanel header="2.Diagnósticos">
            <Diagnostico :cabecera="cabecera" :diagnosticos="diagnosticos" :viewRecord="isViewMode"
                :IdTipoServicio="IdTipoServicio" />
            <Procedimientos :cabecera="cabecera" :diagnosticos="diagnosticos" :viewRecord="isViewMode"
                :IdTipoServicio="IdTipoServicio" :procedimientos_cpt="procedimientos_cpt" />
        </TabPanel>

        <TabPanel header="3.Órdenes Médicas">
            <SeccionApoyoDX :cabecera="cabecera" :productos_farmacia="productos_farmacia"
                :interconsultas="interconsultas" :productos_apoyo_dx="productos_apoyo_dx" :diagnosticos="diagnosticos"
                :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" :usoCatalogoDX="usoCatalogoDX" />
        </TabPanel>

        <TabPanel header="4.Plan de Trabajo">
            <PlanTrabajo ref="planTrabajoRef" :cabecera="cabecera" :viewRecord="isViewMode"
                :IdTipoServicio="IdTipoServicio" />
        </TabPanel>

        <TabPanel header="5.Destino Atención">
            <DestinoAtencion :cabecera="cabecera" :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
        </TabPanel>
    </TabView>
</template>
