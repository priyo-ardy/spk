<div class="modal fade" id="changePassword" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="formGantiPassword">
                <div class="modal-header bg-body-secondary">
                    <h5 class="modal-title" id="modalTitle">Change User Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3" style="display: none;">
                        <input type="text" name="user_token" id="user_token" class="form-control rounded-0" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="new_password">New Password <strong class="text-danger">*</strong></label>
                        <input type="password" name="new_password" id="new_password" class="form-control rounded-0" placeholder="New Password" maxlength="20">
                        <div class="invalid-feedback">This field is required</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-center w-100">
                        <button type="button" class="btn btn-secondary rounded-0 mx-2" id="btnCancelMold" onclick="closeModal()">
                            <i class="bi bi-x-circle"></i> &ensp;Cancel
                        </button>
                        <button type="button" class="btn btn-primary rounded-0 mx-2" id="btnChangePassword">
                            <i class="bi bi-check-circle"></i> &ensp;Change
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>