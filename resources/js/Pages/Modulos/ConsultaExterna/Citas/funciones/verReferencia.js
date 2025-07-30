import axios from 'axios';
 
export function verReferencia({ openPDFModal }) {
    const visualizarReferencia = async (value) => {  
        const formData = {
            idreferenciabd: value.IdReferenciaDB,
            idreferencia: value.idReferencia,   
            numeroreferencia: value.numeroReferencia,
            estadoreferencia: value.estado,
            codigoestadoreferencia: value.codigoEstado,
            idestablecimiento: value.codigoEstablecimientoOrigen ?? value.codigoestablecimientoOrigen,
            anioreferencia: value.anio
        };

        try {
            showAlert(
                "VERIFICANDO REFERENCIA ...",
                "ESPERE UN MOMENTO MIENTRAS SE GENERA LA SOLICITUD",
                "warning",
                true
            );

            const { data } = await axios.post('/core/servicios/visualizarReferenciaRefcon', formData);
            if (!data) {
                showAlert("LA SOLICITUD NO PUDO SER PROCESADA", "No se recibió respuesta del servidor.", "warning");
                return;
            }

            if (data.codRespuesta === -1 || data.codRespuesta === -2) {
                showAlert("LA SOLICITUD NO PUDO SER PROCESADA", "LA SOLICITUD DE SERVICIOS REFCON NO PUDO COMPLETARSE. POR FAVOR COMUNICARSE CON INFORMÁTICA", "warning");
                return;
            }

            if (data.success) {
                const titulo = `N° REFERENCIA: ${value.numeroReferencia} - ${value.estado}`;
                openPDFModal(data.respuesta, titulo, 'base64');
                showAlert("OPERACIÓN REALIZADA", "LA VISTA PREVIA DE LA REFERENCIA FUE GENERADA DE FORMA EXITOSA", "success");
            } else {
                showAlert("LA SOLICITUD NO PUDO SER PROCESADA", data.respuesta?.message || "Error desconocido", "warning");
            }

        } catch (error) {
            console.error(error);
            showAlert("ERROR", "Ocurrió un error al procesar la solicitud", "error");
        }
    };

    return { visualizarReferencia };
}
