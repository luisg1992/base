<script setup>
import { ref } from 'vue'
import axios from 'axios'

import BaseModal from '@/components/BaseModal.vue'


// Props
let IdEspecialidad = ref(null)
let IdInterconsulta = ref(null)
let IdPaciente = ref(null)

const emit = defineEmits(['close', 'success'])

let isDialogOpen = ref(false)
let isModalLoading = ref(false)
let interconsultas = ref([])

const onChangeEspecialidadProximaCita = async () => {

    isModalLoading.value = true;
    interconsultas.value = [];

    try {
        const formData = {
            IdPaciente: IdPaciente.value,
            IdInterconsulta: IdInterconsulta.value
        };

        const { data } = await axios.post('/consulta-externa/citas/WebS_CitaProximaCE_BuscarFiltro', formData);
        if (data.length > 0) {
            interconsultas.value = data;
            isModalLoading.value = false;
        } else {
            let data = {
                success: false,
                IdPaciente: IdPaciente.value,
                IdInterconsulta: IdInterconsulta.value,
                IdEspecialidad: IdEspecialidad.value
            }
            emit('success', data);
            isModalLoading.value = false;
            isDialogOpen.value = false;
        }
    } catch (error) {
        showAlert("OPERACIÓN CANCELADA", error.message || 'Ocurrió un error', "warning");
    }
}


const generarCitaCitaProxima = async (itemInterconsulta) => {
    isModalLoading.value = true;
    let data = {
        success: true,
        IdInterconsulta: itemInterconsulta.IdCitaPorInterconsulta,
        IdEspecialidad: IdEspecialidad.value
    }
    emit('success', data);
    isModalLoading.value = false;
    isDialogOpen.value = false;
};


const openDialog = async (itemInterconsulta) => {
    isDialogOpen.value = true;
    interconsultas.value = [];
    IdEspecialidad.value = itemInterconsulta.IdEspecialidad
    IdPaciente.value = itemInterconsulta.IdPaciente
    IdInterconsulta.value = itemInterconsulta.IdInterconsulta
    await onChangeEspecialidadProximaCita()
}

defineExpose({ openDialog })
</script>

<template>
    <BaseModal :isVisible="isDialogOpen" size="modal-md" :loading="isModalLoading" @close="isDialogOpen = false"
        header="NUEVA CITA PRÓXIMA">

        <div class="row">
            <!-- Acordeón de resultados -->
            <div class="accordion" id="accordionResultados" v-if="IdEspecialidad">
                <template v-for="(inter, index) in interconsultas" :key="inter.IdCitaPorInterconsulta">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingInterconsulta">
                            <div class="accordion-button d-flex justify-content-between align-items-center"
                                style="cursor: default;">
                                <b class="text-primary" style="font-size: 0.80rem !important; margin: 0;">
                                    {{ inter.Servicio }} - {{ inter.FechaCitaPorInterconsulta }}
                                </b>
                            </div>
                        </h2>

                        <!-- Siempre visible, sin data-bs-parent -->
                        <div id="collapseInterconsulta" class="accordion-collapse collapse show"
                            aria-labelledby="headingInterconsulta">
                            <div class="accordion-body p-0">
                                <ul class="list-group mt-2">
                                    <li class="list-group-item"
                                        style="border: none; border-bottom: 1px solid #e6e6e8; font-size: 0.7rem !important; padding: 0.5rem 1.25rem !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="align-items-center">
                                                <b class="text-dark">
                                                    MÉDICO: {{ inter.Medico }}
                                                </b>

                                                <br>
                                                <b class="text-dark"> ORDEN DE LABORATORIO: </b>
                                                <span v-if="inter.Laboratorio == 1"> <b
                                                        class="text-danger">SI</b></span>
                                                <span v-else> <b class="text-danger">NO</b></span>

                                                <br>
                                                <b class="text-dark"> ORDEN DE IMAGEONOLOGÍA: </b>
                                                <span v-if="inter.Imagenes == 1"> <b class="text-danger">SI</b></span>
                                                <span v-else> <b class="text-danger">NO</b></span>

                                                <br>
                                                <b class="text-dark"> CITAS: </b>
                                                <span v-if="inter.Cita != 0"> <b class="text-danger">{{ inter.Cita
                                                }}</b></span>
                                                <span v-else> <b class="text-danger">NO</b></span>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-3" v-if="inter.Validar == 1">
                                            <button class="btn btn-success btn-sm w-100"
                                                @click="generarCitaCitaProxima(inter)">
                                                CITAR PACIENTE
                                            </button>
                                        </div>
                                        <div class="col-md-12 mt-3" v-else>
                                            <b class="text-danger">
                                                {{ inter.Mensaje }}
                                            </b>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </BaseModal>
</template>
