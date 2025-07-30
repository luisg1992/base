<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    currentPage: Number,
    lastPage: Number,
    from: Number,
    to: Number,
    total: Number,
    pageSizes: {
        type: Array,
        default: () => [10, 15, 20, 50, 100]
    },
    rowsPerPage: Number
});

const emit = defineEmits(['update:page', 'update:rowsPerPage']);

const selectedPageSize = ref(props.rowsPerPage);

// Actualizar cuando cambia desde el padre
watch(() => props.rowsPerPage, newVal => {
    selectedPageSize.value = newVal;
});

const onPageSizeChange = () => {
    emit('update:rowsPerPage', selectedPageSize.value);
    emit('update:page', 1); // Reiniciar a la página 1 cuando cambia el tamaño
};

const visiblePages = computed(() => {
    const total = props.lastPage;
    const current = props.currentPage;
    const maxPagesToShow = 5;

    let start = current - Math.floor(maxPagesToShow / 2);
    let end = current + Math.floor(maxPagesToShow / 2);

    if (start < 1) {
        start = 1;
        end = Math.min(maxPagesToShow, total);
    }

    if (end > total) {
        end = total;
        start = Math.max(1, end - maxPagesToShow + 1);
    }

    const pages = [];
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    return pages;
});
</script>

<template>
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center  gap-2">
    <!-- Combo de tamaño de página -->
    <div class="d-flex align-items-center gap-2">
      <span class="text-muted" style="font-size: 10px;">MOSTRAR</span>
      <select id="pageSize" class="form-select form-select-sm w-auto" v-model.number="selectedPageSize" @change="onPageSizeChange">
        <option v-for="size in pageSizes" :key="size" :value="size">{{ size }}</option>
      </select>
      <span class="text-muted" style="font-size: 10px;">REGISTROS</span>
    </div>

    <!-- Botonera -->
    <div class="demo-inline-spacing">
      <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm pagination-rounded pagination-outline-primary mb-0">
          <li class="page-item first" :class="{ disabled: currentPage <= 1 }">
            <a class="page-link" href="javascript:void(0);" @click="$emit('update:page', 1)">
              <i class="ti ti-chevrons-left ti-sm"></i>
            </a>
          </li>
          <li class="page-item prev" :class="{ disabled: currentPage <= 1 }">
            <a class="page-link" href="javascript:void(0);" @click="$emit('update:page', currentPage - 1)">
              <i class="ti ti-chevron-left ti-sm"></i>
            </a>
          </li>

          <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: currentPage === page }">
            <a class="page-link" href="javascript:void(0);" @click="$emit('update:page', page)">
              {{ page }}
            </a>
          </li>

          <li class="page-item next" :class="{ disabled: currentPage >= lastPage }">
            <a class="page-link" href="javascript:void(0);" @click="$emit('update:page', currentPage + 1)">
              <i class="ti ti-chevron-right ti-sm"></i>
            </a>
          </li>
          <li class="page-item last" :class="{ disabled: currentPage >= lastPage }">
            <a class="page-link" href="javascript:void(0);" @click="$emit('update:page', lastPage)">
              <i class="ti ti-chevrons-right ti-sm"></i>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>

  <!-- Detalle debajo -->
  <p class="text-muted text-sm detallePaginador mt-2 mb-0" style="font-size: 11px;">
    Página {{ currentPage }} de {{ lastPage }} mostrando del {{ from }} al {{ to }} de {{ total }} registros
  </p>
</template>
