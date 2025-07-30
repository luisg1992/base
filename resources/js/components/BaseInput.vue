<script setup>
import InputText from 'primevue/inputtext'
import {computed} from 'vue'

const props = defineProps({
    modelValue: [String, Number],
    label: {
        type: String,
        default: ''
    },
    name: String,
    placeholder: String,
    type: {
        type: String,
        default: 'text'
    },
    autofocus: {
        type: Boolean,
        default: true
    },
    autocomplete: {
        type: Boolean,
        default: false
    },
    id: {
        type: String,
        default: () => `vuex-input-${Math.random().toString(36).substring(2, 17)}`
    },
    disabled: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: false
    },
    maxlength: {
        default: null
    },
    showButton: {
        type: Boolean,
        default: false
    },
    showButtonIcon: {
        type: Boolean,
        default: false
    },
    buttonText: {
        type: String,
        default: '+'
    },
    buttonIcon: {
        type: String,
        default: 'search'
    },
    error: {
        default: null
    },
    labelExtra: {
        type: String,
        default: ''
    },
    labelExtraClass: {
        type: String,
        default: ''
    },
    min: {
        type: [String, Number],
        default: null
    },
    max: {
        type: [String, Number],
        default: null
    },
})

const emit = defineEmits(['update:modelValue', 'clickButton'])

const internalValue = computed({
    get: () => props.modelValue,
    set: val => emit('update:modelValue', val)
})

const clickButton = () => {
    emit('clickButton')
}
</script>

<template>
    <div>
        <label :for="id" class="form-label" v-if="label !== ''" style="display: flex; align-items: center; gap: 6px;text-transform: uppercase;">
            <span>{{ label }}</span>
            <span v-if="labelExtra"
                :class="labelExtraClass"
                style="font-weight: bold; font-size: 0.675rem;">
      ({{ labelExtra }})
    </span>
        </label>
        <div style="display: flex; align-items: center; gap: 8px">
            <div style="flex: 1; min-width: 0;">
                <InputText :type="type"
                           class="form-control w-100 mt-1"
                           :class="{ 'p-invalid': !!error }"
                           size="small"
                           :id="id"
                           :name="name"
                           :required="required"
                           :maxlength="maxlength"
                           :min="min"
                           :max="max"
                           :placeholder="placeholder"
                           v-model="internalValue"
                           :autofocus="autofocus"
                           :autocomplete="autocomplete ? 'on' : 'off'"
                           :disabled="disabled"
                           v-bind="$attrs"/>
            </div>
            <button class="btn btn-primary"
                    icon="ti ti-search"
                    style="white-space: nowrap" v-if="showButton || showButtonIcon" @click="clickButton">
                <i :class="`ti ti-${buttonIcon} ti-sm`" v-if="showButtonIcon"></i>
                <span v-else>{{ buttonText }}</span>
            </button>
        </div>
        <small v-if="error" class="p-error">
            <template v-if="Array.isArray(error)">
                <div v-for="err in error" :key="err">{{ err }}</div>
            </template>
            <template v-else>
                <div>{{ error }}</div>
            </template>
        </small>
    </div>
</template>

<style scoped>
.p-error {
    color: #dc3545;
    font-size: 0.675rem;
    margin-top: 0.25rem;
    display: block;
}
</style>
