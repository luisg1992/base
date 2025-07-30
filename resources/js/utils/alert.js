let swalInstance = null
let toastInstance = null

export function setAlertInstance($swal, $toast) {
    swalInstance = $swal
    toastInstance = $toast
}

export function setSwalInstance($swal) {
    swalInstance = $swal
}

export function setToastInstance($toast) {
    toastInstance = $toast
}

const alertTimers = {
    success: 1000,
    error: 5000,
    warning: 3000,
    info: 3000,
    question: 3000,
}

function validateInstance(instance) {
    if (instance !== 'swal' && instance !== 'toast') {
        console.error('The type is incorrect.')
        return false
    }
    if (instance === 'swal' && !swalInstance) {
        console.error('Swal instance not set. Call setSwalInstance($swal) first.')
        return false
    }
    if (instance === 'toast' && !toastInstance) {
        console.error('Toast instance not set. Call setToastInstance($toast) first.')
        return false
    }
    return true
}

function getTimer(infinite, showButton, icon) {
    if (infinite || showButton) return undefined
    return alertTimers[icon] ?? 3
}

function showNotification({
                              message,
                              title = '',
                              icon = 'info',
                              duration = 3,
                              infinite = false,
                              showButton = false,
                              instance = 'swal',
                              toastType = 'success'
                          }) {
    if (!validateInstance(instance)) return

    if (instance === 'toast') {
        toastInstance[toastType](message, {
            position: 'top-right',
            duration: duration * 1000,
        })
        return
    }

    let timer = getTimer(infinite, showButton, icon)
    swalInstance.fire({
        title,
        html: message,
        icon,
        timer,
        showConfirmButton: showButton,
        confirmButtonText: showButton ? 'Entiendo' : undefined,
        allowOutsideClick: false,
    })
}

export function showToastSuccess(message, options = {}) {
    showNotification({
        message,
        icon: 'success',
        toastType: 'success',
        instance: 'toast',
        ...options,
    })
}

export function showToastError(message, options = {}) {
    showNotification({
        message,
        icon: 'error',
        toastType: 'error',
        instance: 'toast',
        ...options,
    })
}

export function showToastInfo(message, options = {}) {
    showNotification({
        message,
        icon: 'info',
        toastType: 'info',
        instance: 'toast',
        ...options,
    })
}

export function showToastWarning(message, options = {}) {
    showNotification({
        message,
        icon: 'warning',
        toastType: 'warning',
        instance: 'toast',
        ...options,
    })
}

export function showSuccess(message, options = {}) {
    showNotification({
        message,
        icon: 'success',
        toastType: 'success',
        ...options,
    })
}

export function showError(message, options = {}) {
    showNotification({
        message,
        icon: 'error',
        toastType: 'error',
        ...options,
    })
}

export function showInfo(message, options = {}) {
    showNotification({
        message,
        icon: 'info',
        toastType: 'info',
        ...options,
    })
}

export function showAlert(title, message, icon = 'info', infinite = false, showButton = false, options = {}) {
    showNotification({
        title: title,
        message: message,
        icon: icon,
        infinite: infinite,
        showButton: showButton,
        toastType: 'success',
        ...options,
    })
}

export async function showAlertConfirmacion(titulo, texto, icono = 'warning', instance = 'swal') {
    if (!validateInstance(instance)) return

    const result = await swalInstance.fire({
        title: titulo,
        text: texto,
        icon: icono,
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        allowOutsideClick: false,
    })

    if (!result.isConfirmed) {
        showAlert(
            'OPERACIÓN CANCELADA',
            'LA OPERACIÓN SOLICITADA FUE CANCELADA, POR FAVOR CONTINÚE CON EL PROCESO DESEADO.',
            'warning'
        )
        return false
    }

    return true
}

export async function showMultiplesOpciones({title, text, icon = 'question', confirmText, cancelText, denyText}) {
    if (!swalInstance) {
        console.error('Swal instance not set. Call setSwalInstance($swal) first.')
        return null
    }

    const result = await swalInstance.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        denyButtonText: denyText,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-warning',
            denyButton: 'btn btn-danger',
        },
        buttonsStyling: false,
        reverseButtons: true,
        allowOutsideClick: false,
    })

    if (result.isConfirmed) return 'confirm'
    if (result.isDismissed && result.dismiss === swalInstance.DismissReason.cancel) return 'cancel'
    if (result.isDenied) return 'deny'

    return null
}
