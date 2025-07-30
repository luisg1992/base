import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createHead } from '@vueuse/head';
import ModalLoader from '@/components/ModalLoader.vue';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import Aura from '@primeuix/themes/aura';
import { createPinia } from 'pinia'

import DefaultLayout from '@/Layouts/AppLayout.vue'
import AppFullLayout from '@/Layouts/AppFullLayout.vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { getTriajeParametros } from './utils/TriajeParametros';
import permissionPlugin from './utils/permission';

window.getTriajeParametros = getTriajeParametros;

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import ToastPlugin from 'vue-toast-notification';
// import 'vue-toast-notification/dist/theme-default.css';

import {setAlertInstance, showToastSuccess, showToastError, showToastInfo, showToastWarning ,
        showSuccess, showError, showInfo,
        showAlert, showAlertConfirmacion, showMultiplesOpciones} from './utils/alert';

createInertiaApp({
    resolve: async name => {
        console.log(name);
        const pages = import.meta.glob('./Pages/**/*.vue')
        const page = await pages[`./Pages/${name}.vue`]()

        // Detecta qué layout usar por convención de nombre
        let layout = DefaultLayout
        if (name.startsWith('Auth/')) {
            layout = AuthLayout
        } else if (name.startsWith('Modulos/Core/PubVisualizar')) {
            layout = AppFullLayout
        }

        // Aplica layout persistente vía render function
        page.default.layout = layout; // (h, pageComponent) => h(layout, null, () => pageComponent)

        return page
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        const head = createHead();

        const pinia = createPinia()

        app.use(pinia)
        app.use(plugin)
        // ✅ Primero PrimeVue
        app.use(PrimeVue, { theme: { preset: Aura } })
        // ✅ Después ConfirmationService y ToastService
        app.use(ConfirmationService)
        // ✅ Luego el resto
        app.use(VueSweetalert2)
        app.use(ToastPlugin)
        app.use(head)
        app.use(permissionPlugin)

        // ✅ Registra componentes ANTES de mount
        // app.component('Toast', Toast)
        app.component('ModalLoader', ModalLoader)

        // setSwalInstance(app.config.globalProperties.$swal)
        setAlertInstance(app.config.globalProperties.$swal, app.config.globalProperties.$toast)

        window.showToastSuccess = showToastSuccess;
        window.showToastError = showToastError;
        window.showToastInfo = showToastInfo;
        window.showToastWarning = showToastWarning;
        window.showSuccess = showSuccess;
        window.showError = showError;
        window.showInfo = showInfo;
        window.showAlert = showAlert;
        window.showAlertConfirmacion = showAlertConfirmacion;
        window.showMultiplesOpciones = showMultiplesOpciones;

        app.mount(el)
    }
});
