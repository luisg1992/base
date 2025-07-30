<script setup>
import {computed} from 'vue'
import {usePage} from '@inertiajs/vue3'

const user = computed(() => usePage().props.auth.user)
</script>

<template>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="mb-3">Bienvenido(a), {{ user.name }} 👋🏻</h5>
            <p class="mb-2">
                Le damos la bienvenida al <strong>WEB GALEN</strong>, sistema de gestión hospitalaria del <strong>Hospital
                Nacional Dos de Mayo</strong>.
            </p>
        </div>
    </div>
</template>
