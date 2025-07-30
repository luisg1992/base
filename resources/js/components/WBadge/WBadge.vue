<script setup>
import { computed, useAttrs, useSlots } from 'vue'
import Badge from 'primevue/badge'

defineOptions({
    name: 'WBadge',
    inheritAttrs: false
})

const props = defineProps({
    value: [String, Number],
    severity: {
        type: String,
        default: 'info', // primary, success, info, warning, danger, help
        validator: val =>
            ['primary', 'success', 'info', 'warn', 'danger', 'help', 'contrast'].includes(val)
    },
    size: {
        type: String,
        default: null, // null | 'large' | 'xlarge'
        validator: val => ['large', 'xlarge', null].includes(val)
    },
    rounded: Boolean // Para mostrar como círculo
})

const attrs = useAttrs()
const slots = useSlots()

const badgeClass = computed(() => ({
    'p-badge-rounded': props.rounded
}))
</script>

<template>
    <Badge v-bind="attrs" :value="value" :severity="severity" :size="size" class="w-badge" :class="badgeClass">
        <!-- slot opcional si quieres contenido personalizado -->
        <template v-if="slots.default">
            <slot />
        </template>
    </Badge>
</template>
