<script setup>
import { ref } from 'vue'
import Dialog from 'primevue/dialog'
import BaseCombo from "@/components/WSelect/WSelect.vue";
import { useAppStore } from '@/stores/useAppStore'

const appStore = useAppStore()

const visible = ref(false)
const motivoSeleccionado = ref(null)
let resolver = null // Para resolver la promesa desde fuera

// Función para mostrar el diálogo y devolver la descripción seleccionada
const mostrar = () => {
    visible.value = true
    return new Promise((resolve) => {
        resolver = resolve
    })
}

// Confirmar selección y devolver solo la descripción del motivo
const confirmar = () => {
    if (motivoSeleccionado.value) {
        visible.value = false

        // Buscar la descripción en la lista original
        const item = appStore.citaAnuladaMotivo.find(opcion => opcion.value === motivoSeleccionado.value)
        const descripcion = item?.label || null

        resolver?.(descripcion)
    }
}


// Cancelar: usuario cerró sin seleccionar
const cancelar = () => {
    visible.value = false
    resolver?.(null)
}

defineExpose({ mostrar })
</script>

<template>
    <Dialog v-model:visible="visible" modal header="MOTIVO DE ANULACIÓN" :style="{ width: '600px' }" @hide="cancelar">
        <div class="col-md-12">
            <BaseCombo v-model="motivoSeleccionado" :options="appStore.citaAnuladaMotivo" label="Motivo de anulación"
                :filter="true"/>
        </div>

        <div class="text-end">
            <button class="btn btn-primary" @click="confirmar" :disabled="!motivoSeleccionado">
                <i class="pi pi-check"></i> Confirmar
            </button>
        </div>
    </Dialog>
</template>
