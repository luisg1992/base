<script setup>
import Button from 'primevue/button'
import {computed, useAttrs, useSlots} from 'vue'

defineOptions({
    name: 'w-button',
    inheritAttrs: false
})

const props = defineProps({
    label: String,
    icon: String,
    iconRight: String,

    typeButton: {
        type: String,
        default: null,
        validator: val => ['primary', 'secondary', 'success', 'danger', 'warning', 'info', null].includes(val)
    },

    color: String,
    textColor: String,

    outlined: Boolean,
    rounded: Boolean,
    raised: Boolean,
    text: Boolean,

    size: {
        type: String,
        default: 'normal'
    },

    loading: Boolean,
    disabled: Boolean
})

const emit = defineEmits(['click'])
const attrs = useAttrs()
const slots = useSlots()

const typeStyleMap = {
    primary: {severity: 'primary'},
    secondary: {severity: 'secondary'},
    success: {severity: 'success'},
    danger: {severity: 'danger'},
    warning: {severity: 'warning'},
    info: {severity: 'info'},
    help: {severity: 'help'}
}

const resolvedSeverity = computed(() => props.typeButton ? typeStyleMap[props.typeButton]?.severity : null)
const hasSlot = (name) => !!slots[name]
const hasLabel = computed(() => !!props.label)
const hasIcon = computed(() => !!props.icon)
const hasIconRight = computed(() => !!props.iconRight)
const isIconOnly = computed(() => hasIcon.value && !hasLabel.value)

function handleClick(event) {
    emit('click', event)
}
</script>

<template>
    <Button v-bind="{
        ...attrs,
        label: null,
        icon: null,
        severity: resolvedSeverity,
        outlined,
        rounded,
        raised,
        text,
        loading,
        disabled,
        size
      }"
            :class="['w-button', { 'w-button--icon-only': isIconOnly }]"
            @click="handleClick">
        <template v-if="hasSlot('default')">
            <slot/>
        </template>
        <template v-else>
            <!-- Icono izquierdo -->
            <span v-if="hasSlot('icon')">
                  <slot name="icon"/>
                </span>
            <i v-else-if="hasIcon"
               :class="['ti', 'ti-'+ icon]"
               class="button-icon"/>

            <!-- Etiqueta -->
            <span v-if="hasSlot('label')">
                  <slot name="label"/>
                </span>
            <span v-else-if="hasLabel">
                  {{ label }}
                </span>
            <!-- Icono derecho -->
            <span v-if="hasSlot('icon-right')">
                  <slot name="icon-right"/>
                </span>
            <i v-else-if="hasIconRight"
               :class="['ti', iconRight]"
               class="button-icon-right"
            />
        </template>
    </Button>
</template>
