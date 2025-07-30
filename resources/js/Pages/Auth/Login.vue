<script setup>
import {router as inertiaRouter, usePage} from '@inertiajs/vue3';
import BaseInput from "../../components/WInput/WInput.vue";
import WCheckbox from "../../components/WCheckbox/WCheckbox.vue";
import {useAppStore} from '@/stores/useAppStore';
import Message from 'primevue/message';
import axios from 'axios';
import {onMounted, ref} from "vue";
import PasswordRecovery from "./PasswordRecovery.vue";
import WButton from "../../components/WButton/WButton.vue";
import WInput from "../../components/WInput/WInput.vue";

const appStore = useAppStore();

let isModalLoading = ref(false);
let errorMensaje = ref(null);
let refDialogPasswordRecovery = ref();
let errors = ref({});

const form = ref({
    email: '',
    password: '',
    remember: false
});

// Leer errores enviados desde Laravel/Inertia
const {props} = usePage();
onMounted(() => {
    if (props.errors) {
        errors.value = props.errors;
        errorMensaje.value = props.errors.message ?? null;
    }
});

const clickPasswordRecovery = () => {
    refDialogPasswordRecovery.value.openDialog();
}

const submit = async () => {
    localStorage.clear();
    isModalLoading.value = true;
    errorMensaje.value = null;
    errors.value = {};

    try {
        const response = await axios.post('/login', form.value);
        const res = response.data;

        if (res.success) {
            localStorage.clear();
            appStore.fetchTablas();
            appStore.fetchCache();
            appStore.getUserLogin();

            try {
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = 'print://' + res.data.externalId;
                document.body.appendChild(iframe);
            } catch (error) {
                console.error('Error al intentar imprimir con iframe:', error);
            }

            // Esperar 3 segundos
            await new Promise(resolve => setTimeout(resolve, 3000));
            isModalLoading.value = false;

            // Redirigir al dashboard
            inertiaRouter.visit('/dashboard');
        } else {
            isModalLoading.value = false;
            errorMensaje.value = res.message;
        }

    } catch (error) {
        isModalLoading.value = false;
        errors.value = error.response?.data?.errors || {};
    }
};

</script>

<template>
    <ModalLoader v-if="isModalLoading"/>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <a href="#" class="app-brand-link">
                                <span class="app-brand-text demo text-heading fw-bold">WEB GALEN</span>
                            </a>
                        </div>

                        <h4 class="mb-1">¡Bienvenido a WEB GALEN! 👋</h4>
                        <p class="mb-6">Inicia sesión en tu cuenta y comienza la aventura.</p>
                        <Message severity="error" v-if="errorMensaje" class="mb-2">{{ errorMensaje }}</Message>

                        <form @submit.prevent="submit" autocomplete="off">
                            <div class="row">
                                <div class="col-12">
                                    <w-input label="Usuario"
                                             placeholder="Enter your email or username"
                                             v-model="form.email"
                                             :error="errors.email"
                                             :autofocus="true"
                                             :autocomplete="false"/>
                                </div>
                                <div class="col-12">
                                    <w-input label="Contraseña"
                                             type="password"
                                             placeholder="••••••••••••"
                                             v-model="form.password"
                                             :error="errors.password"
                                             :autocomplete="false"/>
                                </div>
                                <div class="col-12 my-4">
                                    <w-checkbox v-model="form.remember"
                                                label="Acuérdate de mí"></w-checkbox>
                                </div>
                                <div class="col-12">
                                    <w-button label="Iniciar sesión"
                                              size="large"
                                              type="submit"
                                              class="w-100">
                                    </w-button>
                                </div>
                                <div class="col-12 text-center mt-4 text-muted fs-13">
                                    <a href="#" @click.prevent="clickPasswordRecovery">
                                        ¿Has olvidado tu contraseña?
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <password-recovery ref="refDialogPasswordRecovery"></password-recovery>
    </div>
</template>
