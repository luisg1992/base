<script setup>
import Select from 'primevue/select'
import { computed, ref } from 'vue'

defineOptions({
    name: 'WSelect',
    inheritAttrs: false
})

const props = defineProps({
    modelValue: [String, Number, Object],
    label: String,
    name: String,
    placeholder: {
        type: String,
        default: 'Seleccione una opción'
    },
    options: {
        type: Array,
        required: true
    },
    optionLabel: {
        type: String,
        default: 'label'
    },
    optionValue: {
        type: String,
        default: 'value'
    },
    id: {
        type: String,
        default: () => `w-select-${Math.random().toString(36).substring(2, 17)}`
    },
    disabled: {
        type: Boolean,
        default: false
    },
    showClear: {
        type: Boolean,
        default: true
    },
    required: {
        type: Boolean,
        default: false
    },
    autofocus: {
        type: Boolean,
        default: false
    },
    filter: {
        type: Boolean,
        default: false
    },
    showButton: {
        type: Boolean,
        default: false
    },
    buttonText: {
        type: String,
        default: '+'
    },
    lazy: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    minFilterLength: {
        type: Number,
        default: 2
    }
})

const emit = defineEmits(['update:modelValue', 'change', 'clickButton', 'filter'])

const internalValue = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
})

const filterValue = ref('')

function handleFilter(event) {
    filterValue.value = event.value
    if ((event.value || '').length >= props.minFilterLength) {
        emit('filter', event.value)
    }
}

const formattedOptions = computed(() => {
    if (props.lazy) return props.options
    if (!props.optionValue || props.optionValue === 'value') {
        return props.options
    }
    return props.options.map(item => ({
        ...item,
        value: item[props.optionValue] ?? item
    }))
})

const computedShowClear = computed(() => {
    return props.disabled ? false : props.showClear
})

function handleChange(event) {
    emit('change', event)
}

function clickButton() {
    emit('clickButton')
}
</script>

<template>
    <div class="w-select">
        <label v-if="label" :for="id" class="w-select__label">{{ label }}</label>
        <div class="w-select__wrapper">
            <div class="w-select__control">
                <Select v-model="internalValue"
                        :options="formattedOptions"
                        :optionLabel="optionLabel"
                        :optionValue="optionValue"
                        :placeholder="placeholder"
                        :disabled="disabled"
                        :autofocus="autofocus"
                        :filter="filter"
                        :showClear="computedShowClear"
                        :required="required"
                        :inputId="id"
                        :autoFilterFocus="true"
                        appendTo="body"
                        size="small"
                        v-bind="$attrs"
                        @change="handleChange"
                        @filter="handleFilter"
                        class="w-full"
                        style="width: 100%;">
                    <template #option="slotProps">
                        <slot name="option" v-bind="slotProps">
                            {{ slotProps.option?.[optionLabel] ?? '' }}
                        </slot>
                    </template>
                    <template #empty>
                        <span v-if="!loading && (filterValue.length < minFilterLength)">
                            Escribe al menos {{ minFilterLength }} caracteres para buscar
                        </span>
                        <span v-else-if="!loading">No hay opciones</span>
                        <span v-else>Cargando...</span>
                    </template>
                </Select>
            </div>
            <button v-if="showButton" type="button" class="w-select__button" @click="clickButton">
                {{ buttonText }}
            </button>
        </div>
    </div>
</template>
