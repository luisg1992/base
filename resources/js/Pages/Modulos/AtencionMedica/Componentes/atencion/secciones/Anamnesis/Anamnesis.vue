<script setup>
import { ref, watch, defineProps, defineExpose, defineEmits } from 'vue'
import Textarea from 'primevue/textarea'
import axios from 'axios'
import { useCabeceraStore } from '@/stores/useCabeceraStore'

const emit = defineEmits(['change'])

const props = defineProps({
    cabecera: { type: Object, required: true },
    IdTipoServicio: [Number, String],
    viewRecord: Boolean
})

const cabeceraStore = useCabeceraStore()
const isViewMode = props.viewRecord
const isUpdating = ref(false)
const hasChanges = ref(false)

const form = ref({
    antecedentesQuirurgicos: '',
    antecedentesPatologicos: '',
    antecedentesObstetricos: '',
    antecedentesAlergias: '',
    antecedentesFamiliares: '',
    antecedentesOtros: '',
    relatoCronologico: '',
    funcionesBiologicas: '',
    tiempoEnfermedad: '',
    motivoConsulta: '',
    examenGeneral: '',
    examenRegional: '',
    citaTratamiento: ''
})

const formInicial = ref({})

// 🔁 Cuando cambia cabecera, actualiza form e inicial
watch(
    () => props.cabecera,
    (nuevaCabecera) => {
        if (!nuevaCabecera || !Object.keys(nuevaCabecera).length) return

        form.value = {
            antecedentesQuirurgicos: nuevaCabecera.Antecedentes_Quirugicos || '',
            antecedentesPatologicos: nuevaCabecera.Antecedentes_Patologicos || '',
            antecedentesObstetricos: nuevaCabecera.Antecedentes_Obstetricos || '',
            antecedentesAlergias: nuevaCabecera.Antecedentes_Alergias || '',
            antecedentesFamiliares: nuevaCabecera.Antecedentes_Familiares || '',
            antecedentesOtros: nuevaCabecera.Antecedentes_Otros || '',
            relatoCronologico: nuevaCabecera.Relato_Cronologico || '',
            funcionesBiologicas: nuevaCabecera.Funciones_Biologicas || '',
            tiempoEnfermedad: nuevaCabecera.Tiempo_Enfermedad || '',
            motivoConsulta: nuevaCabecera.Motivo_Consulta || '',
            examenGeneral: nuevaCabecera.Examen_General || '',
            examenRegional: nuevaCabecera.Examen_Regional || '',
            citaTratamiento: nuevaCabecera.CitaTratamiento || ''
        }

        formInicial.value = JSON.parse(JSON.stringify(form.value))
        hasChanges.value = false
    },
    { immediate: true, deep: true }
)

watch(form, (nuevo) => {
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
}, { deep: true })


const actualizarAnamnesis = async () => {
    if (!hasChanges.value) return null

    const cabecera = cabeceraStore.cabecera
    if (!cabecera.IdAtencion || !cabecera.IdPaciente) return null

    isUpdating.value = true
    try {
        const { data } = await axios.post('/atencion-medica/WebS_InsertarAnamnesisAtencion', {
            IdAtencion: cabecera.IdAtencion,
            IdPaciente: cabecera.IdPaciente,
            NroHistoriaClinica: cabecera.CitaTratamiento,
            Antecedentes_Quirugicos: form.value.antecedentesQuirurgicos?.toUpperCase(),
            Antecedentes_Patologicos: form.value.antecedentesPatologicos?.toUpperCase(),
            Antecedentes_Obstetricos: form.value.antecedentesObstetricos?.toUpperCase(),
            Antecedentes_Alergias: form.value.antecedentesAlergias?.toUpperCase(),
            Antecedentes_Familiares: form.value.antecedentesFamiliares?.toUpperCase(),
            Antecedentes_Otros: form.value.antecedentesOtros?.toUpperCase(),
            Relato_Cronologico: form.value.relatoCronologico?.toUpperCase(),
            Funciones_Biologicas: form.value.funcionesBiologicas?.toUpperCase(),
            Tiempo_Enfermedad: form.value.tiempoEnfermedad?.toUpperCase(),
            Motivo_Consulta: form.value.motivoConsulta?.toUpperCase(),
            Examen_General: form.value.examenGeneral?.toUpperCase(),
            Examen_Regional: form.value.examenRegional?.toUpperCase(),
            CitaTratamiento: cabecera.CitaTratamiento
        })

        if (data.success) {
            hasChanges.value = false
            formInicial.value = JSON.parse(JSON.stringify(form.value))

            if (data.data) {
                cabeceraStore.updateCabeceraCampos(data.data)
            }
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
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Antecedentes Quirúrgicos</label>
            <Textarea v-model="form.antecedentesQuirurgicos" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Antecedentes Patológicos</label>
            <Textarea v-model="form.antecedentesPatologicos" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Antecedentes Obstétricos</label>
            <Textarea v-model="form.antecedentesObstetricos" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Alergias</label>
            <Textarea v-model="form.antecedentesAlergias" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Antecedentes Familiares</label>
            <Textarea v-model="form.antecedentesFamiliares" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Otros Antecedentes</label>
            <Textarea v-model="form.antecedentesOtros" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Relato Cronológico</label>
            <Textarea v-model="form.relatoCronologico" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Funciones Biológicas</label>
            <Textarea v-model="form.funcionesBiologicas" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Tiempo de Enfermedad</label>
            <Textarea v-model="form.tiempoEnfermedad" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Motivo de Consulta</label>
            <Textarea v-model="form.motivoConsulta" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Examen General</label>
            <Textarea v-model="form.examenGeneral" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">Examen Regional</label>
            <Textarea v-model="form.examenRegional" rows="2" autoResize class="w-100" :disabled="isUpdating" />
        </div>
    </div>
</template>
