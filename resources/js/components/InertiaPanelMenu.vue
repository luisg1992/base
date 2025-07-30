<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import InertiaPanelMenu from './InertiaPanelMenu.vue'

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    level: {
        type: Number,
        default: 0
    }
})

const currentUrl = computed(() => usePage().url.replace(/\/$/, ''))

const isActive = (item) => {
    const target = `/${item.to}`.replace(/\/$/, '')
    if (target === currentUrl.value || currentUrl.value.startsWith(`${target}/`)) {
        return true
    }

    if (item.children && item.children.length > 0) {
        return item.children.some(isActive)
    }

    return false
}

const closeAllExcept = (items, exception) => {
    items.forEach(item => {
        if (item !== exception) {
            item._open = false
            item._manual = false
        }
        if (item.children) {
            closeAllExcept(item.children, null)
        }
    })
}

const toggleItem = (clickedItem) => {
    closeAllExcept(props.items, clickedItem)
    clickedItem._manual = true
    clickedItem._open = !clickedItem._open
}
</script>

<template>
    <ul :class="['inertia-menu', `level-${level}`]">
        <li
            v-for="(item, index) in items"
            :key="item.label"
            :class="[
                'my-menu-item',
                { 'is-active': isActive(item), 'has-children': item.children?.length },
                item.children?.length ? 'my-menu-parent' : 'my-menu-leaf',
                index > 0 ? 'my-menu-separated' : ''
            ]"
        >
            <!-- Item con hijos -->
            <template v-if="item.children && item.children.length">
                <div
                    class="my-menu-toggle my-menu-hover-parent"
                    @click="toggleItem(item)"
                >
                    <div class="my-menu-content">
                        <i class="ti ti-point" v-if="level >= 1" />
                        <i v-if="item.icon" :class="['my-menu-icon', item.icon]" />
                        <span>{{ item.label }}</span>
                    </div>
                    <span class="my-icon">
                        <template v-if="item._open || (!item._manual && isActive(item))">
                            <i class="ti ti-chevron-down" />
                        </template>
                        <template v-else>
                            <i class="ti ti-chevron-right" />
                        </template>
                    </span>
                </div>

                <Transition name="submenu">
                    <div v-show="item._open || (!item._manual && isActive(item))" class="my-submenu-container">
                        <InertiaPanelMenu :items="item.children" :level="level + 1" />
                    </div>
                </Transition>
            </template>

            <!-- Item sin hijos -->
            <Link v-else class="my-menu-link my-menu-hover-leaf" :href="`/${item.to}`">
                <i class="ti ti-point" v-if="level >= 1" />
                <i v-if="item.icon" :class="['my-menu-icon', item.icon]" />
                <span>{{ item.label }}</span>
            </Link>
        </li>
    </ul>
</template>

<style scoped>
.inertia-menu {
    list-style: none;
    padding-left: 0;
    color: #6f6b7d;
}

.my-menu-item {
    transition: background-color 0.2s;
    margin-bottom: 6px; /* separación entre todos los items */
}

.my-submenu-container {
    margin-top: 4px; /* separación entre padre y primer hijo */
}

.my-menu-toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1em;
    cursor: pointer;
    border-radius: 6px;
    height: 36px;
    font-size: 14px!important;
    text-transform: none!important;
}

.my-menu-link {
    display: flex;
    align-items: center;
    gap: 0.3em;
    padding: 0 6px 0 12px;
    cursor: pointer;
    border-radius: 6px;
    height: 36px;
    color: #6f6b7d;
    font-size: 14px!important;
    text-transform: none!important;
}

.my-menu-content {
    display: flex;
    align-items: center;
    gap: 0.3em;
    flex-grow: 1;
}

.my-menu-icon {
    font-size: 1.4rem;
}

.is-active > .my-menu-link,
.is-active > .my-menu-toggle {
    background: linear-gradient(270deg, #7367f0b3, #7367f0);
    box-shadow: 0 2px 6px #7367f04d;
    color: #fff !important;
}

.my-icon {
    transition: transform 0.4s ease;
    display: inline-block;
    width: 1em;
    text-align: center;
    font-size: 0.9em;
    margin-left: 0.5em;
}

.level-1 {
    padding-left: 0.5rem;
}
.level-2 {
    padding-left: 0.5rem;
}

/* Transición de submenús más suave */
.submenu-enter-active,
.submenu-leave-active {
    transition: max-height 0.4s ease, opacity 0.4s ease, transform 0.4s ease;
    overflow: hidden;
}
.submenu-enter-from,
.submenu-leave-to {
    opacity: 0;
    transform: translateY(-10px);
    max-height: 0;
}
.submenu-enter-to,
.submenu-leave-from {
    opacity: 1;
    transform: translateY(0);
    max-height: 999px;
}

/* Hover Styles */
.my-menu-hover-parent:hover {
    background-color: #2f2b3d0f;
}
.my-menu-hover-leaf:hover {
    background-color: #2f2b3d0f;
}
</style>
