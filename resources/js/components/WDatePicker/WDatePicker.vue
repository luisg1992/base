<script setup>
import { computed } from 'vue'
import DatePicker from 'primevue/datepicker'

const props = defineProps({
    modelValue: [Date, String],
    label: String,
    name: String,
    placeholder: String,
    id: {
        type: String,
        default: () => `w-datepicker-${Math.random().toString(36).substring(2, 17)}`
    },
    disabled: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    error: { type: String, default: '' },
    minDate: { type: Date, default: null },
    view: {
        type: String,
        default: 'date' // 'date', 'month', 'year'
    },
    required: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue'])

const rawValue = computed({
    get() {
        return props.modelValue
    },
    set(val) {
        emit('update:modelValue', formatDate(val))
    }
})

function formatDate(fecha) {
    if (!fecha) return ''
    const f = new Date(fecha)
    const dia = f.getDate().toString().padStart(2, '0')
    const mes = (f.getMonth() + 1).toString().padStart(2, '0')
    const anio = f.getFullYear()

    if (props.view === 'month') return `${mes}/${anio}`
    if (props.view === 'year') return `${anio}`
    return `${dia}/${mes}/${anio}`
}

const dateFormat = computed(() => {
    if (props.view === 'month') return 'mm/yy'
    if (props.view === 'year') return 'yy'
    return 'dd/mm/yy'
})
</script>

<template>
    <div class="w-date-picker">
        <label
            v-if="label"
            :for="id"
            class="w-date-picker__label"
        >
            {{ label }}
            <span v-if="required" class="w-date-picker__required">*</span>
        </label>

        <DatePicker
            v-model="rawValue"
            :id="id"
            :name="name"
            :placeholder="placeholder"
            :disabled="disabled"
            :autofocus="autofocus"
            :minDate="minDate"
            :view="view"

            :dateFormat="dateFormat"
            class="w-date-picker__input"
            :class="{ 'p-invalid': error }"
            v-bind="$attrs"
        />

        <small v-if="error" class="w-date-picker__error">{{ error }}</small>
    </div>
</template>

<style scoped>
.w-date-picker {
    width: 100%;
}

.w-date-picker__label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.w-date-picker__required {
    color: #f44336;
    margin-left: 0.25rem;
}

.w-date-picker__error {
    display: block;
    color: #f44336;
    margin-top: 0.25rem;
    font-size: 0.75rem;
}

.w-date-picker__input {
    width: 100%;
}
</style>
