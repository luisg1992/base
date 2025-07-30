<script setup>
import { computed } from 'vue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

import Ordenes from './Ordenes/Ordenes.vue';
import Interconsultas from './Interconsultas/Interconsultas.vue';
import Procedimientos from './Procedimientos/Procedimientos.vue';
import FarmaciaCE from './FarmaciaCE/FarmaciaCE.vue';

const props = defineProps({
    cabecera: {
        type: Object,
        required: true
    },
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
    interconsultas: {
        type: Array,
        required: true
    },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean,
    usoCatalogoDX: Boolean
});
let isViewMode = computed(() => props.viewRecord)
let usoCatalogoDX = computed(() => props.usoCatalogoDX)
</script>

<template>
    <Tabs value="0">
        <TabList>
            <Tab value="0"><b class="text-primary">1.Farmacia</b></Tab>
            <Tab value="1"><b class="text-primary">2.Apoyo al Diagnóstico</b></Tab>
            <Tab value="2"><b class="text-primary">3.Interconsultas</b></Tab>
            <!-- <Tab value="3"><b class="text-primary">Procedimientos</b></Tab> -->
        </TabList>

        <TabPanels>
            <TabPanel value="0">
                <FarmaciaCE :cabecera="cabecera" :productos_farmacia="productos_farmacia" :diagnosticos="diagnosticos"
                    :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </TabPanel>

            <TabPanel value="1">
                <Ordenes :cabecera="cabecera" :diagnosticos="diagnosticos" :productos_apoyo_dx="productos_apoyo_dx"
                    :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </TabPanel>

            <TabPanel value="2">
                <Interconsultas :cabecera="cabecera" :diagnosticos="diagnosticos" :viewRecord="isViewMode"
                    :IdTipoServicio="IdTipoServicio" :usoCatalogoDX="usoCatalogoDX" :interconsultas="interconsultas"/>
            </TabPanel>

            <!-- <TabPanel value="3">
                <Procedimientos :cabecera="cabecera" :diagnosticos="diagnosticos" :viewRecord="isViewMode"
                    :IdTipoServicio="IdTipoServicio" />
            </TabPanel> -->
        </TabPanels>
    </Tabs>
</template>
