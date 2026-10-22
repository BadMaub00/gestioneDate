document.addEventListener('DOMContentLoaded', () => {
    // 1. Iniezione dinamica del modal nel DOM se non presente
    if (!document.getElementById('customDeleteModal')) {
        const modalHTML = `
        <div class="modal fade" id="customDeleteModal" tabindex="-1" aria-labelledby="customDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger d-flex align-items-center" id="customDeleteModalLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>Conferma Eliminazione
                        </h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                    </div>
                    <div class="modal-body py-3">
                        <p class="m-0 text-dark fw-medium" id="customDeleteModalMessage">
                            Sei sicuro di voler procedere con l'eliminazione? L'operazione non potrà essere annullata.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0 gap-2">
                        <!-- Pulsante ANNULLA con bordo e testo blu -->
                        <button type="button" class="btn bg-white border border-unibs text-unibs btn-sm rounded-pill px-3 fw-bold" data-bs-dismiss="modal">
                            Annulla
                        </button>
                        <!-- Pulsante ELIMINA -->
                        <button type="button" id="confirmDeleteSubmitBtn" class="btn btn-danger btn-sm text-white rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-trash-fill me-1"></i> Elimina
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    const deleteModalEl = document.getElementById('customDeleteModal');
    const deleteModal = new bootstrap.Modal(deleteModalEl);
    const confirmBtn = document.getElementById('confirmDeleteSubmitBtn');
    const messageEl = document.getElementById('customDeleteModalMessage');

    let targetForm = null;

    // 2. Delegazione evento submit globale
    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (form.hasAttribute('data-confirm')) {
            if (!form.dataset.confirmed) {
                event.preventDefault();
                targetForm = form;

                const customMsg = form.getAttribute('data-confirm');
                if (customMsg && customMsg.trim() !== '') {
                    messageEl.textContent = customMsg;
                } else {
                    messageEl.textContent = 'Sei sicuro di voler eliminare questo elemento?';
                }

                deleteModal.show();
            }
        }
    });

    // 3. Invio effettivo del form
    confirmBtn.addEventListener('click', () => {
        if (targetForm) {
            targetForm.dataset.confirmed = "true";
            targetForm.submit();
            deleteModal.hide();
        }
    });
});