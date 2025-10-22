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
                        <li class="breadcrumb-item">Supplier</li>
                        <li class="breadcrumb-item">New Supplier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div calss="app-content">
        <div class="container-fluid">
            <form id="formData">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="btn-group" role="group" aria-label="toolbar">
                            <button type="button" id="btnBack" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Back">
                                <i class="bi bi-arrow-left"></i>&ensp;Back
                            </button>
                            <button type="button" id="btnSave" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Save">
                                <i class="bi bi-floppy"></i>&ensp;Save
                            </button>
                            <button type="button" id="btnCancel" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel">
                                <i class="bi bi-arrow-counterclockwise"></i>&ensp;Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_code">Supplier Code</label>
                                        <input type="text" class="form-control rounded-0 bg-secondary-subtle" id="data_code" name="data_code" placeholder="This code automatically generated after saving the data" readonly>
                                    </div>
                                    <div class="form-group col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_name">Supplier Name <strong class="text-danger">*</strong></label>
                                        <input type="text" class="form-control rounded-0" id="data_name" name="data_name" placeholder="Supplier Name" required maxlength="150" autofocus autocomplete="off">
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_alamat">Address</label>
                                        <input type="text" name="data_alamat" id="data_alamat" class="form-control rounded-0" placeholder="Supplier Address" autocomplete="off">
                                    </div>
                                    <div class="form-group col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_phone">Phone No.</label>
                                        <input type="text" name="data_phone" id="data_phone" class="form-control rounded-0" maxlength="20" placeholder="Supplier Phone No." autocomplete="off">
                                    </div>
                                    <div class="form-group col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_email">Email Address</label>
                                        <input type="email" name="data_email" id="data_email" class="form-control rounded-0" maxlength="150" placeholder="Supplier Email Address" autocomplete="off">
                                    </div>
                                    <div class="form-group col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_contact">Contact Person</label>
                                        <input type="text" name="data_contact" id="data_contact" class="form-control rounded-0" maxlength="150" placeholder="Supplier Contact Person" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12">
                                        <label class="form-label" for="data_remark">Remark</label>
                                        <textarea name="data_remark" id="data_remark" class="form-control rounded-0 summernote" placeholder="Remark"></textarea>
                                    </div>
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