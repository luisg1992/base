import axios from 'axios';

/**
 * Ver detalles de la cita
 */
export const verFormatoCita = async (idCita) => {
    try {
        const response = await axios.post('/consulta-externa/citas/CitasBuscarIdCitaFormatoPDF', {
            IdCita: idCita,
            Formato: 'ticket'
        });

        if (response.data.success !== false) {
            return response.data;
        } else {
            console.error('Error al generar PDF:', response.data.mensaje);
            return null;
        }
    } catch (error) {
        console.error('Error al obtener cita:', error);
        return null;
    }
};

/**
 * Imprimir detalles de la cita
 */
export const imprimirFormatoCita = async (idCita, validarImpresion, impresionMasiva = false) => {
    try {
        if (validarImpresion) {
            const mensaje = "¿ESTÁ SEGURO QUE DESEA REALIZAR LA IMPRESIÓN SOLICITADA, PARA EL REGISTRO SELECCIONADO?";
            const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
            if (!confirmado) return;
        }

        const response = await axios.post('/consulta-externa/citas/CitasPrinter', {
            IdCita: idCita,
            Formato: 'ticket'
        });

        if (response.data.success !== false) {
            showAlert("OPERACIÓN REALIZADA", "LA CITA FUE GENERADA DE FORMA EXITOSA", "success");

            if (impresionMasiva) {
                return response.data.response;
            } else {
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = "print://" + response.data.response;
                document.body.appendChild(iframe);
            }
        } else {
            showAlert("LA SOLICITUD NO PUDO SER PROCESADA", response.data?.mensaje || "Error desconocido", "warning");
        }
    } catch (error) {
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", error || "Error desconocido", "error");
    }
};

/**
 * Notifica al paciente (ejemplo básico)
 */
export const notificarFormatoCita = async (idCita) => {
    try {
        console.log('notificarCita:', idCita);
        // const response = await axios.post('/ruta/notificacion', { IdCita: idCita });
        // console.log('Notificación enviada:', response.data);
    } catch (error) {
        console.error('Error al notificar cita:', error);
    }
};

/**
 * Anular detalles de la cita
 */
export const anularFormatoCita = async (idCita, consultar = true, motivo = null) => {
    try {
        if (consultar) {
            const mensaje = "¿ESTÁ SEGURO QUE DESEA REALIZAR LA ANULACIÓN SOLICITADA, PARA EL REGISTRO SELECCIONADO?";
            const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
            if (!confirmado) return;
        }

        const response = await axios.post('/consulta-externa/citas/WebS_EliminarCita', { IdCita: idCita, Motivo: motivo });
        if (response.data.success !== false) {
            return response.data;
        } else {
            return response.data;
        }
    } catch (error) {
        return false;
    }
};

export const confirmarYAnularCita = async (IdCita, motivoAnulacionRef) => {
    // Mostrar el diálogo y esperar la selección
    const motivo = await motivoAnulacionRef.value?.mostrar()

    if (!motivo) {
        showAlert(
            'OPERACIÓN CANCELADA',
            'ES NECESARIO SELECCIONAR UN MOTIVO DE ANULACIÓN.',
            'warning',
            false,
            true
        )
        return false
    }

    // Reutilizar la función actual de anulación, enviando el motivo como segundo parámetro
    const respuesta = await anularFormatoCita(IdCita, false, motivo)

    if (respuesta?.success) {
        showAlert('OPERACIÓN REALIZADA', 'LA CITA FUE ANULADA DE FORMA EXITOSA', 'success')
        return true
    } else {
        showAlert('NO SE PUDO ANULAR LA CITA', respuesta?.mensaje || 'OCURRIÓ UN ERROR.', 'warning')
        return false
    }
}

/**
 * Ver detalles del FUA
 */
export const verFormatoFUA = async (idCita, intentos = 0) => {
    try {
        const response = await axios.post('/consulta-externa/citas/WebS_AdmisionCitasFormatoFua', {
            IdCita: idCita,
            Formato: 'a4'
        });

        if (response.data.success !== false) {
            return response.data;
        } else if (intentos < 2) {
            console.warn("No se encontró el FUA. Intentando generarlo...");
            const respValiFua = await axios.post('/consulta-externa/citas/WebS_GenerarFormatoFua', {
                IdCita: idCita
            });

            if (respValiFua.data.success !== false) {
                return await verFormatoFUA(idCita, intentos + 1);
            } else {
                showAlert("NO SE PUDO GENERAR EL FUA", respValiFua.data?.mensaje || "Error al generar el FUA", "warning");
                return null;
            }
        } else {
            showAlert("OPERACIÓN NO COMPLETADA", response.data?.mensaje || "Se alcanzó el número máximo de intentos para obtener el FUA.", "warning");
            return null;
        }
    } catch (error) {
        console.error('Error al obtener cita:', error);
        showAlert("ERROR DE CONEXIÓN", error?.message || "Error desconocido al obtener el FUA.", "error");
        return null;
    }
};


