<script setup>

import {ref} from "vue";
import axios from 'axios';
import BaseInput from "../../components/WInput/WInput.vue";
import BaseButton from "../../components/BaseButton.vue";
import BaseModal from "../../components/BaseModal.vue";
import InputOtp from "primevue/inputotp";

const isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let title = ref('');
let errors = ref({});
let form = ref({});
let process = ref();

const initForm = () => {
    process.value = 1;
    title.value = 'Recuperar contraseña';
    errors.value = {};
    form.value = {
        number: null,
        full_name: null,
        cellphone: null,
        email: null,
        password: null,
        password_confirmation: null,
        code: ''
    }
}

const handleOpen = async () => {
    initForm();
}

const onValidateNumber = async () => {
    loadingSubmit.value = true;
    await axios.post('/validate-number', {
        number: form.value.number
    })
        .then(response => {
            const res = response.data
            if (res.success) {
                process.value = 2;
                form.value = Object.assign({}, form.value, res.data);
            } else {
            }
        })
        .finally(() => {
            loadingSubmit.value = false;
        })
}

const onSendCode = async () => {
    if (form.value.email === '' || form.value.email === null) {

    }

    loadingSubmit.value = true;
    await axios.post('/send-code-reset-password', form.value)
        .then(response => {
            const res = response.data
            if (res.success) {
                process.value = 3

            } else {

            }
        })
        .finally(() => {
            loadingSubmit.value = false;
        })
}


const onSubmit = async () => {
    loadingSubmit.value = true;
    await axios.post('/reset-password', form.value)
        .then(response => {
            const res = response.data
            if (res.success) {
                isDialogOpen.value = false
            } else {
            }
        })
        .finally(() => {
            loadingSubmit.value = false;
        })
}

const openDialog = () => {
    isDialogOpen.value = true;
}

const closeDialog = () => {
    showModal.value = false;
    isDialogOpen.value = false;
}

defineExpose({
    openDialog
})
</script>

<template>
    <base-modal :isVisible="isDialogOpen"
                :loading="loadingSubmit"
                :header="title"
                @close="closeDialog"
                @open="handleOpen"
                size="modal-sm">
        <div class="row">
            <div class="col-24 col-sm-24" v-if="process === 1">
                <base-input label="Ingresar su número de DNI"
                            v-model="form.number"
                            :max-length="8"
                            :x-error="errors.number"></base-input>
            </div>
            <template v-if="process === 2 || process === 3">
                <div class="col-24 col-sm-24">
                    <base-input label="Nombres y apellidos"
                                v-model="form.full_name"
                                readonly></base-input>
                </div>
                <div class="col-24 col-sm-12">
                    <base-input label="Correo electrónico"
                                v-model="form.email"
                                readonly></base-input>
                </div>
                <template v-if="process === 3">
                    <div class="col-24 col-sm-12">
                        <base-input label="Contraseña"
                                    type="password"
                                    v-model="form.password">
                        </base-input>
                    </div>
                    <div class="col-24 col-sm-12">
                        <base-input label="Confirmar contraseña"
                                    type="password"
                                    v-model="form.password_confirmation">
                        </base-input>
                    </div>
                    <div class="col-24 col-sm-24">
                        <label>Código de verificación</label>
                        <InputOtp v-model="form.code" :length="6" integerOnly/>
                    </div>
                </template>
            </template>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <base-button label="Cancelar"
                         color="secondary"
                         @click="closeDialog"
                         style="margin-right: 16px;"
                         :disabled="loadingSubmit"></base-button>

            <base-button label="Validar documento"
                         @click="onValidateNumber"
                         :loading="loadingSubmit"
                         :disabled="loadingSubmit"
                         v-if="process === 1"></base-button>

            <base-button label="Enviar código de verificación"
                         @click="onSendCode"
                         :loading="loadingSubmit"
                         :disabled="form.email === '' || loadingSubmit"
                         v-if="process === 2"></base-button>

            <base-button label="Actualizar contraseña"
                         @click="onSubmit"
                         :loading="loadingSubmit"
                         :disabled="loadingSubmit"
                         v-if="process === 3"></base-button>
        </div>
    </base-modal>
</template>
