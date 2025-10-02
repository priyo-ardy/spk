<?= $this->extend('Layout/template'); ?>

<?= $this->section('content'); ?>
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0"><?= $title; ?></h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= base_url() . 'dashboard' ?>" onclick="loading()">Dashboard</a></li>
                        <li class="breadcrumb-item">Master Data</li>
                        <li class="breadcrumb-item">Common Data</li>
                        <li class="breadcrumb-item">Defect</li>
                        <li class="breadcrumb-item">List of Defect</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div calss="app-content">
        <div class="container-fluid">
            <form id="formData">
                <div class="row g-2">
                    <div class="col-4">
                        <div class="card rounded-0">
                            <div class="card-header rounded-0 bg-body-secondary">
                                <h3 class="card-title"><i class="bi bi-pencil-square"></i>&ensp;Form Data</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3" style="display: none">
                                    <input type="text" name="data_token" id="data_token" class="form-control rounded-0 bg-body-secondary" readonly placeholder="Token">
                                    <div class="invalid-feedback">This field is required</div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form_label" for="data_kategori">Category <strong class="text-danger">*</strong></label>
                                    <select name="data_kategori" id="data_kategori" class="form-control select2 select2bs5 rounded-0" required>
                                        <option value="">-- Choose --</option>
                                        <option value="1">1. SPK Mold Repair</option> <!-- hanya keluar part no -->
                                        <option value="2">2. SPK Mesin</option> <!-- hanya keluar mesin & equipment -->
                                        <option value="3">3. SPK Preventive Maintenance Request</option> <!-- all part no -->
                                        <option value="4">4. SPK Equipment Request</option> <!-- material custom/input manual -->
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="data_code">Code <strong class="text-danger">*</strong></label>
                                    <input type="text" name="data_code" id="data_code" class="form-control rounded-0 bg-body-secondary" readonly placeholde="Generate automatically after saving the data">
                                    <div class="invalid-feedback">This field is required</div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="data_name">Name<strong class="text-danger">*</strong></label>
                                    <input type="text" name="data_name" id="data_name" class="form-control rounded-0" maxlength="150" placeholder="Defect name" autocomplete="off" autofocus>
                                    <div class="invalid-feedback">This field is required</div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="data_remark">Remark</label>
                                    <textarea name="data_remark" id="data_remark" class="form-control rounded-0" placeholder="Remark"></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" id="btnCancel" class="btn btn-secondary rounded-0" title="Cancel">
                                            <i class="bi bi-arrow-counterclockwise"></i>&ensp;Cancel
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" hidden id="btnUpdate" class="btn btn-primary rounded-0" title="Update">
                                            <i class="bi bi-floppy"></i>&ensp;Update
                                        </button>
                                        <button type="button" id="btnSave" class="btn btn-primary rounded-0" title="Save">
                                            <i class="bi bi-floppy"></i>&ensp;Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="card rounded-0">
                            <div class="card-header rounded-0 bg-body-secondary">
                                <h3 class="card-title"><i class="bi bi-list-ul"></i>&ensp;List of Defect</h3>
                                <div class="card-tools">
                                    <button type="button" id="btnDownload" class="btn btn-tool" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button type="button" id="btnRefresh" class="btn btn-tool" title="Refresh">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-primary" id="dataTable">
                                        <thead>
                                            <th class="align-middle bg-body-secondary">No.</th>
                                            <th class="align-middle bg-body-secondary">Code</th>
                                            <th class="align-middle bg-body-secondary">Category</th>
                                            <th class="align-middle bg-body-secondary">Name</th>
                                            <th class="align-middle bg-body-secondary">Remark</th>
                                            <th class="align-middle bg-body-secondary">#</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>