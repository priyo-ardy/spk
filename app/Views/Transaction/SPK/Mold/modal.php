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
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="formMoldSelesai">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation of Mold Repair Completion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearFinishMold()"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 g-2">
                        <div class="form-group col-12 clearfix">
                            <input type="text" name="token_selesai" id="token_selesai" class="form-control rounded-0 bg-secondary-subtle" hidden readonly>
                        </div>
                        <div class="form-group mb-3 col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix">
                            <label class="form-label" for="plan_finish">Plan Finish Date <strong class="text-danger">*</strong></label>
                            <input type="date" name="plan_finish" id="plan_finish" class="form-control rounded-0 bg-secondary-subtle" readonly value="<?= date("Y-m-d") ?>" required>
                        </div>
                        <div class="form-group mb-3 col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix">
                            <label class="form-label" for="required_finish">Required Finish Date <strong class="text-danger">*</strong></label>
                            <input type="date" name="required_finish" id="required_finish" class="form-control rounded-0 bg-secondary-subtle" readonly value="<?= date("Y-m-d") ?>" required>
                        </div>
                        <div class="form-group mb-3 col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix">
                            <label class="form-label" for="critical_level">Critical Level</label>
                            <input type="text" name="critical_level" id="critical_level" class="form-control rounded-0 bg-secondary-subtle" readonly>
                        </div>
                        <div class="form-group mb-3 col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix">
                            <label class="form-label" for="actual_finish">Actual Finish Date <strong class="text-danger">*</strong></label>
                            <input type="date" name="actual_finish" id="id_tanggal" class="form-control rounded-0" value="<?= date("Y-m-d") ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">Activity</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="remark-tab" data-bs-toggle="tab" data-bs-target="#remark" type="button" role="tab" aria-controls="remark" aria-selected="true">Remark</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-primary" id="activityTable">
                                                    <thead>
                                                        <th class="text-center align-middle bg-secondary-subtle">Activity Content</th>
                                                        <th class="text-center align-middle bg-secondary-subtle">Operator</th>
                                                        <th class="text-center align-middle bg-secondary-subtle">Date</th>
                                                        <th class="text-center align-middle bg-secondary-subtle">Repair Duration</th>
                                                        <th class="text-center align-middle bg-secondary-subtle">#</th>
                                                    </thead>
                                                    <tbody id="activityTableBody">
                                                        <tr>
                                                            <td>
                                                                <input type="text" name="nama_aktifitas[]" class="form-control rounded-0" required maxlength="150" placeholder="Activity Name">
                                                                <div class="invalid-feedback">This field is required</div>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="operator[]" class="form-control rounded-0" required maxlength="50" placeholder="Operator">
                                                                <div class="invalid-feedback">This field is required</div>
                                                            </td>
                                                            <td>
                                                                <input type="date" name="tanggal[]" class="form-control rounded-0" required>
                                                                <div class="invalid-feedback">This field is required</div>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="durasi[]" class="form-control rounded-0" required placeholder="Repair duration">
                                                                <div class="invalid-feedback">This field is required</div>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <button type="button" class="text-success btn shadow-none btn-sm rounded-0" onclick="addRow()">
                                                                    <i class="fas fa-plus-circle"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="remark" role="tabpanel" aria-labelledby="remark-tab">
                                    <div class="form-group mb-3 col-12 clearfix">
                                        <label class="form-label" for="mold_remark">Remark</label>
                                        <textarea name="mold_remark" id="mold_remark" class="form-control rounded-0 summernote" placeholder="Describe the problem here" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-0" id="btnCancel" onclick="clearFinishMold()" data-dismiss=" modal"><i class="bi bi-x-circle"></i>&ensp;Cancel</button>
                    <button type="button" class="btn btn-primary rounded-0" id="btnMoldSelesai"><i class="bi bi-send"></i>&ensp;Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>