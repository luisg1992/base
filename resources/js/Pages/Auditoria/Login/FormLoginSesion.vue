<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import BaseModal from '@/components/BaseModal.vue'

const props = defineProps({
    recordId: Number,
    viewRecord: Boolean,
    resource: String
})

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false)
let loadingSubmit = ref(false)
let isModalLoading = ref(false)
let sesion = ref(null)

let isViewMode = computed(() => props.viewRecord)

const handleOpen = async () => {
    isModalLoading.value = true
    try {
        const response = await axios.get(`/${props.resource}/record/${props.recordId}`)
        sesion.value = response.data.data
    } catch (error) {
        console.error('Error al obtener detalles de la sesión:', error)
    } finally {
        isModalLoading.value = false
    }
}

const openDialog = () => {
    isDialogOpen.value = true
}

defineExpose({ openDialog })
</script>

<template>
    <BaseModal
        :isVisible="isDialogOpen"
        :recordId="props.recordId"
        :viewRecord="isViewMode"
        :loadingSubmit="loadingSubmit"
        @close="closeDialog"
        @open="handleOpen"
        size="modal-md"
    >
        <ModalLoader v-if="isModalLoading" />

        <template v-else>
            <div class="table-responsive" v-if="sesion && sesion.usuario">
                <table class="table table-sm table-hover" style="width: 100%; table-layout: fixed;">
                    <tbody>
                        <tr>
                            <th style="width: 35%;"><strong>Usuario:</strong></th>
                            <td>{{ sesion.usuario.nombre }}</td>
                        </tr>
                        <tr>
                            <th><strong>Correo:</strong></th>
                            <td>{{ sesion.usuario.correo }}</td>
                        </tr>
                        <tr>
                            <th><strong>Inicio de sesión:</strong></th>
                            <td>{{ sesion.hora_inicio_sesion }}</td>
                        </tr>
                        <tr>
                            <th><strong>Cierre de sesión:</strong></th>
                            <td>
                                <span v-if="sesion.hora_cierre_sesion">{{ sesion.hora_cierre_sesion }}</span>
                                <span v-else class="text-muted fst-italic">Sesión activa</span>
                            </td>
                        </tr>
                        <tr>
                            <th><strong>Último intento:</strong></th>
                            <td>{{ sesion.ultimo_intento_at ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th><strong>Razón:</strong></th>
                            <td>{{ sesion.razon ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th><strong>IP:</strong></th>
                            <td>{{ sesion.direccion_ip }}</td>
                        </tr>
                        <tr>
                            <th><strong>Agente:</strong></th>
                            <td>{{ sesion.agente_usuario }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="text-center text-muted">
                No se encontraron datos de sesión.
            </div>
        </template>
    </BaseModal>
</template>
