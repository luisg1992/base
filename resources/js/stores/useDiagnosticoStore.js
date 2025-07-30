import { defineStore } from 'pinia';
import { ref, computed, toRaw } from 'vue';

export const useDiagnosticoStore = defineStore('diagnosticoStore', () => {

    const diagnosticos = ref([]);
    const isInitialized = ref(false); // 🔑 Control de inicialización

    const setDiagnosticos = (items) => {
        diagnosticos.value = items.map(item => ({ ...toRaw(item) }));
        isInitialized.value = true; // 🚩 Marca que ya se cargó la lista
    };

    const agregarOActualizarDiagnostico = (item) => {
        const index = diagnosticos.value.findIndex(f => f.IdDiagnostico === item.IdDiagnostico);
        if (index !== -1) {
            Object.assign(diagnosticos.value[index], toRaw(item));
        } else {
            diagnosticos.value.push({ ...toRaw(item) });
        }
    };

    const eliminarDiagnostico = (IdDiagnostico) => {
        diagnosticos.value = diagnosticos.value.filter(f => f.IdDiagnostico !== IdDiagnostico);
    };

    const limpiarDiagnosticos = () => {
        diagnosticos.value = [];
        isInitialized.value = false; // Reinicia el flag
    };

    const diagnosticoOptions = computed(() =>
        diagnosticos.value.map(d => ({
            label: d.DiagnosticoNombre,
            value: parseInt(d.IdDiagnostico),
            CodigoCIE10: d.CodigoCIE10
        }))
    );

    return {
        diagnosticos,
        diagnosticoOptions,
        setDiagnosticos,
        agregarOActualizarDiagnostico,
        eliminarDiagnostico,
        limpiarDiagnosticos,
        isInitialized
    };
});
