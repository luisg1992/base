<script setup>
import {computed, onMounted, ref} from "vue";
// import Carousel from 'primevue/carousel'
import 'vue3-carousel/carousel.css'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'
import axios from "axios";

let configuracion = ref(null);
let recordsVertical = ref([]);
let recordsHorizontal = ref([]);
let products = ref([]);
let carouselConfig = ref({});
let carouselConfigHorizontal = ref({});

const colors = ['red', 'blue', 'green', 'orange'];

products.value = Array.from({length: 4}, () => ({
    Descripcion: 'Product Description',
    TamanoLetra: '36px',
    FondoColor: colors[Math.floor(Math.random() * colors.length)]
}));

// const carruselHeight = computed(() =>
//     configuracion.value.contenidoInferior ? 'calc(100vh - 200px)' : '100vh'
// );

const videoSrc = ref('/videos/video.mp4')
const videoRef = ref(null)

onMounted(async () => {
    recordsVertical.value = [];
    recordsHorizontal.value = [];
    const {data} = await axios.get(`/core/pub/configuracion`)
    configuracion.value = data.configuracion;
    recordsVertical.value = data.recordsVertical;
    recordsHorizontal.value = data.recordsHorizontal;

    carouselConfig.value = {
        itemsToShow: 4,
        wrapAround: true,
        autoplay: 1,
        transition: Number(configuracion.value.tiempoPorPantalla) * 1000,
        dir: 'ttb',
        height: '100%',
        snapAlign: 'start',
        gap: 12,
    }

    carouselConfigHorizontal.value = {
        itemsToShow: 1,
        wrapAround: true,
        autoplay: 1,
        transition: Number(configuracion.value.tiempoPorPantalla) * 1000,
        height: '100%',
        snapAlign: 'start',
        gap: 12,
    }
})

// const carouselConfig = {
//     itemsToShow: 4,
//     wrapAround: true,
//     // autoplay: 1,
//     transition: 3000,
//     dir: 'ttb',
//     height: '100%',
//     snapAlign: 'start',
//     gap: 12,
// }

</script>

<template>
    <div class="tv-safe-area">
        <div class="flex-col-layout" v-if="configuracion">
            <div class="fila-superior">
                <Carousel v-bind="carouselConfig">
                    <Slide v-for="(row, index) in recordsVertical" :key="index">
                        <div style="border: 0 solid #000; height: 100%;
                                    border-radius: 16px;
                                    width: 100%; display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 32px;
                                    padding: 0 16px"
                             :style="{'background-color': `#${row.ColorFondo}`, 'color': `#${row.ColorLetra}`}">
                            {{ row.Descripcion }}
                        </div>
                    </Slide>
                </Carousel>

<!--                <Carousel :value="recordsVertical"-->
<!--                          :numVisible="configuracion.cantidadPorPantalla"-->
<!--                          :numScroll="1"-->
<!--                          orientation="vertical"-->
<!--                          :verticalViewPortHeight="carruselHeight"-->
<!--                          containerClass="flex items-center"-->
<!--                          circular-->
<!--                          style="height: 100%; width: 100%;"-->
<!--                          :show-indicators="false"-->
<!--                          :show-navigators="false"-->
<!--                          :autoplayInterval="configuracion.tiempoPorPantalla * 500">-->
<!--                    <template #item="slotProps">-->
<!--                        <div style="height: 100%; width: 100%; padding: 8px 0;">-->
<!--                            <div style="height: 100%;-->
<!--                                        border-radius: 16px;-->
<!--                                        display: flex;-->
<!--                                        align-items: center;-->
<!--                                        justify-content: center;-->
<!--                                        padding: 16px 4px"-->
<!--                                 :style="{'background-color': `#${slotProps.data.ColorFondo}`,-->
<!--                                          'color': `#${slotProps.data.ColorLetra}`,-->
<!--                                          'font-size': `36px`}">-->
<!--                                <div style="display: flex;-->
<!--                                                    align-items: center;-->
<!--                                                    justify-content: center;-->
<!--                                                    line-height: 1.1;">-->
<!--                                    {{ slotProps.data.Descripcion }}-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </template>-->
<!--                </Carousel>-->
            </div>
            <div class="fila-inferior" v-if="configuracion.contenidoInferior">
                <!-- Columna fija solo si existe -->
                <div class="col-fija">
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
                <div class="col-flex">
                    <Carousel v-bind="carouselConfigHorizontal">
                        <Slide v-for="(row, index) in recordsHorizontal" :key="index">
                            <div style="border: 0 solid #000; height: 100%;
                                    border-radius: 16px;
                                    width: 100%;
                                     display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 32px;
                                    padding: 0 16px"
                                 :style="{'background-color': `#${row.ColorFondo}`, 'color': `#${row.ColorLetra}`}">
                                {{ row.Descripcion }}
                            </div>
                        </Slide>
                    </Carousel>
                </div>
            </div>
        </div>
    </div>
</template>
