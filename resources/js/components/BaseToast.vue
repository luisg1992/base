<!--<template>-->
<!--    <Toast position="top-right" group="global"/>-->
<!--</template>-->

<!--<script setup>-->
<!--import {useToast} from 'primevue/usetoast'-->

<!--const toast = useToast()-->

<!--const showInfo = (msg, closeAuto = true, time = 4000) => {-->
<!--    toast.add({severity: 'info', summary: 'Información', detail: msg, life: closeAuto ? time : null})-->
<!--}-->

<!--const showSuccess = (msg, closeAuto = true, time = 4000) => {-->
<!--    toast.add({severity: 'success', summary: 'Confirmación', detail: msg, life: closeAuto ? time : null})-->
<!--}-->

<!--const showError = (msg, closeAuto = true, time = 4000) => {-->
<!--    toast.add({severity: 'error', summary: 'Error', detail: msg, life: closeAuto ? time : null})-->
<!--}-->

<!--defineExpose({showInfo, showSuccess, showError})-->
<!--</script>-->
<template>
    <Toast position="top-right" group="global"/>
</template>

<script setup>
import Toast from 'primevue/toast'
</script>
