<script setup>
import { ref, watch, defineModel } from 'vue'
import Dialog from 'primevue/dialog'
import axios from 'axios'

const visible = defineModel('visible') // <-- Aquí es la clave

const emit = defineEmits(['seleccionar'])

const busqueda = ref('')
const listaPacientes = ref([])
const loading = ref(false)

const buscarPacientes = async () => {
    if (!busqueda.value.trim() || busqueda.value.length < 8) {
        return showAlert('VALIDACIÓN DE CAMPOS REALIZADO', 'PARA LA BÚSQUEDA DE PACIENTES SE REQUIERE COMO MÍNIMO 8 CARACTERES', 'warning', false, true);
    }

    try {
        loading.value = true
        const response = await axios.post('/personas/pacientes/WebS_Pacientes_BuscarFiltro', {
            filtro: busqueda.value
        })

        if (response.data.success) {
            listaPacientes.value = response.data.data
        } else {
            listaPacientes.value = []
        }

    } catch (error) {
        console.error('Error al buscar pacientes:', error)
        return showAlert('VALIDACIÓN DE CAMPOS REALIZADO', error, 'warning', false, true);
    } finally {
        loading.value = false
    }
}

watch(visible, (nuevo) => {
    if (!nuevo) {
        busqueda.value = ''
        listaPacientes.value = []
    }
})
</script>

<template>
    <Dialog v-model:visible="visible" modal header="Buscar Paciente por Nombres" :style="{ width: '600px' }">
        <div class="input-group mb-3">
            <input type="text" v-model="busqueda" class="form-control"
                placeholder="Ingrese nombres para buscar (mínimo 8 caracteres)..." :disabled="loading"
                @keyup.enter="buscarPacientes" />
            <button class="btn btn-primary" type="button" @click="buscarPacientes" :disabled="loading">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <ul class="list-group" v-if="listaPacientes.length > 0">
            <li v-for="paciente in listaPacientes" :key="paciente.IdPaciente"
                class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <small><b>DOCUMENTO: {{ paciente.NroDocumento }} | HC: {{ paciente.NroHistoriaClinica}}</b></small><br />
                    <small>{{ paciente.NombreCompleto }}</small>
                </div>
                <button class="btn btn-sm btn-primary" @click="emit('seleccionar', paciente)">
                    Seleccionar
                </button>
            </li>
        </ul>

        <div v-else-if="!loading" class="text-center text-muted">
            No hay resultados.
        </div>

        <div v-if="loading" class="text-center text-secondary">
            Buscando pacientes...
        </div>
    </Dialog>
</template>
