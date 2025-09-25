<div class="modal fade" id="modalImage" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 image-container" id="imageData"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary rounded-0" id="btnSaveMember" onclick="clearModal()">
                    <i class="bi bi-check-circle"></i> &ensp;OK
                </button>
            </div>
        </div>
    </div>
</div>