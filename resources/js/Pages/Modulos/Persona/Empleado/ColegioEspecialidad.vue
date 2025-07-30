<script setup>
import {ref} from 'vue'
import BaseModal from '@/components/BaseModal.vue'

const props = defineProps(['especialidades'])
const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false);
let title = ref('');

const handleOpen = async () => {
    title.value = 'Listado';
}

const seleccionarEspecialidad = (especialidad) => {
    isDialogOpen.value = false;
    emit('success', especialidad);
}

const openDialog = () => {
    isDialogOpen.value = true;
}

defineExpose({openDialog})
</script>

<template>
    <BaseModal :isVisible="isDialogOpen"
               :header="title"
               @open="handleOpen"
               size="modal-md">
        <div class="row">
            <div class="col-12">
                <table class="table table-sm table-hover">
                    <thead>
                    <tr>
                        <th>Colegiatura</th>
                        <th>Estado</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(c, index) in especialidades">
                        <td>
                            {{ c.colegiatura }}
                        </td>
                        <td>
                            {{ c.estado }}
                        </td>
                        <td>
                            {{ c.codigo }}
                        </td>
                        <td>
                            {{ c.nombre }}
                        </td>
                        <td style="text-align: right">
                            <button @click="seleccionarEspecialidad(c)"
                                    class="btn btn-danger btn-sm">
                                <i class="ti ti-check"></i></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </BaseModal>
</template>
