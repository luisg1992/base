<script setup>
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import {computed} from 'vue'

defineOptions({
    name: 'WInput',
    inheritAttrs: false
})

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
        default: false
    },
    autocomplete: {
        type: Boolean,
        default: false
    },
    id: {
        type: String,
        default: () => `w-input-${Math.random().toString(36).substring(2, 17)}`
    },
    disabled: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: false
    },
    maxlength: [String, Number],
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
        type: [String, Array],
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
    labelBottom: {
        type: String,
        default: ''
    },
    labelBottomClass: {
        type: String,
        default: ''
    },
    min: [String, Number],
    max: [String, Number],
})

const emit = defineEmits(['update:modelValue', 'clickButton'])

const internalValue = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
})

const handleClickButton = () => {
    emit('clickButton', internalValue.value)
}
</script>

<template>
    <div class="w-input">
        <!-- Label -->
        <label v-if="label" :for="id" class="w-input__label">
            <span>{{ label }}</span>
            <span v-if="labelExtra" :class="['w-input__label-extra', labelExtraClass]">
                ({{ labelExtra }})
            </span>
        </label>

        <!-- Input + Botón -->
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="flex: 1; min-width: 0;">
                <InputText v-model="internalValue"
                           :type="type"
                           class="w-100"
                           :class="{ 'p-invalid': !!error }"
                           :id="id"
                           :name="name"
                           :required="required"
                           :maxlength="maxlength"
                           :min="min"
                           :max="max"
                           :placeholder="placeholder"
                           :autofocus="autofocus"
                           :autocomplete="autocomplete ? 'on' : 'off'"
                           :disabled="disabled"
                           v-bind="$attrs"
                />
            </div>
            <!-- Botón -->
            <template v-if="showButton || showButtonIcon">
                <Button
                    v-if="showButtonIcon"
                    :icon="`ti ti-${buttonIcon}`"
                    @click="handleClickButton"
                    outlined
                    size="small"
                    class="w-input__button"/>
                <Button
                    v-else
                    @click="handleClickButton"
                    size="small"
                    class="w-input__button">
                    {{ buttonText }}
                </Button>
            </template>
        </div>
        <div v-if="labelBottom" :class="['w-input__label-bottom', labelBottomClass]">
            ({{ labelBottom }})
        </div>
        <!-- Error -->
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
