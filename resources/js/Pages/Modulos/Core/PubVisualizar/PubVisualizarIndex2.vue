<script setup>
import {computed, onMounted, ref} from "vue";
import Carousel from 'primevue/carousel'
import axios from "axios";

let configuracion = ref(null);
let recordsVertical = ref([]);
let recordsHorizontal = ref([]);
let products = ref([]);

const colors = ['red', 'blue', 'green', 'orange'];

products.value = Array.from({length: 4}, () => ({
    Descripcion: 'Product Description',
    TamanoLetra: '36px',
    FondoColor: colors[Math.floor(Math.random() * colors.length)]
}));

const carruselHeight = computed(() =>
    configuracion.value.contenidoInferior ? 'calc(100vh - 200px)' : '100vh'
);

const videoSrc = ref('/videos/video.mp4')
const videoRef = ref(null)

onMounted(async () => {
    recordsVertical.value = [];
    recordsHorizontal.value = [];
    const {data} = await axios.get(`/core/pub/configuracion`)
    configuracion.value = data.configuracion;
    recordsVertical.value = data.recordsVertical;
    recordsHorizontal.value = data.recordsHorizontal;
})

</script>

<template>
    <div class="tv-safe-area" v-if="configuracion">
<!--    <div style="height: 100vh; width: 100%; display: flex; flex-direction: column;" v-if="configuracion">-->
        <!-- Contenedor superior -->
        <div style="flex: 1; display: flex;">
            <div class="col-auto" v-if="configuracion.imagenPublicidad">
                <div style="width: 500px;">
                    <!-- Contenido de imagenPublicidad -->
                </div>
            </div>
            <div class="col" style="flex: 1;">
                <Carousel :value="recordsVertical"
                          :numVisible="configuracion.cantidadPorPantalla"
                          :numScroll="1"
                          orientation="vertical"
                          :verticalViewPortHeight="carruselHeight"
                          containerClass="flex items-center"
                          circular
                          :show-indicators="false"
                          :show-navigators="false"
                          :autoplayInterval="configuracion.tiempoPorPantalla * 500">
                    <template #item="slotProps">
                        <div style="padding: 8px; height: 100%">
                            <div style="display: flex; height: 100%; width: 100%;">
                                <div class="col-auto"
                                     v-if="slotProps.data.Titulo">
                                    <div style="height: 100%;
                                        border-radius: 16px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        font-size: 24px;
                                        font-weight: bold;
                                        padding: 0 16px;
                                        margin-right: 8px;
                                        width: 500px"
                                         :style="{'background-color': `#${slotProps.data.ColorFondo}`,
                                                 'color': `#${slotProps.data.ColorLetra}`,
                                                 'font-size': `${slotProps.data.TamanoLetra}px`}">
                                        <div style="display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    line-height: 1.1;">
                                            {{ slotProps.data.Titulo }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div style="height: 100%;
                                        border-radius: 16px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        font-size: 24px;
                                        padding: 0 16px"
                                         :style="{'background-color': `#${slotProps.data.ColorFondo}`,
                                          'color': `#${slotProps.data.ColorLetra}`,
                                          'font-size': `${slotProps.data.TamanoLetra}px`}">
                                        <div style="display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    line-height: 1.1;">
                                            {{ slotProps.data.Descripcion }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </Carousel>
            </div>
        </div>

        <!-- Contenedor inferior de altura fija -->
        <div v-if="configuracion.contenidoInferior" style="height: 200px; padding: 8px">

            <div style="display: flex; height: 100%; width: 100%;">
                <div class="col-auto">
                    <video ref="videoRef"
                           width="320"
                           height="180"
                           controls
                           autoplay
                           loop
                           muted
                           :src="videoSrc">
                        Tu navegador no soporta el elemento de video.
                    </video>
                </div>
                <div class="col" style="margin-left: 8px; height: 100%;">
                    <Carousel :value="recordsHorizontal"
                              :numVisible="1"
                              :numScroll="1"
                              :verticalViewPortHeight="carruselHeight"
                              containerClass="flex items-center"
                              circular
                              :show-indicators="false"
                              :show-navigators="false"
                              :autoplayInterval="configuracion.tiempoPorPantallaHorizontal * 1000">
                        <template #item="slotProps">
                            <div style="height: 180px;
                                            border-radius: 16px;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-size: 24px;
                                            padding: 0 16px"
                                 :style="{'background-color': `#${slotProps.data.ColorFondo}`,
                                              'color': `#${slotProps.data.ColorLetra}`,
                                              'font-size': `${slotProps.data.TamanoLetra}px`}">
                                <div
                                    style="display: flex; align-items: center; justify-content: center; text-align: center;">
                                    {{ slotProps.data.Descripcion }}
                                </div>
                            </div>
                        </template>
                    </Carousel>
                </div>
            </div>
        </div>
    </div>

    <!--        <div style="height: 100%; width: 100%; display:flex" v-if="configuracion">-->
    <!--            <div class="col-auto" v-if="configuracion.imagenPublicidad">-->
    <!--                <div style="width:500px">-->

    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="col">-->
    <!--            </div>-->
    <!--        </div>-->
</template>
