<script setup>
import { usePage } from '@inertiajs/vue3'  // Asegúrate de importar 'router'
import { ref } from 'vue'
import { useAppStore } from '@/stores/useAppStore'
import { computed } from 'vue'

const appStore = useAppStore()

const user = computed(() => usePage().props.auth.user)
const menu = ref([])

</script>

<template>
<!--  <div class="layout-wrapper layout-content-navbar">-->
<!--    <div class="layout-container">-->
<!--      <div class="layout-page">-->
<!--        <div class="content-wrapper">-->
<!--          <div style="height: 100vh; width: 100%">-->
            <slot />
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
</template>
