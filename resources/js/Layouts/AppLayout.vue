<script setup>
import {defineAsyncComponent} from 'vue'
import {usePage} from '@inertiajs/vue3'
import {ref, onMounted} from 'vue'
import {useAppStore} from '@/stores/useAppStore'
import {computed} from 'vue'
import Avatar from 'primevue/avatar';
import Menu from 'primevue/menu';
import ToggleButton from 'primevue/togglebutton';
import InertiaPanelMenu from "../components/InertiaPanelMenu.vue";
import WButton from "../components/WButton/WButton.vue";
import Popover from "primevue/popover";
import axios from "axios";
import {showToastSuccess} from "../utils/alert.js";

const PasswordChange = defineAsyncComponent(() => import('@/pages/Auth/PasswordChange.vue'))
const appStore = useAppStore()

const user = computed(() => usePage().props.auth.user)
const menu = ref();
const checked = ref(false);
const menuServices = ref();
let refDialogPasswordChange = ref();

const clickPasswordChange = () => {
    refDialogPasswordChange.value.openDialog();
}

const items = ref([
    {
        label: 'Cambio de contraseña',
        value: 'password-change',
        icon: 'ti ti-key'
    },
    {
        label: 'Cerrar sesión',
        value: 'logout',
        icon: 'ti ti-logout'
    }
]);

const services = ref([]);

const toggleServices = (event) => {
    menuServices.value.toggle(event);
};

const clickAction = (action) => {
    if (action === 'password-change') {
        clickPasswordChange()
    }
    if (action === 'logout') {
        appStore.logout()
    }
};

const toggle = (event) => {
    menu.value.toggle(event);
}

const getToggleServices = async () => {
    const {data} = await axios.get(`/core/parametros/obtener_servicios_consultar`);
    services.value = data;
}

const changeToggleServices = async (data) => {
    const response = await axios.post(`/core/parametros/actualizar`, {
        'codigo': data.value,
        'valorTexto': data.checked ? 'S' : 'N'
    })
    services.value = response.data.data.parametros;
    showToastSuccess('Actualización satisfactoria', 'Servicios actualizados correctamente.')
}

function getToggleStyle(service) {
    if (!service.checked) {
        return {
            color: '#ff0000',
            backgroundColor: 'transparent',
            border: 'none',
            boxShadow: 'none',
            width: '100%',
        };
    }
    return {
        backgroundColor: 'transparent',
        border: 'none',
        boxShadow: 'none',
        width: '100%',
    };
}

onMounted(async () => {
    await appStore.fetchTablas()
    await appStore.fetchCache()
    await appStore.getUserLogin()
    await getToggleServices()
})

</script>

<template>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="#" class="app-brand-link">
            <span class="app-brand-logo demo">
              <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                      fill="#7367F0"/>
                <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                      d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616"/>
                <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                      d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616"/>
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                      fill="#7367F0"/>
              </svg>
            </span>
                        <span class="app-brand-text demo menu-text fw-bold">WEB GALEN</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
                        <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <InertiaPanelMenu :items="appStore.menu" style="padding: 0 8px"/>
            </aside>

            <div class="layout-page">
                <!-- Navbar -->
                <nav
                    class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="ti ti-menu-2 ti-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item navbar-search-wrapper mb-0">
                                <!-- Texto en la esquina derecha (solo en pantallas grandes) -->
                                <span class="ms-auto me-3 d-none d-md-block" style="font-size: 0.8rem;">
                  ESTABLECIMIENTO PRINCIPAL<br><b class="text-primary">HOSPITAL NACIONAL DOS DE MAYO</b>
                </span>
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li  style="margin-right: 5px;" v-if="appStore.user && appStore.user.isAdmin">
                                <w-button @click="toggleServices" rounded variant="outlined" icon="ti ti-settings">
                                </w-button>
                                <Popover ref="menuServices" class="w-popover-services" style="width: 265px!important;">
                                    <div class="flex flex-col gap-4">
                                        <ToggleButton
                                            v-for="service in services"
                                            v-model="service.checked"
                                            :key="service.value"
                                            :onLabel="service.onLabel"
                                            :offLabel="service.offLabel"
                                            onIcon="ti ti-check"
                                            offIcon="ti ti-x"
                                            @change="changeToggleServices(service)"
                                            :style="getToggleStyle(service)"
                                        />
                                    </div>
                                </Popover>
                            </li>
                            <!-- User -->
                            <li class="d-flex align-items-center">
                                <Avatar :image="user.avatar" class="mr-2" size="large" @click="toggle" shape="circle"
                                        style="cursor: pointer;" aria-haspopup="true" aria-controls="overlay_menu"/>
                                <Menu ref="menu" id="overlay_menu" :model="items" :popup="true">
                                    <template #item="{ item, props }">
                                        <a class="flex items-center" v-bind="props.action"
                                           @click="clickAction(item.value)">
                                            <span :class="item.icon"/>
                                            <span>{{ item.label }}</span>
                                        </a>
                                    </template>
                                </Menu>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>

                </nav>
                <!-- / Navbar -->

                <div class="content-wrapper">

                    <div class="flex-grow-1 container-p-y container-fluid">
                        <slot/>
                    </div>

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-fluid">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                                <div style="font-size: 13px;">
                                    © {{ user.anioActual }}, hecho con ❤️ por
                                    <a class="footer-link text-primary fw-medium" href="javascript:void(0);">
                                        <b>WEB GALEN</b>
                                    </a>
                                </div>
                                <div class="d-none d-lg-inline-block" style="text-align: right">
                                    <a class="footer-link me-4" href="javascript:void(0);">
                                        <b>Derechos reservados por WEB GALEN</b><br>
                                        <b class="text-primary">{{ user.TerminalLogin }}: {{ user.IpTerminalLogin }}
                                        </b>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>

            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>
        </div>

        <password-change ref="refDialogPasswordChange"></password-change>
    </div>
</template>
