<div class="modal fade" id="modalMoldConfirm" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary-subtle">
                <h5 class="modal-title">Mold Engineer Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="form-group clearfix mb-3">
                        <label class="form-label" for="data_tanggal_selesai_mold">Required Completion Date <strong class="text-danger">*</strong></label>
                        <input type="date" name="data_tanggal_selesai_mold" id="data_tanggal_selesai_mold" class="form-control rounded-0" value="<?= date("Y-m-d") ?>" required>
                        <div class="invalid-feedback">This field is required</div>
                    </div>
                    <div class="form-group clearfix">
                        <label class="form-label" for="data_keterangan_mold">Additional Information</label>
                        <textarea name="data_keterangan_mold" id="data_keterangan_mold" class="form-control rounded-0" placeholder="Additinal information"></textarea>
                    </div>
                </div>
                <div class="modal-footer g-2">
                    <div class="d-flex justify-content-center w-100">
                        <button type="button" class="btn btn-secondary rounded-0 mx-2" id="btnCancelMold">
                            <i class="bi bi-x-circle"></i> &ensp;Cancel
                        </button>
                        <button type="button" class="btn btn-danger rounded-0 mx-2" id="btnRejectMold">
                            <i class="bi bi-x-circle"></i> &ensp;Reject
                        </button>
                        <button type="button" class="btn btn-primary rounded-0 mx-2" id="btnConfirmMold">
                            <i class="bi bi-check-circle"></i> &ensp;Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>