<div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="modalConfirm" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="formKonfirmasi">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Mold Repair Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="form-group col-12">
                            <input type="text" name="konfirmasi_token" id="konfirmasi_token" class="form-control rounded-0" hidden readonly>
                        </div>
                        <div class="form-group col-12 mb-3">
                            <label class="form-label">Reported Date <strong class="text-danger">*</strong></label>
                            <input type="date" name="tgl_lapor" id="tgl_lapor" readonly class="form-control bg-body-secondary rounded-0" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group col-12 mb-3">
                            <label class="form-label">Plan Finish Date <strong class="text-danger">*</strong></label>
                            <input type="date" name="plan_finish_date" id="plan_finish_date" class="form-control rounded-0" required autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group col-12">
                            <label class="form-label">Initial Analysis</label>
                            <textarea name="reason" id="reason" class="form-control rounded-0" placeholder="Describe the initial analysis here ..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-0" id="btnCancel" onclick="clearModal()" data-dismiss=" modal"><i class="bi bi-x-circle"></i>&ensp;Cancel</button>
                    <button type="button" class="btn btn-primary rounded-0" id="btnConfirm"><i class="bi bi-check-circle"></i>&ensp;Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImage" tabindex="-1" role="dialog" aria-labelledby="modalConfirm" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2" id="imageData"></div>
            </div>
            <div class=" modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="moldSelesai" tabindex="-1" role="dialog" aria-labelledby="moldSelesai" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="formMoldSelesai">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation of Mold Repair Completion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3 clearfix">
                        <label class="form-label" for="plan_finish">Plan Finish Date <strong class="text-danger">*</strong></label>
                        <input type="date" name="plan_finish" id="id_tanggal" class="form-control rounded-0" value="<?= date("Y-m-d") ?>" required>
                    </div>
                    <div class="form-group mb-3 clearfix">
                        <label class="form-label" for="required_finish">Required Finish Date <strong class="text-danger">*</strong></label>
                        <input type="date" name="required_finish" id="id_tanggal" class="form-control rounded-0" value="<?= date("Y-m-d") ?>" required>
                    </div>
                    <div class="form-group mb-3 clearfix">
                        <label class="form-label" for="actual_finish">Actual Finish Date <strong class="text-danger">*</strong></label>
                        <input type="date" name="actual_finish" id="id_tanggal" class="form-control rounded-0" value="<?= date("Y-m-d") ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-0" id="btnCancel" data-dismiss=" modal"><i class="bi bi-x-circle"></i>&ensp;Cancel</button>
                    <button type="button" class="btn btn-primary rounded-0" id="btnConfirm"><i class="bi bi-send"></i>&ensp;Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>