<script setup>
import Checkbox from 'primevue/checkbox'
import {computed} from 'vue'

defineOptions({
    name: 'WCheckbox',
    inheritAttrs: false
})

const props = defineProps({
    modelValue: {},
    label: {
        type: String,
        default: ''
    },
    name: {
        type: String,
        default: ''
    },
    id: {
        type: String,
        default: () => `w-checkbox-${Math.random().toString(36).substring(2, 17)}`
    },
    disabled: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const checked = computed({
    get: () => {
        return props.modelValue === true || props.modelValue === 1 || props.modelValue === '1'
    },
    set: (val) => {
        if (typeof props.modelValue === 'number') {
            emit('update:modelValue', val ? 1 : 0)
        } else if (typeof props.modelValue === 'string') {
            emit('update:modelValue', val ? '1' : '0')
        } else {
            emit('update:modelValue', val)
        }
    }
})
</script>

<template>
    <div class="w-checkbox">
        <Checkbox
            v-model="checked"
            :inputId="id"
            :name="name"
            :disabled="disabled"
            :binary="true"
            :required="required"
        />
        <label v-if="label" :for="id" class="w-checkbox__label">{{ label }}</label>
    </div>
</template>
