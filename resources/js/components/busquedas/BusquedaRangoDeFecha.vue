<script setup>
import {ref, reactive} from 'vue'
import Dialog from 'primevue/dialog'
import BaseDatePicker from '@/components/BaseDatePicker.vue'
import WButton from "@/components/WButton/WButton.vue";

const emit = defineEmits(['enviarDatos', 'cerrado'])

const visible = ref(false)
const cerradoPorBoton = ref(false)

const form = reactive({
    FechaDesde: '',
    FechaHasta: ''
})

const openDialog = () => {
    form.FechaDesde = ''
    form.FechaHasta = ''
    visible.value = true
}

const closeDialog = () => {
    visible.value = false
}

const emitirDatos = async () => {
    if (!form.FechaDesde) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'SE HA DETECTADO QUE NO HA SELECCIONADO UNA FECHA INICIAL, ESTE CAMPO ES REQUERIDO PARA LA BÚSQUEDA NECESARIA.',
            'warning', false, true
        )
    }
    if (!form.FechaHasta) {
        return showAlert(
            'VALIDACIÓN DE CAMPO REALIZADO',
            'SE HA DETECTADO QUE NO HA SELECCIONADO UNA FECHA FINAL, ESTE CAMPO ES REQUERIDO PARA LA BÚSQUEDA NECESARIA.',
            'warning', false, true
        )
    }

    emit('enviarDatos', {
        FechaDesde: form.FechaDesde,
        FechaHasta: form.FechaHasta
    })

    closeDialog()
}

const onClose = () => {
    if (!cerradoPorBoton.value) {
        emit('cerrado')
    }

    cerradoPorBoton.value = false
    form.FechaDesde = ''
    form.FechaHasta = ''
}

defineExpose({openDialog, closeDialog})
</script>

<template>
    <Dialog v-model:visible="visible" modal header="BÚSQUEDA POR FECHAS" :style="{ width: '500px' }"
            @hide="onClose">
        <div class="row g-3">
            <div class="col-md-12">
                <BaseDatePicker v-model="form.FechaDesde" label="Fecha Inicial" placeholder="Selecciona la fecha"/>
            </div>
            <div class="col-md-12">
                <BaseDatePicker v-model="form.FechaHasta" label="Fecha Final" placeholder="Selecciona la fecha"/>
            </div>
        </div>

        <div class="mt-4 text-end">
            <w-button type-button="primary"
                      label="Descargar Exportación"
                      icon="download"
                      @click="emitirDatos"/>
        </div>
    </Dialog>
</template>
