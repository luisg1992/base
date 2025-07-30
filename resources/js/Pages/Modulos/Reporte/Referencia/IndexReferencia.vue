<script setup>

import {onMounted, ref} from 'vue';
import axios from 'axios';

import AppLayout from '@/Layouts/AppLayout.vue';

import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
 
import IndexReporteReferencias from './secciones/Historiales/IndexReporteReferencias.vue';

let moduloActual = ref();

const obtenerModuloActual = async() => {
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
                        <Tab v-if="$can(`${moduloActual}.tab.filtro.de.referencias`)" value="0">REFERENCIAS</Tab>
                    </TabList>

                    <TabPanels>
                        <TabPanel v-if="$can(`${moduloActual}.tab.filtro.de.referencias`)" value="0">
                            <IndexReporteReferencias :moduloActual="moduloActual" />
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
        </div>
</template>
