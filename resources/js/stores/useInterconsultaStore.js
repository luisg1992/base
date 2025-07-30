import { defineStore } from 'pinia';
import { ref, toRaw } from 'vue';

export const useInterconsultaStore = defineStore('interconsultaStore', () => {
    const interconsultas = ref([]);
    const isInitialized = ref(false);

    const setInterconsultas = (items) => {
        interconsultas.value = items.map(item => ({ ...toRaw(item) }));
        isInitialized.value = true;
    };

    const agregarOActualizarInterconsulta = (item) => {
        const index = interconsultas.value.findIndex(f =>
            f.IdEspecialidad === item.IdEspecialidad &&
            f.IdAtencion === item.IdAtencion 
        );

        if (index !== -1) {
            Object.assign(interconsultas.value[index], toRaw(item));
        } else {
            interconsultas.value.push({ ...toRaw(item) });
        }
    };

    const eliminarInterconsulta = (IdEspecialidad, IdAtencion) => {
        interconsultas.value = interconsultas.value.filter(f =>
            !(
                f.IdEspecialidad === IdEspecialidad &&
                f.IdAtencion === IdAtencion 
            )
        );
    };

    const limpiarInterconsultas = () => {
        interconsultas.value = [];
        isInitialized.value = false;
    };

    return {
        interconsultas,
        isInitialized,
        setInterconsultas,
        agregarOActualizarInterconsulta,
        eliminarInterconsulta,
        limpiarInterconsultas
    };
});
