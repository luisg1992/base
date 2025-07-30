<script setup>
import {computed, ref, watch} from 'vue'
import Dialog from 'primevue/dialog'

// Props
const props = defineProps({
    isVisible: Boolean,
    recordId: {
        type: [String, Number, null],
        default: null
    },
    viewRecord: {
        type: Boolean,
        default: false
    },
    isSaving: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'modal-lg',
        validator: (value) =>
            ['modal-xs', 'modal-sm', 'modal-md', 'modal-lg', 'modal-xl', 'modal-xxl', 'fullscreen', 'modal-full'].includes(value)
    },
    header: {
        type: String,
        default: ''
    },
    loading: {
        type: Boolean,
        default: false
    },
})

// Emits
const emit = defineEmits(['close', 'submit', 'open'])

// Estado interno
const internalVisible = ref(props.isVisible)

watch(() => props.isVisible, (val) => {
    internalVisible.value = val
})

// Funciones
const closeModal = () => emit('close', true)
const handleOpen = () => emit('open')

// ¿Fullscreen?
const fullscreen = computed(() => props.size === 'fullscreen')

// Estilo principal fullscreen
const fullscreenStyle = {
    width: '100vw',
    height: '100vh',
    maxWidth: '100vw',
    maxHeight: '100vh',
    margin: '0',
    padding: '0',
    borderRadius: '0'
}

// Estilo interno del contenido para ocupar todo el alto sin márgenes
const fullscreenContentStyle = {
    height: 'calc(100vh - 4rem)', // ajusta según altura del header
    overflow: 'auto',
    padding: '1rem'
}

// Título dinámico
const computedHeader = computed(() => {
    if (props.header) return props.header
    if (props.viewRecord && props.recordId) return 'VER REGISTRO'
    if (!props.viewRecord && props.recordId) return 'EDITAR REGISTRO'
    return 'NUEVO REGISTRO'
})

// Tamaños normales
const dialogWidth = computed(() => {
  switch (props.size) {
    case 'modal-xs':
      return '20vw'
    case 'modal-sm':
      return '30vw'
    case 'modal-md':
      return '40vw'
    case 'modal-lg':
      return '60vw'
    case 'modal-xl':
      return '75vw'
    case 'modal-xxl':
      return '95vw'
    case 'modal-full':
      return '100vw'
    default:
      return '50vw'
  }
})
</script>

<template>
    <Dialog v-model:visible="internalVisible"
            modal
            @show="handleOpen"
            @hide="closeModal"
            :header="computedHeader"
            :style="fullscreen ? fullscreenStyle : { width: dialogWidth }"
            :breakpoints="fullscreen ? {} : { '1199px': '75vw', '575px': '90vw' }"
            :contentStyle="fullscreen ? fullscreenContentStyle : {}"
            :draggable="!fullscreen">
        <ModalLoader v-if="loading" />
        <slot/>
    </Dialog>
</template>
