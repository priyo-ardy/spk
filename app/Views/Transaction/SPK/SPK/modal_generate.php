<div class="modal fade" id="modal-generate" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate SPK Identification Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModalGenerate()"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <p>
                            Are you sure you want to generate the SPK Identification Document from the selected data?
                        </p>
                    </div>
                    <div class="col-12">
                        <form id="form-generate" hidden></form>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary rounded-0" onclick="clearModalGenerate()"><i class="bi bi-x-circle"></i>&ensp;Cancel</button>
                <button class="btn btn-primary rounded-0" id="btnModalGenerate"><i class="bi bi-gear-wide-connected"></i>&ensp;Generate</button>
            </div>
        </div>
    </div>
</div>