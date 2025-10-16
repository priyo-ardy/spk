<div class="modal fade" id="modalPlannerConfirm" tabindex="-1" role="dialog" aria-labelledby="modalConfirm" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form id="formKonfirmasiPlanner">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Planner Mold Repair Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="form-group col-12 clearfix">
                            <input type="text" name="konfirmasi_token" id="konfirmasi_token" class="form-control rounded-0" readonly>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                            <label class="form-label">Reported Date <strong class="text-danger">*</strong></label>
                            <input type="date" name="tgl_lapor" id="tgl_lapor" readonly class="form-control bg-body-secondary rounded-0" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                            <label class="form-label">Plan Finish Date <strong class="text-danger">*</strong></label>
                            <input type="date" name="plan_finish_date" id="plan_finish_date" class="form-control rounded-0" readonly required autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                            <label class="form-label">Required Finish Date <strong class="text-danger">*</strong></label>
                            <input type="date" name="required_finish_date" id="required_finish_date" class="form-control rounded-0" required autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                            <label class="form-label">Repair Priority <strong class="text-danger">*</strong></label>
                            <select name="prioritas" id="prioritas" class="form-control select2 select2bs5">
                                <option value="">-- Choose --</option>
                                <option value="0">Low</option>
                                <option value="1">Normal</option>
                                <option value="2">Urgent</option>
                                <option value="3">Very Urgent</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group col-12">
                            <label class="form-label">Comment</label>
                            <textarea name="reason" id="reason" class="form-control rounded-0" placeholder="Comment"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-0" id="btnCancel" onclick="clearModal()" data-dismiss=" modal"><i class="bi bi-x-circle"></i>&ensp;Cancel</button>
                    <button type="button" class="btn btn-primary rounded-0" id="btnConfirm"><i class="bi bi-send"></i>&ensp;Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>