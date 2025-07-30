import { defineStore } from 'pinia'
import axios from 'axios'
import MyLib from '../mixins/lib'
import { router, usePage } from '@inertiajs/vue3'

export const useAppStore = defineStore('app', {
    state: () => ({
        // menu: JSON.parse(localStorage.getItem('menu')) || [],
        // user: JSON.parse(localStorage.getItem('user')) || null,
        // permission: JSON.parse(localStorage.getItem('permission')) || null,
        menu: [],
        user: null,
        permission: null,

        catalogoServiciosEspecialidades: [],

        farmaciaAlmacenes: [],
        farmaciaTipoConceptos: [],
        farmaciaTipoDocumentos: [],
        farmaciaTipoProcesos: [],
        farmaciaTipoCompras: [],
        farmaciaEstadoMovimientos: [],

        configuracionDepartamentosHospital: [],
        configuracionTipoServicios: [],
        configuracionEspecialidades: [],
        configuracionEspecialidadesServicios: [],

        configuracionEspecialidadesPrimarias: [],
        configuracionUbicacionesFisicas: [],
        configuracionTipoEdades: [],
        configuracionTipoSexos: [],
        configuracionTipoTerapias: [],
        configuracionUpsServicios: [],
        configuracionSuSaludServicios: [],
        configuracionUpsRenaes: [],
        configuracionEstablecimientos: [],
        configuracionTipoLugaresLaborales: [],
        configuracionHisColegios: [],
        configuracionCategoria: [],
        configuracionCapitulos: [],
        configuracionGrupo: [],
        configuracionFormasIngreso: [],
        configuracionEstadosIngreso: [],
        configuracionUbigeos: [],
        configuracionCentrosPoblados: [],
        configuracionServicio: [],
        configuracionFuenteFinanciamiento: [],
        configuracionTipoConsultorios: [],
        configuracionTipoFinanciamiento: [],
        configuracionTipoFinanciador: [],
        configuracionFarmTipoConceptos: [],
        configuracionAreaTramitaSeguros: [],
        configuracionCajaTipoComprobante: [],
        citaDemandaInsatisfecha: [],

        coreParametros: [],

        programacionGeneralTipoTunos: [],
        personaTipoCargos: [],
        personaTipoEmpleados: [],
        personaTipoCondicionesTrabajo: [],
        personaTipoDocumentosIdentidad: [],
        personaTipoDestacados: [],
        personaEmpleadosUsuarioRoles: [],
        personaEmpleados: [],
        personaMedicos: [],
        personaTipoSexos: [],
        personaTipoEstadosCivil: [],
        personaTipoGradosInstruccion: [],
        personaTipoEtnias: [],
        personaTipoIdiomas: [],
        personaTipoReligiones: [],
        personaTipoOcupaciones: [],
        personaTipoProcedencias: [],
        seguridadRole: [],
        seguridadRoles: [],

        citasTipoEstadoInterconsulta: [],
        citaAtencionProximaEspecialidades: [],
        citaAtencionInterconsultaEspecialidades: [],
        citaAnuladaMotivo: [],

        configuracionEmergenciaFormasIngreso: [],
        configuracionEmergenciaEstadosIngreso: [],
        configuracionEmergenciaMotivoIngreso: [],
        configuracionEmergenciaPrioridad: [],
        programasInstituciones: [],
        programasTiposDocumentos: [],
        tablasCache: [],
        configuracionPuntoCarga: [],

        recetaCasificacionViaAdmin: [],
    }),
    actions: {
        setMenu(menu) {
            this.menu = menu
        },

        setUser(user) {
            this.user = user
        },

        setPermission(permission) {
            this.permission = permission
        },

        setTable(table, data) {
            this[table] = data;
        },

        clear() {
            this.menu = []
            this.user = null
            this.permission = null
        },

        async fetchTablas() {
            try {
                const response = await axios.get('/obtener_tablas')
                const data = response.data
                Object.entries(data).forEach(([key, value]) => {
                    const camelKey = MyLib.toCamelCase(key)
                    if (camelKey in this.$state) {
                        this[camelKey] = value
                    } else {
                        console.warn(`La clave '${camelKey}' no está definida en el state`)
                    }
                })
            } catch (error) {
                console.error('Error al obtener tablas:', error)
            }
        },

        async fetchCache() {
            try {
                const response = await axios.get('/obtener_cache')
                const data = response.data
                const newTablas = {}
                Object.entries(data).forEach(([key, value]) => {
                    const camelKey = MyLib.toCamelCase(key)
                    newTablas[camelKey] = value
                })

                this.tablasCache = newTablas

                // Object.entries(data).forEach(([key, value]) => {
                //     const camelKey = MyLib.toCamelCase(key)
                //     if (camelKey in this.$state) {
                //         this[camelKey] = value
                //         // localStorage.setItem(camelKey, JSON.stringify(value))
                //     } else {
                //         console.warn(`La clave '${camelKey}' no está definida en el state`)
                //     }
                // })
            } catch (error) {
                console.error('Error al obtener tablas:', error)
            }
        },

        async fetchTabla(camelKey) {
            try {
                const snakeCase = MyLib.toSnakeCase(camelKey)
                const response = await axios.get(`/obtener_tablas/${snakeCase}`)

                if (camelKey in this.$state) {
                    const data = response.data;
                    this[camelKey] = data;
                    // localStorage.setItem(camelKey, JSON.stringify(data))
                } else {
                    console.warn(`La clave '${camelKey}' no está definida en el state`)
                }
            } catch (error) {
                console.error('Error al obtener tablas:', error)
            }
        },

        async getUserLogin() {
            try {
                const response = await axios.get('/obtener_user_login')
                const data = response.data
                if (data.success) {
                    this.menu = data.data.menu;
                    this.user = data.data.user;
                    this.permission = data.data.permission;
                } else {
                    this.logout();
                }
            } catch (error) {
                console.error('Error al obtener tablas:', error)
            }
        },

        logout() {
            this.clear()
            router.post('/logout')
        }

    }
})
