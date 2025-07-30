import { useAppStore } from '@/stores/useAppStore';

export default {
  install(app) {  
    /**
     * Verifica si el usuario tiene un permiso específico.
     * @param {string} permiso - El nombre del permiso a verificar.
     * @returns {boolean} - true si el permiso existe en el store, false en caso contrario.
     */
    app.config.globalProperties.$can = (permiso) => {
      const appStore = useAppStore()
      const permisos = appStore.permission || []
      // Extraemos solo los nombres de los permisos para facilitar la comparación
      const nombresPermisos = permisos.map(p => p.name) 
      return nombresPermisos.includes(permiso)
    }

    /**
     * Verifica si el usuario tiene al menos uno de los permisos dados.
     * @param {string[]} permisos - Lista de permisos a verificar.
     * @returns {boolean} - true si al menos uno de los permisos está presente.
     */
    app.config.globalProperties.$canAny = (permisos = []) => {
      const appStore = useAppStore()
      const permisosGuardados = appStore.permission || []
      const nombresPermisos = permisosGuardados.map(p => p.name)
      // Retorna true si alguno de los permisos proporcionados está en los permisos del usuario
      return permisos.some(p => nombresPermisos.includes(p))
    }

    /**
     * Verifica si el usuario tiene todos los permisos dados.
     * @param {string[]} permisos - Lista de permisos a verificar.
     * @returns {boolean} - true solo si el usuario tiene *todos* los permisos especificados.
     */
    app.config.globalProperties.$canAll = (permisos = []) => {
      const appStore = useAppStore()
      const permisosGuardados = appStore.permission || []
      const nombresPermisos = permisosGuardados.map(p => p.name)
      // Retorna true solo si todos los permisos están en los permisos del usuario
      return permisos.every(p => nombresPermisos.includes(p))
    }
  }
}
