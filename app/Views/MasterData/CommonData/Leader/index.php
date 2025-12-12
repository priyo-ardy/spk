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
                        <li class="breadcrumb-item">Leader</li>
                        <li class="breadcrumb-item">List of Leader</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
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
                                    <input type="text" name="data_token" id="id_token" class="form-control rounded-0 bg-body-secondary" readonly placeholder="Token">
                                    <div class="invalid-feedback">This field is required</div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="data_NIK">Karyawan <strong class="text-danger">*</strong></label>
                                    <select name="data_NIK" id="id_NIK" class="form-control select2 select2bs5 rounded-0" required>
                                        <option value="">--Choose--</option>
                                        <?php foreach ($karyawan as $k): ?>
                                            <option value="<?= $k->NIK ?>"><?= $k->nama ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <div class="invalid-feedback">This field is required</div>
                                    <input type="text" name="data_nama" id="id_nama" class="form-control rounded-0 bg-body-secondary" readonly required hidden>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="data_remark">Remark</label>
                                    <textarea name="data_remark" id="id_remark" class="form-control rounded-0" placeholder="Remark"></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" id="btnCancel" class="btn btn-secondaryrounded-0" title="Cancel">
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
                                <h3 class="card-title"><i class="bi bi-list-ul"></i>&ensp;List of Group Leader</h3>
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
                                            <th class="align-middle bg-body-secondary">NIK</th>
                                            <th class="align-middle bg-body-secondary">Full Name</th>
                                            <th class="align-middle bg-body-secondary">Description</th>
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