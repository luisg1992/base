<script setup>
import { computed, ref, watch } from 'vue'
import DatePicker from 'primevue/datepicker'

// Props
const props = defineProps({
    modelValue: [Date, String],
    label: String,
    name: String,
    placeholder: String,
    id: {
        type: String,
        default: () => `vuex-datepicker-${Math.random().toString(36).substring(2, 17)}`
    },
    disabled: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    error: { type: String, default: '' },
    minDate: { type: Date, default: null },
    view: {
        type: String,
        default: 'date' // 'date', 'month', 'year'
    }
})

const emit = defineEmits(['update:modelValue'])

// Valor interno del componente
const rawValue = ref(props.modelValue ?? null)

// Emitir valor formateado cuando cambia
watch(rawValue, (val) => {
    emit('update:modelValue', formatearFecha(val))
})

// Formatear la fecha según el tipo de vista
function formatearFecha(fecha) {
    if (!fecha) return ''

    const f = new Date(fecha)
    const dia = f.getDate().toString().padStart(2, '0')
    const mes = (f.getMonth() + 1).toString().padStart(2, '0')
    const anio = f.getFullYear()

    if (props.view === 'month') return `${mes}/${anio}`
    if (props.view === 'year') return `${anio}`
    return `${dia}/${mes}/${anio}` // default 'date'
}

// Formato visual del calendario
const dateFormat = computed(() => {
    if (props.view === 'month') return 'mm/yy'
    if (props.view === 'year') return 'yy'
    return 'dd/mm/yy'
})
</script>

<template>
  <div class="w-100">
    <label :for="id" class="font-bold block">{{ label }}</label>

    <DatePicker
      v-model="rawValue"
      :id="id"
      :name="name"
      class="w-100"
      :class="{ 'p-invalid': error }"
      :placeholder="placeholder"
      :disabled="disabled"
      :autofocus="autofocus"
      :minDate="minDate"
      :view="view"
      :dateFormat="dateFormat"
      v-bind="$attrs"
    />

    <small v-if="error" class="p-error">{{ error }}</small>
  </div>
</template>

<style scoped>
.p-error {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
}
</style>
