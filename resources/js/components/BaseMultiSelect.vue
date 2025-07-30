<script setup>
import MultiSelect from 'primevue/multiselect'

// Props
defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    options: {
        type: Array,
        required: true,
    },
    optionLabel: {
        type: String,
        default: 'label',
    },
    optionValue: {
        type: String,
        default: 'value',
    },
    placeholder: {
        type: String,
        default: 'Selecciona...',
    },
    label: {
        type: String,
        default: '',
    },
    inputId: {
        type: String,
        default: () => `multiselect-${Math.random().toString(36).substr(2, 9)}`
    },
    filter: {
        type: Boolean,
        default: true,
    },
    maxSelectedLabels: {
        type: Number,
        default: 3,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    customClass: {
        type: String,
        default: '',
    },
    helperText: {
        type: String,
        default: '',
    },
    inputProps: {
        type: Object,
        default: () => ({}),
    },
    showButton: {
        type: Boolean,
        default: false
    },
    buttonText: {
        type: String,
        default: '+'
    },
})

const emit = defineEmits(['update:modelValue', 'clickButton'])

const clickButton = () => {
    emit('clickButton')
}
</script>

<template>
    <div style="width: 100%">
        <label :for="inputId" class="form-label" v-if="label">{{ label }}</label>
        <div style="display: flex; align-items: center; gap: 8px">
            <div style="flex: 1; min-width: 0;">
                <!--        <label v-if="label" :for="inputId" class="block mb-1 font-semibold">{{ label }}</label><br>-->
                <MultiSelect :modelValue="modelValue"
                             :options="options"
                             :optionLabel="optionLabel"
                             :optionValue="optionValue"
                             :placeholder="placeholder"
                             :filter="filter" size="small"
                             :maxSelectedLabels="maxSelectedLabels"
                             :disabled="disabled"
                             :inputId="inputId"
                             :class="customClass"
                             @update:modelValue="$emit('update:modelValue', $event)"
                             v-bind="inputProps"/>
            </div>
            <button class="btn btn-primary"
                    style="white-space: nowrap" v-if="showButton" @click="clickButton">
                {{ buttonText }}
            </button>
        </div>
    </div>
</template>
