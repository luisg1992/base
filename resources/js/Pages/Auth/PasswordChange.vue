<script setup>
import {ref} from "vue";
import InputOtp from 'primevue/inputotp';
import axios from 'axios';
import BaseModal from "../../components/BaseModal.vue";
import BaseInput from "../../components/WInput/WInput.vue";
import BaseButton from "../../components/BaseButton.vue";
import WButton from "../../components/WButton/WButton.vue";

const props = defineProps(['recordId'])
const emit = defineEmits(['success'])

const isDialogOpen = ref(false);
let loadingSubmit = ref(false);
let title = ref('');
let errors = ref({});
let form = ref({});
let process = ref(1);

const initForm = () => {
    process.value = 1;
    title.value = 'Actualizar contraseña'
    form.value = {
        user_id: null,
        password_old: null,
        password: null,
        password_confirmation: null,
        cellphone: null,
        email: null,
    }
}

const handleOpen = async () => {
    initForm()
    await axios.get('/user-data')
        .then(response => {
            form.value = Object.assign({}, form.value, response.data)
            // lockedEmail.value = form.value.email !== null && form.value.email !== ''
        })
}

const onSubmit = async () => {
    if (form.value.password_old === '' || form.value.password_old === null) {

    }
    if (form.value.password === '' || form.value.password === null) {

    }
    if (form.value.password !== form.value.password_confirmation) {

    }
    if (form.value.code === null) {

    }
    loadingSubmit.value = true;
    await axios.post('/profile', form.value)
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

const onSendCode = async () => {
    loadingSubmit.value = true;
    await axios.post('/send-code', form.value)
        .then(response => {
            const res = response.data
            if (res.success) {
                process.value = 2
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
                <base-input label="Celular"
                            v-model="form.cellphone"></base-input>
            </div>
            <div class="col-24 col-sm-24" v-if="process === 1">
                <base-input label="Correo electrónico"
                            v-model="form.email"></base-input>
            </div>
            <template v-if="process === 2">
                <div class="col-24 col-sm-24">
                    <base-input label="Contraseña actual"
                                type="password"
                                v-model="form.password_old">
                    </base-input>
                </div>
                <div class="col-24 col-sm-24">
                    <base-input label="Contraseña nueva"
                                type="password"
                                v-model="form.password">
                    </base-input>
                </div>
                <div class="col-24 col-sm-24">
                    <base-input label="Confirmar contraseña nueva"
                                type="password"
                                v-model="form.password_confirmation">
                    </base-input>
                </div>
                <div class="col-24 col-sm-24">
                    <label>Código de verificación</label>
                    <InputOtp v-model="form.code" :length="6" integerOnly/>
                </div>
            </template>
        </div>
        <div class="d-flex justify-content-end mt-4 gap-2">
            <w-button label="Cancelar"
                      type-button="secondary"
                      text
                      @click="closeDialog"
                      :disabled="loadingSubmit"></w-button>

            <w-button label="Enviar código de verificación"
                      @click="onSendCode"
                      :loading="loadingSubmit"
                      :disabled="form.email === '' || loadingSubmit"
                      v-if="process === 1"></w-button>

            <w-button label="Actualizar contraseña"
                      @click="onSubmit"
                      :loading="loadingSubmit"
                      :disabled="loadingSubmit"
                      v-if="process === 2"></w-button>
        </div>
    </base-modal>
</template>
