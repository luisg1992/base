<script setup>
import { ref, computed, watch } from 'vue'
import Textarea from 'primevue/textarea'
import axios from 'axios'
import BaseInput from '@/components/WInput/WInput.vue'
import { useFarmaciaStore } from '@/stores/useFarmaciaStore'
import { useCabeceraStore } from '@/stores/useCabeceraStore'

const props = defineProps({
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
})

const emit = defineEmits(['change'])
const cabeceraStore = useCabeceraStore()
const cabecera = computed(() => cabeceraStore.cabecera)

const formInicial = ref({})
const form = ref({
    CitaTratamiento: ''
})

const isViewMode = computed(() => props.viewRecord)
const isUpdating = ref(false)
const hasChanges = ref(false)

const farmaciaStore = useFarmaciaStore()
const listaFarmaciaAtencion = computed(() => farmaciaStore.farmacia)

watch(
    () => cabecera.value,
    (nuevaCabecera) => {
        if (!nuevaCabecera || !Object.keys(nuevaCabecera).length) return

        form.value = {
            CitaTratamiento: nuevaCabecera.CitaTratamiento || ''
        }

        formInicial.value = JSON.parse(JSON.stringify(form.value))
        hasChanges.value = false
    },
    { immediate: true, deep: true }
)

watch(
    form,
    (nuevo) => {
        const cambios = Object.keys(formInicial.value).filter(
            (key) => nuevo[key] !== formInicial.value[key]
        )

        if (cambios.length > 0) { 
            hasChanges.value = true
            emit('change', { hasChanges: true, modificados: cambios })
        } else {
            hasChanges.value = false
            emit('change', { hasChanges: false })
        }
    },
    { deep: true }
)


const actualizarAnamnesis = async () => {
    console.log(cabecera.value);
    if (!hasChanges.value) return null
    if (!cabecera.value.IdAtencion || !cabecera.value.IdPaciente) return null

    isUpdating.value = true

    try {
        const { data } = await axios.post('/atencion-medica/WebS_InsertarAnamnesisAtencion', {
            IdAtencion: cabecera.value.IdAtencion,
            IdPaciente: cabecera.value.IdPaciente,
            Antecedentes_Quirugicos: cabecera.value.Antecedentes_Quirugicos,
            Antecedentes_Patologicos: cabecera.value.Antecedentes_Patologicos,
            Antecedentes_Obstetricos: cabecera.value.Antecedentes_Obstetricos,
            Antecedentes_Alergias: cabecera.value.Antecedentes_Alergias,
            Antecedentes_Familiares: cabecera.value.Antecedentes_Familiares,
            Antecedentes_Otros: cabecera.value.Antecedentes_Otros,
            Relato_Cronologico: cabecera.value.Relato_Cronologico,
            Funciones_Biologicas: cabecera.value.Funciones_Biologicas,
            Tiempo_Enfermedad: cabecera.value.Tiempo_Enfermedad,
            Motivo_Consulta: cabecera.value.Motivo_Consulta,
            Examen_General: cabecera.value.Examen_General,
            Examen_Regional: cabecera.value.Examen_Regional,
            CitaTratamiento: form.value.CitaTratamiento?.toUpperCase()
        })

        if (data.success) {
            hasChanges.value = false
            formInicial.value = JSON.parse(JSON.stringify(form.value))
        } else {
            console.warn('Error al guardar Anamnesis:', data.mensaje)
        }

        return data
    } catch (err) {
        console.error('Error en actualización de anamnesis:', err)
        return { success: false, mensaje: 'Error en la solicitud', error: err }
    } finally {
        isUpdating.value = false
    }
}

defineExpose({
    actualizarAnamnesis,
    hasChanges
})
</script>

<template>
    <div class="row g-3 align-items-end">
        <!-- Productos de Farmacia -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12" v-if="listaFarmaciaAtencion.length > 0">
            <div v-for="(fila, index) in listaFarmaciaAtencion"
                :key="`${fila.IdDiagnostico}-${fila.IdProducto}-${index}`"
                class="d-flex position-relative p-1 border-bottom">
                <div class="row w-100">
                    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
                        <BaseInput :value="fila.DiagnosticoNombre" :disabled="true" />
                    </div>
                    <div class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
                        <BaseInput :value="fila.Tratamiento" :disabled="true" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicaciones del Tratamiento -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">
            <label class="form-label">Indicaciones del Tratamiento</label>
            <Textarea v-model="form.CitaTratamiento" rows="4" autoResize class="w-100" />
        </div>
    </div>
</template>
