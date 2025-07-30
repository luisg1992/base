<script setup>
const props = defineProps({
    modelValue: {
        type: [Boolean, Number],
        default: false
    },
    name: String,
    id: {
        type: String,
        default: () => `vuex-checkbox-${Math.random().toString(36).substring(2, 17)}`
    },
    readonly: {
        type: Boolean,
        default: false
    }
})

defineEmits(['update:modelValue'])
</script>

<template>
    <div class="form-check d-flex align-items-center gap-2 my-1 ps-0">
        <input class="form-check-input m-0" type="checkbox" :id="id" :name="name" :checked="Boolean(modelValue)"
               :readonly="readonly"
               @change="$emit('update:modelValue', $event.target.checked)" v-bind="$attrs"/>
        <label class="form-check-label" :for="id">
            <slot/>
        </label>
    </div>
</template>
