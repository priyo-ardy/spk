<?= $this->extend('Layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    .action-buttons {
        position: absolute;
        top: 5px;
        right: 5px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
</style>

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
                        <li class="breadcrumb-item">Material Management</li>
                        <li class="breadcrumb-item">Material</li>
                        <li class="breadcrumb-item">Material Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <form id="formData" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="btn-group" role="group" aria-label="toolbar">
                            <button type="button" id="btnBack" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Back">
                                <i class="bi bi-arrow-left"></i>&ensp;Back
                            </button>
                            <button type="button" hidden id="btnUpdate" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Update">
                                <i class="bi bi-floppy"></i>&ensp;Update
                            </button>
                            <button type="button" id="btnEdit" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                <i class="bi bi-pencil-square"></i>&ensp;Edit
                            </button>
                            <button type="button" id="btnAdd" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Add">
                                <i class="bi bi-plus-circle"></i>&ensp;Add
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
                <div class="row">
                    <div class="col-12">
                        <div class="card rounded-0">
                            <div class="card-body">

                                <div class="row mb-3 g-2">
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_kategori">Category <strong class="text-danger">*</strong></label>
                                        <select name="data_kategori" id="id_kategori" class="form-control select2 select2bs5" required disabled>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($kategori as $k) : ?>
                                                <option <?= ($data->kategori == $k->id) ? 'selected' : '' ?> value="<?= $k->id ?>"><?= $k->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                        <input type="hidden" name="data_token" id="id_token" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" readonly placeholder="Machine Code" value="<?= isset($token) ? $token : '' ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_code">Material Code <strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_code" id="id_code" class="form-control rounded-0 bg-body-secondary" placeholder="Material Code" required readonly value="<?= $data->code; ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_name">Material Name <strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_name" id="id_name" class="form-control rounded-0 bg-body-secondary" placeholder="Material Name" required readonly value="<?= $data->name; ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_cust_name">Customer Material Name <strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_cust_name" id="id_cust_name" class="form-control rounded-0 bg-body-secondary" placeholder="Customer Material Name" required readonly value="<?= $data->cust_part_name; ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_color">Color <strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_color" id="id_color" class="form-control rounded-0 bg-body-secondary" placeholder="Material Color" required readonly value="<?= $data->color; ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_workshop">Workshop <strong class="text-danger">*</strong></label>
                                        <select name="data_workshop" id="id_workshop" class="form-control select2 select2bs5" required disabled>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($workshop as $w) : ?>
                                                <option <?= ($data->workshop == $w->id) ? 'selected' : '' ?> value="<?= $w->id ?>"><?= $w->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_uom">Unit <strong class="text-danger">*</strong></label>
                                        <select name="data_uom" id="id_uom" class="form-control select2 select2bs5" required disabled>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($satuan as $s) : ?>
                                                <option <?= ($data->uom == $s->id) ? 'selected' : '' ?> value="<?= $s->id ?>"><?= $s->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_keterangan">Description</label>
                                        <textarea name="data_keterangan" id="id_keterangan" class="form-control rounded-0 summernote" placeholder="Describe the problem here" rows="5"><?= $data->keterangan ?></textarea>
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