<script setup>
import {computed} from 'vue';
import Button from 'primevue/button';

const props = defineProps({
    label: {
        type: String,
        default: '',
    },
    icon: {
        type: String,
        default: null,
    },
    iconPos: {
        type: String,
        default: 'left',
    },
    variant: {
        type: String,
        default: 'undefined', //"outlined" | "text" | "link"
    },
    raised: {
        type: Boolean,
        default: false,
    },
    color: {
        type: String,
        default: 'primary',
    },
    size: {
        type: String,
        default: 'md',
    },
    tooltipColor: {
        type: String,
        default: '',
    },
    tooltipText: {
        type: String,
        default: '',
    },
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['click']);
const showIcon = computed(() => props.icon?`pi pi-${props.icon}`:null);
// const hasIconRight = computed(() => !!props.iconRight)
// const hasTooltip = computed(() => !!props.tooltipText)
// const hasLabel = computed(() => !!props.label)

function handleClick() {
    emit('click')
}
</script>

<template>
    <Button :label="label"
            :icon="showIcon"
            :iconPos="iconPos"
            :severity="color"
            :variant="variant"
            :size="size"
            :raised="raised"
            :disabled="disabled"
            :loading="loading"
            @click="handleClick"
            :aria-label="`${icon}`">
    </Button>
</template>
