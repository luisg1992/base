import { defineStore } from 'pinia';
import { ref, toRaw } from 'vue';

export const useOrdenStore = defineStore('ordenStore', () => {
    const orden = ref([]);
    const isInitialized = ref(false);

    const setOrden = (items) => {
        orden.value = items.map(item => ({ ...toRaw(item) }));
        isInitialized.value = true;
    };

    const agregarOActualizarOrden = (item) => {
        const index = orden.value.findIndex(f =>
            f.IdProducto === item.IdProducto && f.IdPuntoCarga === item.IdPuntoCarga && f.IdCuentaAtencion === item.IdCuentaAtencion
        );

        if (index !== -1) {
            Object.assign(orden.value[index], toRaw(item));
        } else {
            orden.value.push({ ...toRaw(item) });
        }
    };

    const eliminarOrden = (IdProducto, IdDiagnostico) => {
        orden.value = orden.value.filter(f =>
            !(f.IdProducto === IdProducto && f.IdDiagnostico === IdDiagnostico)
        );
    };

    const limpiarOrden = () => {
        orden.value = [];
        isInitialized.value = false;
    };

    return {
        orden,
        isInitialized,
        setOrden,
        agregarOActualizarOrden,
        eliminarOrden,
        limpiarOrden
    };
});
