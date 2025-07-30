import { defineStore } from 'pinia';
import { ref, toRaw } from 'vue';

export const useProcedimientoStore = defineStore('procedimientoStore', () => {
    const procedimiento = ref([]);
    const isInitialized = ref(false);

    const setProcedimiento = (items) => {
        procedimiento.value = items.map(item => ({ ...toRaw(item) }));
        isInitialized.value = true;
    };

    const agregarOActualizarProcedimiento = (item) => {
        const index = procedimiento.value.findIndex(f =>
            f.IdDiagnostico === item.IdDiagnostico && f.IdProducto === item.IdProducto
        );

        if (index !== -1) {
            Object.assign(procedimiento.value[index], toRaw(item));
        } else {
            procedimiento.value.push({ ...toRaw(item) });
        }
    };

    const eliminarProcedimiento = (IdProducto, IdDiagnostico) => {
        procedimiento.value = procedimiento.value.filter(f =>
            !(f.IdProducto === IdProducto && f.IdDiagnostico === IdDiagnostico)
        );
    };

    const limpiarProcedimiento = () => {
        procedimiento.value = [];
        isInitialized.value = false;
    };

    return {
        procedimiento,
        isInitialized,
        setProcedimiento,
        agregarOActualizarProcedimiento,
        eliminarProcedimiento,
        limpiarProcedimiento
    };
});
