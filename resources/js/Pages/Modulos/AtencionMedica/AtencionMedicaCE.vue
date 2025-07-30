<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'
import DatosPacienteCE from './Componentes/paciente/DatosPacienteCE.vue'
import UltimoTriaje from './Componentes/triaje/UltimoTriaje.vue'
import SeccionesAtencionCE from './Componentes/atencion/SeccionesAtencionCE.vue'

import { useDiagnosticoStore } from '@/stores/useDiagnosticoStore'
import { useFarmaciaStore } from '@/stores/useFarmaciaStore'
import { useOrdenStore } from '@/stores/useOrdenStore'
import { useProcedimientoStore } from '@/stores/useProcedimientoStore'
import { useCabeceraStore } from '@/stores/useCabeceraStore'
import { useInterconsultaStore } from '@/stores/useInterconsultaStore' 

const diagnosticos = ref([])
const productosFarmacia = ref([])
const productosApoyoDX = ref([])
const procedimientosCPT = ref([])
const interconsultas = ref([])

const props = defineProps({
    recordId: [Number, String],
    IdTipoServicio: [Number, String],
    viewRecord: Boolean,
    usoCatalogoDX: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])
const usoCatalogoDX = computed(() => props.usoCatalogoDX)

const diagnosticoStore = useDiagnosticoStore()
const farmaciaStore = useFarmaciaStore()
const ordenesStore = useOrdenStore()
const procedimientoStore = useProcedimientoStore()
const cabeceraStore = useCabeceraStore()
const interconsultaStore = useInterconsultaStore() // ✅ NUEVO

const isDialogOpen = ref(false)
const loadingSubmit = ref(false)
const isModalLoading = ref(false)
const showModal = ref(false)

const isViewMode = computed(() => props.viewRecord)

const handleOpen = async () => {
    if (props.recordId) {
        try {
            isModalLoading.value = true
            showModal.value = false

            const { data } = await axios.get(`/${props.resource}/record/${props.recordId}`)

            if (data.success) {
                cabeceraStore.setCabecera(data.data)

                diagnosticos.value = data.diagnosticos
                procedimientosCPT.value = data.procedimientos_cpt
                productosFarmacia.value = data.productos_farmacia
                productosApoyoDX.value = data.productos_apoyo_dx
                interconsultas.value = data.interconsultas

                diagnosticoStore.setDiagnosticos(data.diagnosticos)
                procedimientoStore.setProcedimiento(data.procedimientos_cpt)
                farmaciaStore.setFarmacia(data.productos_farmacia)
                ordenesStore.setOrden(data.productos_apoyo_dx)
                interconsultaStore.setInterconsultas(data.interconsultas) 
            }
        } catch (error) {
            if (error.response?.status === 403) {
                closeDialog()
            } else {
                console.error("Error cargando datos:", error)
            }
        } finally {
            showModal.value = true
            isModalLoading.value = false
        }
    } else {
        showModal.value = true
    }
}

const onSubmit = async () => {
    // lógica de guardado general (si aplica)
}

const openDialog = () => {
    isDialogOpen.value = true
}

const closeDialog = () => {
    showModal.value = false
    isDialogOpen.value = false

    diagnosticoStore.setDiagnosticos([])
    procedimientoStore.limpiarProcedimiento()
    farmaciaStore.limpiarFarmacia()
    ordenesStore.limpiarOrden()
    cabeceraStore.limpiarCabecera()
    interconsultaStore.limpiarInterconsultas() 
}

defineExpose({ openDialog })
</script>


<template>
    <BaseModal :isVisible="isDialogOpen" :recordId="props.recordId" :viewRecord="isViewMode" :loading="isModalLoading"
        @close="closeDialog" @open="handleOpen" size="modal-xxl">
        <div class="row">
            <div class="col-12">
                <DatosPacienteCE v-if="cabeceraStore.cabecera" :cabecera="cabeceraStore.cabecera"
                    :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />

                <UltimoTriaje v-if="cabeceraStore.cabecera" :cabecera="cabeceraStore.cabecera" :viewRecord="isViewMode"
                    :IdTipoServicio="IdTipoServicio" />
            </div>

            <div class="col-12">
                <SeccionesAtencionCE v-if="cabeceraStore.cabecera" :cabecera="cabeceraStore.cabecera"
                    :interconsultas="interconsultas" :diagnosticos="diagnosticos"
                    :productos_farmacia="productosFarmacia" :usoCatalogoDX="usoCatalogoDX"
                    :productos_apoyo_dx="productosApoyoDX" :procedimientos_cpt="procedimientosCPT"
                    :viewRecord="isViewMode" :IdTipoServicio="IdTipoServicio" />
            </div>
        </div>
    </BaseModal>
</template>
