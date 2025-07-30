<script>
export default {
    data() {
        return {
            isModalOpen: false,
            modalTitle: '',
            pdfSrc: ''
        };
    },
    methods: {
        openModal(url, titulo, tipo = null) {
            this.modalTitle = titulo; // Establece el título del modal
            this.isModalOpen = true; // Muestra el modal

            // Determina la fuente del PDF
            if (tipo === 'base64') {
                this.pdfSrc = `data:application/pdf;base64,${url}`;
            } else if (tipo === 'base64_bloqueado') {
                this.pdfSrc = `data:application/pdf;base64,${url}#toolbar=0`;
            } else {
                this.pdfSrc = url; // Si no es base64, se usa la URL directamente
            }
        },
        closeModal() {
            this.isModalOpen = false; // Cierra el modal
            this.pdfSrc = ''; // Limpia el src del PDF
        }
    }
};
</script>

<template>
    <div v-if="isModalOpen" class="modal fade show" tabindex="-1" role="dialog" aria-labelledby="modalPDFLabel"
        style="display: block;" aria-hidden="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalPDFLabel">{{ modalTitle }}</h5>
                    <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div style="position: relative; height: 650px;">
                            <embed :src="pdfSrc" width="100%" height="650px" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>