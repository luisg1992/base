import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCabeceraStore = defineStore('cabecera', () => {
  const cabecera = ref({})

  function setCabecera(data) {
    cabecera.value = { ...data }
  }

  function updateCabeceraCampos(camposActualizados) {
    cabecera.value = {
      ...cabecera.value,
      ...camposActualizados
    }
  }

  function limpiarCabecera() {
    cabecera.value = {}
  }

  return {
    cabecera,
    setCabecera,
    updateCabeceraCampos,
    limpiarCabecera
  }
})
