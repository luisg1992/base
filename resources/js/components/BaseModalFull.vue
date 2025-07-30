<template>
    <Dialog
        v-model:visible="internalVisible"
        modal
        position="right"
        @show="handleOpen"
        @hide="closeModal"
        :header="computedTitle"
        :style="{ width: dialogWidth, height: '100vh' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        :closable="false"
        class="x-modal-full">
        <slot/>
    </Dialog>
</template>

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
        default: 'modal-lg'
    },
    title: {
        type: String,
        default: ''
    }
})

// Emit
const emit = defineEmits(['close', 'open'])

const internalVisible = ref(props.isVisible)

watch(() => props.isVisible, (val) => {
    internalVisible.value = val
})

// Cerrar modal
const closeModal = () => {
    emit('close')
}

// Evento al abrir modal
const handleOpen = () => {
    emit('open')
}

// Determinar título del modal
const computedTitle = computed(() => {
    if (props.title) return props.title
    if (props.viewRecord && props.recordId) return 'VER REGISTRO'
    if (!props.viewRecord && props.recordId) return 'EDITAR REGISTRO'
    return 'NUEVO REGISTRO'
})

// Tamaño del modal
const dialogWidth = computed(() => {
    switch (props.size) {
        case 'modal-sm':
            return '30vw'
        case 'modal-md':
            return '40vw'
        case 'modal-lg':
            return '60vw'
        case 'modal-xl':
            return '75vw'
        default:
            return '50vw'
    }
})
</script>
