import { defineStore } from 'pinia';
import { ref, toRaw } from 'vue';

export const useFarmaciaStore = defineStore('farmaciaStore', () => {
    const farmacia = ref([]);
    const isInitialized = ref(false);

    const setFarmacia = (items) => {
        farmacia.value = items.map(item => ({ ...toRaw(item) }));
        isInitialized.value = true;
    };

    const agregarOActualizarFarmacia = (item) => {
        const index = farmacia.value.findIndex(f =>
            f.IdDiagnostico === item.IdDiagnostico && f.IdProducto === item.IdProducto
        );

        if (index !== -1) {
            Object.assign(farmacia.value[index], toRaw(item));
        } else {
            farmacia.value.push({ ...toRaw(item) });
        }
    };

    const eliminarFarmacia = (IdProducto, IdDiagnostico) => {
        farmacia.value = farmacia.value.filter(f =>
            !(f.IdProducto === IdProducto && f.IdDiagnostico === IdDiagnostico)
        );
    };

    const limpiarFarmacia = () => {
        farmacia.value = [];
        isInitialized.value = false;
    };

    return {
        farmacia,
        isInitialized,
        setFarmacia,
        agregarOActualizarFarmacia,
        eliminarFarmacia,
        limpiarFarmacia
    };
});
