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
                        <li class="breadcrumb-item">App Setup</li>
                        <li class="breadcrumb-item">Database Seeder</li>
                        <li class="breadcrumb-item active">Master Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card rounded-0">
                        <div class="card-header rounded-0">
                            <h3 class="card-title">Select Table</h3>
                            <div class="card-tools">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="form-group col-xl-6 col-lg-6 col-md-12 colsm-12 clearfix">
                                    <label class="form-label" for="table_name">Table Name &ensp;<strong class="text-danger">*</strong></label>
                                    <select name="table_name" id="table_name" class="form-control select2 select2bs5" required>
                                        <option value="">-- Choose --</option>
                                        <option value="1">Material Category</option>
                                        <option value="2">List of Material</option>
                                        <option value="3">Equipment Type</option>
                                        <option value="4">Workshop</option>
                                        <option value="5">Tonnage</option>
                                        <option value="6">Machine</option>
                                        <option value="7">Defect</option>
                                        <option value="8">Sub Defect</option>
                                        <option value="9">Defect Position</option>
                                        <option value="10">Repair Reason</option>
                                        <option value="11">UoM</option>
                                        <option value="12">Department</option>
                                        <option value="13">Leader</option>
                                        <option value="14">List of Employee</option>
                                        <option value="15">Location</option>
                                        <option value="16">Supplier</option>
                                    </select>
                                </div>
                                <div class="invalid-feedback">This field is required</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" id="btnSubmit" class="btn btn-primary rounded-0"><i class="bi bi-gear-wide-connected"></i>&ensp;Generate</button>
                            <button type="button" id="btnReset" class="btn btn-secondary rounded-0"><i class="bi bi-arrow-counterclockwise"></i>&ensp;Reset</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card rounded-0">
                        <div class="card-header">Data Result</div>
                        <div class="card-body card-body-scrollable" id="dataResult" style="height: 500px; overflow-y: auto;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>