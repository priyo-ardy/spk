<div class="modal fade" id="modal-error" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content rounded-0">
            <div class="modal-header bg-danger rounded-0 text-white fw-bolder">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i>&ensp;Error Result</h5>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <p id="errorMessage"></p>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered table-primary" id="tableError">
                            <thead>
                                <tr>
                                    <th class="bg-secondary-subtle text-center align-middle col-1">No</th>
                                    <th class="bg-secondary-subtle text-center align-middle col-11">Message</th>
                                </tr>
                            </thead>
                            <tbody id="errorList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary rounded-0" data-bs-dismiss="modal" onclick="closeModalError()"><i class="bi bi-x-circle"></i>&ensp;Close</button>
            </div>
        </div>
    </div>
</div>