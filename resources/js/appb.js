import '../assets/vendor/libs/jquery/jquery.js';
import '../assets/vendor/js/bootstrap.js';
import '../assets/vendor/libs/popper/popper.js';
import '../assets/vendor/libs/node-waves/node-waves.js';

import '../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js';
import '../assets/vendor/libs/hammer/hammer.js';
import '../assets/vendor/libs/i18n/i18n.js';
import '../assets/vendor/libs/typeahead-js/typeahead.js';
import '../assets/vendor/js/menu.js';

import '../assets/vendor/libs/moment/moment.js';
import '../assets/vendor/libs/select2/select2.js';
import '../assets/vendor/libs/cleavejs/cleave.js';
import '../assets/vendor/libs/cleavejs/cleave-phone.js';

import '../assets/vendor/libs/@form-validation/popular.js';
import '../assets/vendor/libs/@form-validation/bootstrap5.js';
import '../assets/vendor/libs/@form-validation/auto-focus.js';

import '../assets/js/pages-auth.js';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createHead } from '@vueuse/head';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ModalLoader from '@/components/ModalLoader.vue';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Toast from 'primevue/toast';
import Aura from '@primeuix/themes/aura';
import { createPinia } from 'pinia'

// Importa las funciones que quieres exponer globalmente
import { showAlert, showAlertConfirmacion, showMultiplesOpciones } from './utils/alert';
import { getTriajeParametros } from './utils/TriajeParametros';
import permissionPlugin from './utils/permission';

// === ASIGNACIONES GLOBALES ANTES DE INICIALIZAR LA APP ===
window.showAlert = showAlert;
window.showAlertConfirmacion = showAlertConfirmacion;
window.showMultiplesOpciones = showMultiplesOpciones;
window.getTriajeParametros = getTriajeParametros;

// Función para inicializar el menú
function initMenu() {
    let layoutMenuEl = document.querySelectorAll('#layout-menu');
    if (layoutMenuEl.length > 0) {
        layoutMenuEl.forEach(function (element) {
            menu = new Menu(element, {
                orientation: isHorizontalLayout ? 'horizontal' : 'vertical',
                closeChildren: isHorizontalLayout ? true : false,
                showDropdownOnHover: localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover')
                    ? localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover') === 'true'
                    : window.templateCustomizer !== undefined
                        ? window.templateCustomizer.settings.defaultShowDropdownOnHover
                        : true
            });

            window.Helpers.scrollToActive(false);
            window.Helpers.mainMenu = menu;
        });
    }
}

// Función para inicializar los togglers de menú
function initMenuToggles() {
    let menuToggler = document.querySelectorAll('.layout-menu-toggle');
    if (menuToggler.length > 0) {
        menuToggler.forEach(item => {
            item.addEventListener('click', event => {
                event.preventDefault();
                window.Helpers.toggleCollapsed();

                if (config.enableMenuLocalStorage && !window.Helpers.isSmallScreen()) {
                    try {
                        localStorage.setItem(
                            'templateCustomizer-' + templateName + '--LayoutCollapsed',
                            String(window.Helpers.isCollapsed())
                        );

                        let layoutCollapsedCustomizerOptions = document.querySelector('.template-customizer-layouts-options');
                        if (layoutCollapsedCustomizerOptions) {
                            let layoutCollapsedVal = window.Helpers.isCollapsed() ? 'collapsed' : 'expanded';
                            layoutCollapsedCustomizerOptions.querySelector(`input[value="${layoutCollapsedVal}"]`).click();
                        }
                    } catch (e) {
                    }
                }
            });
        });
    }
}

// Función para manejar el gesto de deslizamiento (swipe gesture)
function initSwipeGestures() {
    // Detect swipe gesture on the target element and call swipe In
    window.Helpers.swipeIn('.drag-target', function (e) {
        window.Helpers.setCollapsed(false);
    });

    // Detect swipe gesture on the target element and call swipe Out
    window.Helpers.swipeOut('#layout-menu', function (e) {
        if (window.Helpers.isSmallScreen()) window.Helpers.setCollapsed(true);
    });
}

