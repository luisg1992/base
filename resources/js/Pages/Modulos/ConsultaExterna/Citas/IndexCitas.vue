<script setup>

import { onMounted, ref } from 'vue';
import axios from 'axios';

import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

import GestionarCitas from './secciones/GestionarCitas.vue'
import IndexCitaControl from './secciones/Historiales/IndexCitaControl.vue';
import IndexImprimirFuas from './secciones/Historiales/IndexImprimirFuas.vue';
import IndexHistorialCitas from './secciones/Historiales/IndexHistorialCitas.vue'
import IndexCitasInterconsulta from './secciones/Historiales/IndexCitasInterconsulta.vue';
import IndexReferenciasAceptadas from './secciones/Historiales/IndexReferenciasAceptadas.vue';
import IndexCitasDemandaInsatisfecha from './secciones/Historiales/IndexCitasDemandaInsatisfecha.vue';

let moduloActual = ref();

const obtenerModuloActual = async () => {
    await axios.post('/core/modulos/obtener-modulo-actual', {
        path: window.location.pathname.substring(1),
    })
        .then(response => {
            moduloActual.value = response.data;
        })
        .catch(error => {
            console.log(error);
        });
}

onMounted(() => {
    obtenerModuloActual()
})

</script>

<template>
    <div class="card">
        <div class="card-body">
            <Tabs value="0">
                <TabList>
                    <Tab v-if="$can(`${moduloActual}.tab.gestionar.citas`)" value="0">GESTIONAR CITAS</Tab>
                    <Tab v-if="$can(`${moduloActual}.tab.citas.interconsulta`)" value="1">CITAS INTERCONSULTA</Tab>
                    <Tab v-if="$can(`${moduloActual}.tab.citas.control`)" value="2">CITAS CONTROL</Tab>
                    <Tab v-if="$can(`${moduloActual}.tab.citas.referencia`)" value="3">CITAS REFERENCIA</Tab>
                    <Tab v-if="$can(`${moduloActual}.tab.citas.historial`)" value="4">HISTORIAL DE CITAS</Tab>
                    <Tab v-if="$can(`${moduloActual}.tab.imprimir.fuas`)" value="5">IMPRESIÓN FUAS</Tab>
                    <Tab v-if="$can(`${moduloActual}.tab.demanda.insatisfecha`)" value="6">DEMANDA INSATISFECHA</Tab>
                </TabList>

                <TabPanels>
                    <TabPanel v-if="$can(`${moduloActual}.tab.gestionar.citas`)" value="0">
                        <GestionarCitas :moduloActual="moduloActual" />
                    </TabPanel>
                    <TabPanel v-if="$can(`${moduloActual}.tab.citas.interconsulta`)" value="1">
                        <IndexCitasInterconsulta :moduloActual="moduloActual" />
                    </TabPanel>
                    <TabPanel v-if="$can(`${moduloActual}.tab.citas.control`)" value="2">
                        <IndexCitaControl :moduloActual="moduloActual" />
                    </TabPanel>
                    <TabPanel v-if="$can(`${moduloActual}.tab.citas.referencia`)" value="3">
                        <IndexReferenciasAceptadas :moduloActual="moduloActual" />
                    </TabPanel>
                    <TabPanel v-if="$can(`${moduloActual}.tab.citas.historial`)" value="4">
                        <IndexHistorialCitas :moduloActual="moduloActual" />
                    </TabPanel>
                    <TabPanel v-if="$can(`${moduloActual}.tab.imprimir.fuas`)" value="5">
                        <IndexImprimirFuas />
                    </TabPanel>
                    <TabPanel v-if="$can(`${moduloActual}.tab.demanda.insatisfecha`)" value="6">
                        <IndexCitasDemandaInsatisfecha :moduloActual="moduloActual" />
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </div>
</template>
