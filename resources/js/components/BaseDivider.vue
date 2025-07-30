<template>
    <div class="divider text-start">
        <div class="divider-text text-primary"><b>{{ title }}</b></div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true
    }
})
</script>