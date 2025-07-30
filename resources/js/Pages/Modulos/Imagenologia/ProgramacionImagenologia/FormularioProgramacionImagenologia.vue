<script	setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import BaseModal from '@/components/BaseModal.vue'
import BaseInput from '@/components/BaseInput.vue'
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    recordId: Number,
    puntoCargaId: Number,
    viewRecord: Boolean,
    resource: String,
    fecha: Date
})

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false);
let isSaving = ref(false);
let isViewMode = computed(() => props.viewRecord)
let errors = ref({});
const form = useForm({
    Cupos: 1
})

const openDialog = () => {
    isDialogOpen.value = true;
}

const guardar = async () => {
    isSaving.value = true;

    //Contruimos la data para guardar
    const formData = {
        id: props.recordId,
        IdPuntoCarga: props.puntoCargaId,
        Cupos: form.Cupos,
        Fecha: props.fecha
    }

    try {
        const { data } = await axios.post('/imagenologia/programacion-imagenologia/WebS_InsertarProgramacionImagenologia', formData);
        if (data.success) {
             isSaving.value = false;
                emit('success');
                isDialogOpen.value = false;

                const accion = props.recordId ? 'ACTUALIZADO' : 'REALIZADO';
                const mensaje = `EL REGISTRO FUE ${accion} DE FORMA EXITOSA`;
                showAlert('PROCESO REALIZADO EXITOSAMENTE', mensaje, 'success');
        } else {
            
            showAlert("OPERACIÓN CANCELADA", data.Mensaje, "warning", false, true);
        }
    } catch (error) {
        //isModalLoading.value = false;
        showAlert("OPERACIÓN CANCELADA", error.message || 'Ocurrió un error', "warning");
    }

    //isSaving.value = false;
    ///isDialogOpen.value = false;
}

const handleOpen = async () => {
    form.reset()
    form.clearErrors()

    if (props.recordId) {
        try {
            const {data} = await axios.get(`/${props.resource}/record/${props.recordId}`)
            Object.assign(form, data.data)
        }catch (error) {
            if (error.status === 403){
                isDialogOpen.value = false;
            }
        }finally {
            isDialogOpen.value = true;
        }
    }else {
        isDialogOpen.value = true;
    }
}

defineExpose({openDialog})

</script>

<template>
    <BaseModal
        :isVisible="isDialogOpen"
        @close="isDialogOpen = false"
        size="modal-md"
        @open="handleOpen"
    >
        <form @submit.prevent="guardar">
            <div class="row">
                 <div class="col-md-4 mb-2">
                    <BaseInput v-model="form.Cupos" label="Cupos" type="number" placeholder="Ingrese Cupos"
                               :disabled="isViewMode" :error="errors.Cupos" required/>
                </div>
            </div>
            <div v-if="!isViewMode" class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary" :disabled="isSaving">
                    {{ isSaving ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </BaseModal>
</template>