/**
 * Generar impresion de FUAS
 */
export const imprimirFormatoFUA = async (idCita, validarImpresion, intentos = 0, impresionDirecta = true) => {
    try {
        // Mostrar confirmación solo en el primer intento y si se solicita
        if (validarImpresion && intentos === 0) {
            const mensaje = "¿ESTÁ SEGURO QUE DESEA REALIZAR LA IMPRESIÓN DEL FORMATO FUA?";
            const confirmado = await showAlertConfirmacion('VALIDACIÓN DE DATOS REALIZADOS', mensaje, 'warning');
            if (!confirmado) return;
        }

        const respFua = await axios.post('/consulta-externa/citas/CitasFuaAdmisionPrinter', {
            IdCita: idCita,
            Formato: 'A4'
        });

        if (respFua.data.success !== false) {
            if (validarImpresion) {
                showAlert("OPERACIÓN REALIZADA", "LA FUA SE GENERÓ DE FORMA EXITOSA", "success");
            }

            //SI GENERAMOS POR LOTES NO IMPRIME DIRECTO, SOLO PARA CUANDO SE IMPRIME INDIVIDUAL
            if (impresionDirecta) {
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = "print://" + respFua.data.response;
                document.body.appendChild(iframe);
            }

            // RETORNAR EL EXTERNAL ID
            return respFua.data.response;
        } else {
            const respValiFua = await axios.post('/consulta-externa/citas/WebS_GenerarFormatoFua', {
                IdCita: idCita
            });

            if (respValiFua.data.success !== false && intentos < 2) {
                await imprimirFormatoFUA(idCita, validarImpresion, intentos + 1);
            } else if (intentos >= 2) {
                console.warn(`Se alcanzó el máximo de intentos (${intentos + 1}) para imprimir el FUA de la cita ID: ${idCita}`);
                showAlert("OPERACIÓN NO COMPLETADA", "Se alcanzó el número máximo de intentos para imprimir el FUA.", "warning");
            } else {
                showAlert("LA SOLICITUD NO PUDO SER PROCESADA", respFua.data?.mensaje || "Error desconocido", "warning");
            }
        }
    } catch (error) {
        console.error("Error durante la impresión del FUA:", error);
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", error?.message || "Error desconocido", "error");
    }
};


/**
 * Ver Listas relacionadas al paciente
 */
export const listasRelacionadasPaciente = async (IdPaciente, TipoFiltro) => {
    try {
        const response = await axios.post('/consulta-externa/citas/WebS_Listas_Paciente', {
            IdPaciente: IdPaciente,
            TipoFiltro: TipoFiltro
        });

        if (response.data.success !== false) {
            return response.data;
        } else {
            console.error('Error al generar PDF:', response.data.mensaje);
            return null;
        }
    } catch (error) {
        console.error('Error al obtener cita:', error);
        return null;
    }
};


/**
 * Genera la trama de impresión de un LOTE de citas. 
 */
export const generarLoteCITAS = async (payload) => {
    try {
        // Aquí enviamos TODO el objeto
        const resp = await axios.post('/consulta-externa/citas/CitasPrinter', payload);

        if (resp.data.success !== false) {
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = 'print://' + resp.data.response;
            document.body.appendChild(iframe);
        } else {
            showAlert(
                'LA SOLICITUD NO PUDO SER PROCESADA',
                resp.data?.mensaje || 'Error desconocido',
                'warning'
            );
        }
    } catch (error) {
        console.error('Error durante la impresión de CITAS:', error);
        showAlert(
            'LA SOLICITUD NO PUDO SER PROCESADA',
            error?.message || 'Error desconocido',
            'error'
        );
    }
};



/**
 * Generar trama de impresion de LOTE FUAS
 */
export const generarLoteFUA = async (Lote) => {
    try {
        const respFuaLote = await axios.post('/consulta-externa/citas/CitasFuaAdmisionPrinter', {
            Lote: Lote
        });

        if (respFuaLote.data.success !== false) {
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = "print://" + respFuaLote.data.response;
            document.body.appendChild(iframe);
        } else {
            showAlert("LA SOLICITUD NO PUDO SER PROCESADA", respFuaLote.data?.mensaje || "Error desconocido", "warning");
        }
    } catch (error) {
        console.error("Error durante la impresión del FUA:", error);
        showAlert("LA SOLICITUD NO PUDO SER PROCESADA", error?.message || "Error desconocido", "error");
    }
};