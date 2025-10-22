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
                        <li class="breadcrumb-item">Supplier Detail</li>
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
                            <button type="button" hidden id="btnSave" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Save">
                                <i class="bi bi-floppy"></i>&ensp;Save
                            </button>
                            <button type="button" id="btnEdit" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                <i class="bi bi-pencil-square"></i>&ensp;Edit
                            </button>
                            <button type="button" hidden id="btnCancel" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel">
                                <i class="bi bi-arrow-counterclockwise"></i>&ensp;Cancel
                            </button>
                            <button type="button" id="btnPrev" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Previous">
                                <i class="bi bi-chevron-double-left"></i>&ensp;Prev
                            </button>
                            <button type="button" id="btnNext" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Next">
                                Next&ensp;<i class="bi bi-chevron-double-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="form-group col-12 mb-3 clearfix" style="display: none;">
                                        <input type="text" name="data_token" id="data_token" class="form-control rounded-0 bg-secondary-subtle" readonly value="<?= ($data->id) ? enkripsi($data->id) : '' ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_code">Supplier Code</label>
                                        <input type="text" class="form-control rounded-0 bg-secondary-subtle text-primary fw-bolder" id="data_code" name="data_code" placeholder="This code automatically generated after saving the data" readonly value="<?= $data->code ?>">
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_name">Supplier Name <strong class="text-danger">*</strong></label>
                                        <input type="text" class="form-control rounded-0 bg-secondary-subtle" readonly id="data_name" name="data_name" placeholder="Supplier Name" required maxlength="150" autofocus autocomplete="off" value="<?= $data->name ?>">
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_alamat">Address</label>
                                        <input type="text" name="data_alamat" id="data_alamat" class="form-control rounded-0 bg-secondary-subtle" readonly placeholder="Supplier Address" autocomplete="off" value="<?= $data->address ?>">
                                    </div>
                                    <div class="form-group col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_phone">Phone No.</label>
                                        <input type="text" name="data_phone" id="data_phone" class="form-control rounded-0 bg-secondary-subtle" readonly maxlength="20" placeholder="Supplier Phone No." autocomplete="off" value="<?= ($data->phone_no) ? dekripsi($data->phone_no) : '' ?>">
                                    </div>
                                    <div class="form-group col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_email">Email Address</label>
                                        <input type="email" name="data_email" id="data_email" class="form-control rounded-0 bg-secondary-subtle" readonly maxlength="150" placeholder="Supplier Email Address" autocomplete="off" value="<?= ($data->email_address) ? dekripsi($data->email_address) : '' ?>">
                                    </div>
                                    <div class="form-group col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_contact">Contact Person</label>
                                        <input type="text" name="data_contact" id="data_contact" class="form-control rounded-0 bg-secondary-subtle" readonly maxlength="150" placeholder="Supplier Contact Person" autocomplete="off" value="<?= $data->contact_person ?>">
                                    </div>
                                    <div class="form-group col-12">
                                        <label class="form-label" for="data_remark">Remark</label>
                                        <textarea name="data_remark" id="data_remark" class="form-control rounded-0 summernote" placeholder="Remark"><?= $data->remark ?></textarea>
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