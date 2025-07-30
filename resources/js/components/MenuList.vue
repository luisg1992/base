<script setup>
import MenuItem from '@/components/MenuItem.vue';

const props = defineProps({
    menu: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <ul class="menu-inner py-1">
        <MenuItem v-for="item in menu" :key="item.id" :item="item" />
    </ul>
</template>