// Función para actualizar la imagen según el estilo
function switchImage(style) {
    if (style === 'system') {
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            style = 'dark';
        } else {
            style = 'light';
        }
    }
    const switchImagesList = [].slice.call(document.querySelectorAll('[data-app-' + style + '-img]'));
    switchImagesList.map(function (imageEl) {
        const setImage = imageEl.getAttribute('data-app-' + style + '-img');
        imageEl.src = assetsPath + 'img/' + setImage; // Using window.assetsPath to get the exact relative path
    });
}

// Función para manejar el cambio de estilo (modo claro, oscuro, automático)
function initStyleSwitcher() {
    let styleSwitcher = document.querySelector('.dropdown-style-switcher');
    const activeStyle = document.documentElement.getAttribute('data-style');

    // Obtener el estilo desde localStorage o usar 'system' como predeterminado
    let storedStyle =
        localStorage.getItem('templateCustomizer-' + templateName + '--Style') ||
        (window.templateCustomizer?.settings?.defaultStyle ?? 'light');

    if (window.templateCustomizer && styleSwitcher) {
        let styleSwitcherItems = [].slice.call(styleSwitcher.children[1].querySelectorAll('.dropdown-item'));
        styleSwitcherItems.forEach(function (item) {
            item.classList.remove('active');
            item.addEventListener('click', function () {
                let currentStyle = this.getAttribute('data-theme');
                if (currentStyle === 'light') {
                    window.templateCustomizer.setStyle('light');
                } else if (currentStyle === 'dark') {
                    window.templateCustomizer.setStyle('dark');
                } else {
                    window.templateCustomizer.setStyle('system');
                }
            });

            if (item.getAttribute('data-theme') === activeStyle) {
                item.classList.add('active');
            }
        });

        // Actualizamos el ícono según el estilo almacenado
        const styleSwitcherIcon = styleSwitcher.querySelector('i');
        if (storedStyle === 'light') {
            styleSwitcherIcon.classList.add('ti-sun');
            new bootstrap.Tooltip(styleSwitcherIcon, {
                title: 'Light Mode',
                fallbackPlacements: ['bottom']
            });
        } else if (storedStyle === 'dark') {
            styleSwitcherIcon.classList.add('ti-moon-stars');
            new bootstrap.Tooltip(styleSwitcherIcon, {
                title: 'Dark Mode',
                fallbackPlacements: ['bottom']
            });
        } else {
            styleSwitcherIcon.classList.add('ti-device-desktop-analytics');
            new bootstrap.Tooltip(styleSwitcherIcon, {
                title: 'System Mode',
                fallbackPlacements: ['bottom']
            });
        }
    }

    // Ejecutamos la función switchImage basada en el estilo almacenado
    switchImage(storedStyle);
}

// Escuchar el evento inertia:finish para reejecutar la inicialización después de cada navegación
document.addEventListener('inertia:finish', () => {
    initMenu(); // Re inicializamos el menú
    initMenuToggles(); // Re inicializamos los togglers
    initSwipeGestures(); // Re inicializamos los gestos de deslizamiento
    // initStyleSwitcher(); // Re inicializamos el selector de estilo
});


// Inicializar la app Vue/Inertia
createInertiaApp({
    resolve: name =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        const head = createHead();

        const pinia = createPinia()
        app.use(pinia)

        app.use(plugin)
            .use(PrimeVue, {
                theme: {
                    preset: Aura
                }
            })
            .use(ConfirmationService)
            .use(ToastService)
            .component('ModalLoader', ModalLoader)
            .component('Toast', Toast)
            .use(head)
            .use(permissionPlugin)
            .mount(el)

        initMenu();
        initMenuToggles();
        initSwipeGestures();
        // initStyleSwitcher();
    }
});
