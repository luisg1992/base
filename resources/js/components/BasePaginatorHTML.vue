<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
    currentPage: { type: [Number, String], required: true },
    totalRecords: { type: [Number, String], required: true },
    perPage: { type: [Number, String], required: true },
    totalPaginas: { type: [Number, String], required: true }
})

const emit = defineEmits(['cambiarPagina'])

function cambiarPagina(pagina) {
    if (pagina >= 1 && pagina <= props.totalPaginas) {
        emit('cambiarPagina', pagina)
    }
}
</script>


<template>
    <nav v-if="totalRecords > perPage">
        <ul class="pagination pagination-sm pagination-rounded pagination-outline-primary mb-0">
            <!-- Botón anterior -->
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="cambiarPagina(currentPage - 1)">
                    <i class="ti ti-chevron-left ti-sm"></i>
                </a>
            </li>

            <!-- Primera página -->
            <li class="page-item" :class="{ active: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="cambiarPagina(1)">1</a>
            </li>

            <!-- Página actual si no es la primera ni la última -->
            <li class="page-item" v-if="currentPage > 1 && currentPage < totalPaginas" :class="{ active: true }">
                <a class="page-link" href="#" @click.prevent="cambiarPagina(currentPage)">
                    {{ currentPage }}
                </a>
            </li>

            <!-- Última página -->
            <li v-if="totalPaginas > 1" class="page-item" :class="{ active: totalPaginas === currentPage }">
                <a class="page-link" href="#" @click.prevent="cambiarPagina(totalPaginas)">
                    {{ totalPaginas }}
                </a>
            </li>

            <!-- Botón siguiente -->
            <li class="page-item" :class="{ disabled: currentPage === totalPaginas }">
                <a class="page-link" href="#" @click.prevent="cambiarPagina(currentPage + 1)">
                    <i class="ti ti-chevron-right ti-sm"></i>
                </a>
            </li>
        </ul>
    </nav>
</template>