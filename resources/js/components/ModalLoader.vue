<script setup>
const props = defineProps({
    text: {
        type: String,
        default: 'Cargando',
    },
    textLoading: {
        type: String,
        default: 'Cargando',
    },
})
</script>

<template>
    <div class="modal d-flex align-items-center justify-content-center">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ text }}...</span>
            </div>
            <p class="mt-2">{{ textLoading }}...</p>
        </div>
    </div>
</template>
