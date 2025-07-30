<script setup>
import {ref, computed} from 'vue';
import Button from 'primevue/button';
import Menu from 'primevue/menu';

const props = defineProps({
    record: Object,
});

const emit = defineEmits(['action-selected']);

const menu = ref(null);
const currentRecord = ref(null);

const menuItems = computed(() => {
    if (!currentRecord.value) return [];
    return currentRecord.value.actions.map(action => ({
        label: action.label,
        icon: action.icon,
        command: () => seleccionarAction(action, currentRecord.value),
    }));
});

function toggleMenu(event, record) {
    currentRecord.value = record;
    menu.value.toggle(event);
}

function seleccionarAction(action, record) {
    emit('action-selected', action, record);
}
</script>

<template>
    <div>
        <Button icon="ti ti-dots-vertical"
                severity="secondary"
                rounded
                class="p-0"
                @click="toggleMenu($event, record)"
                aria-haspopup="true"
                variant="text"
                aria-controls="overlay_menu"
                style="width: 30px!important; height: 30px!important;"/>
        <Menu ref="menu" :model="menuItems" :popup="true"/>
    </div>
</template>
