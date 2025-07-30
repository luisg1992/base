<script setup>
import { computed } from 'vue'
import DatePicker from 'primevue/datepicker'

defineOptions({
    name: 'WTimePicker',
    inheritAttrs: false
})

const props = defineProps({
    modelValue: [Date, String],
    label: String,
    name: String,
    placeholder: String,
    id: {
        type: String,
        default: () => `w-timepicker-${Math.random().toString(36).substring(2, 17)}`
    },
    disabled: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    error: { type: String, default: '' },
    required: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue'])

function parseTime(str) {
    if (!str) return null
    const [hours, minutes] = str.split(':').map(Number)
    const date = new Date()
    date.setHours(hours)
    date.setMinutes(minutes)
    date.setSeconds(0)
    date.setMilliseconds(0)
    return date
}

function formatTime(fecha) {
    if (!fecha) return ''
    const f = new Date(fecha)
    const horas = f.getHours().toString().padStart(2, '0')
    const minutos = f.getMinutes().toString().padStart(2, '0')
    return `${horas}:${minutos}`
}

const rawValue = computed({
    get() {
        if (typeof props.modelValue === 'string') {
            return parseTime(props.modelValue)
        }
        return props.modelValue ?? null
    },
    set(val) {
        emit('update:modelValue', formatTime(val))
    }
})
</script>

<template>
    <div class="w-time-picker">
        <label
            v-if="label"
            :for="id"
            class="w-time-picker__label"
        >
            {{ label }}
            <span v-if="required" class="w-time-picker__required">*</span>
        </label>

        <DatePicker
            v-model="rawValue"
            :id="id"
            :name="name"
            :placeholder="placeholder"
            :disabled="disabled"
            :autofocus="autofocus"
            timeOnly
            hourFormat="24"
            class="w-time-picker__input"
            :class="{ 'p-invalid': error }"
            v-bind="$attrs"
        />

        <small v-if="error" class="w-time-picker__error">{{ error }}</small>
    </div>
</template>
