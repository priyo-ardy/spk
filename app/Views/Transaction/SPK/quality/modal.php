<div class="modal fade" id="qualityConfirmModal" tabindex="-1" role="dialog" aria-labelledby="qualityConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <form id="formConfirmQuality">
                <div class="modal-header">
                    <h5 class="modal-title">Quality Mold Repair Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="form-group col-12 mb-3 clearfix">
                            <input type="text" name="qa_token" id="qa_token" class="form-control rounded-0 bg-secondary-subtle" readonly hidden>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-primary table-striped table-hover" id="qualityTable">
                                <thead>
                                    <th class="text-center align-middle bg-secondary-subtle col-1">No.</th>
                                    <th class="text-center align-middle bg-secondary-subtle col-6">Check Type</th>
                                    <th class="text-center align-middle bg-secondary-subtle col-5">Status</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1.</td>
                                        <td>Appearance</td>
                                        <td>
                                            <select name="appearance" id="appearance" class="form-control select2 select2bs5" required>
                                                <option value="">-- Choose --</option>
                                                <option value="1">OK</option>
                                                <option value="0">NG</option>
                                                <option value="2">No checks were performed</option>
                                            </select>
                                            <div class="invalid-feedback">This field is required</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2.</td>
                                        <td>Dimension</td>
                                        <td>
                                            <select name="dimension" id="dimension" class="form-control select2 select2bs5" required>
                                                <option value="">-- Choose --</option>
                                                <option value="1">OK</option>
                                                <option value="0">NG</option>
                                                <option value="2">No checks were performed</option>
                                            </select>
                                            <div class="invalid-feedback">This field is required</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3.</td>
                                        <td>Performance</td>
                                        <td>
                                            <select name="performance" id="performance" class="form-control select2 select2bs5" required>
                                                <option value="">-- Choose --</option>
                                                <option value="1">OK</option>
                                                <option value="0">NG</option>
                                                <option value="2">No checks were performed</option>
                                            </select>
                                            <div class="invalid-feedback">This field is required</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="form-group col-12">
                            <label class="form-label">Remark</label>
                            <textarea name="qa_remark" id="qa_remark" class="form-control rounded-0 summernote" placeholder="Remark"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-0" id="btnCancelQuality" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i>&ensp;Cancel</button>
                    <button type="button" class="btn btn-primary rounded-0" id="btnConfirmQuality"><i class="bi bi-send"></i>&ensp;Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>