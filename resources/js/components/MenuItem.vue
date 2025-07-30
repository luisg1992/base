<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import {computed, ref, watch} from 'vue'
import MenuItem from './MenuItem.vue'

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

const isOpen = ref(false)
// Ruta actual (reactiva)
const currentRoute = computed(() => usePage().url)


// Usuario puede abrir/cerrar manualmente
const manuallyToggled = ref(false)

// Verifica si el ítem o uno de sus hijos está activo
const isActive = (menuItem) => {
    console.log('isActive')
    const current = currentRoute.value.replace(/\/$/, '')
    const target = `/${menuItem.Url}`.replace(/\/$/, '')

    if (target === current || current.startsWith(`${target}/`)) {
        return true
    }

    if (menuItem.has_children && Array.isArray(menuItem.children)) {
        return menuItem.children.some(child => isActive(child))
    }

    return false
}

const updateOpenState = () => {
    console.log('updateOpenState')
    const hasActiveChild = props.item.children?.some(child => isActive(child)) || false
    isOpen.value = isActive(props.item) || hasActiveChild
}

updateOpenState()

watch(
    () => usePage().url,
    () => {
        updateOpenState()
    }
)

// Clases para el <li>
const itemClasses = computed(() => {
    return {
        active: isActive(props.item),
        open: isOpen.value
    }
})

// Click del usuario
const toggleOpen = () => {
    console.log('toggleOpen')
    isOpen.value = !isOpen.value
}
</script>

<template>
    <li class="menu-item" :class="itemClasses">
        <!-- Ítem sin hijos -->
        <Link
            v-if="!item.has_children"
            :href="'/' + item.Url"
            class="menu-link"
        >
            <i
                v-if="item.Icono"
                :class="['menu-icon', 'tf-icons', 'ti', 'ti-' + item.Icono]"
            ></i>
            <div>{{ item.Etiqueta }}</div>
        </Link>

        <!-- Ítem con hijos -->
        <a
            v-else
            href="javascript:void(0);"
            class="menu-link menu-toggle"
            :aria-expanded="isOpen.value"
            @click="toggleOpen"
        >
            <i
                :class="['menu-icon', 'tf-icons', 'ti', 'ti-' + (item.Icono || 'settings')]"
            ></i>
            <div>{{ item.Etiqueta }}</div>
        </a>

        <!-- Submenú -->
        <ul v-if="item.has_children" class="menu-sub" :style="{ display: isOpen ? 'block' : 'none' }">

<!--        <ul v-if="item.has_children" class="menu-sub" v-show="isOpen.value">-->
<!--        <ul v-if="item.has_children" class="menu-sub" v-show="isOpen.value">-->
            <MenuItem
                v-for="child in item.children"
                :key="child.ModuloId"
                :item="child"
            />
        </ul>
    </li>
</template>
