<script setup>
import Select from 'primevue/select';
import {computed, ref} from 'vue';

const props = defineProps({
    modelValue: [String, Number, Object],
    label: String,
    name: String,
    placeholder: {
        type: String,
        default: 'SELECCIONE UNA OPCIÓN'
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
        default: () => `vuex-select-${Math.random().toString(36).substring(2, 17)}`
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
        default: false // Indica si el filtrado es remoto
    },
    loading: {
        type: Boolean,
        default: false // Muestra loader mientras se consulta
    },
    minFilterLength: {
        type: Number,
        default: 2 // el valor que tú prefieras como mínimo, por ejemplo 2 caracteres
    }
})

const emit = defineEmits(['update:modelValue', 'change', 'clickButton', 'filter'])

const internalValue = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
})

const filterValue = ref('') // Para controlar el valor filtrado

function handleFilter(event) {
    filterValue.value = event.value
    // Solo emitir si el valor cumple con el mínimo de caracteres
    if ((event.value || '').length >= props.minFilterLength) {
        emit('filter', event.value)
    }
}

// Si es lazy, no formatear los options (los asume como llegan del padre, ya que pueden variar dinámicamente)
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

const clickButton = () => {
    emit('clickButton')
}

function handleChange(event) {
    emit('change', event)
}
</script>

<template>
    <div style="width: 100%">
        <label :for="id" class="form-label" v-if="label" >{{ label }}</label>
        <div style="display: flex; align-items: center; gap: 8px">
            <div style="flex: 1; min-width: 0;">
                <Select v-model="internalValue"
                        :options="formattedOptions"
                        :optionLabel="optionLabel"
                        :optionValue="optionValue"
                        :placeholder="placeholder"
                        :disabled="disabled"
                        :autofocus="autofocus"
                        :filter="filter"
                        :showClear="showClear"
                        :required="required"
                        :inputId="id"
                        :autoFilterFocus="true"
                        class="w-full"
                        appendTo="body"
                        size="small"
                        v-bind="$attrs"
                        @change="handleChange"
                        @filter="handleFilter"
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
            <button class="btn btn-primary"
                    style="white-space: nowrap" v-if="showButton" @click="clickButton">
                {{ buttonText }}
            </button>
        </div>
    </div>
</template>
