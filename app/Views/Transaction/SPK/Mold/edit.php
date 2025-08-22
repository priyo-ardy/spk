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
                        <li class="breadcrumb-item">Transaction</li>
                        <li class="breadcrumb-item">SPK</li>
                        <li class="breadcrumb-item">Mold</li>
                        <li class="breadcrumb-item">List of Mold SPK</li>
                        <li class="breadcrumb-item">View</li>
                        <li class="breadcrumb-item active"><?= $data->code ?></li>
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
                            <button type="button" id="btnSave" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Save">
                                <i class="bi bi-floppy"></i>&ensp;Save
                            </button>
                            <button type="button" id="btnCancel" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel">
                                <i class="bi bi-arrow-counterclockwise"></i>&ensp;Cancel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <div class="row mb-3 g-2">
                                    <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_code">SPK No.</label>
                                        <input type="text" name="data_code" id="data_code" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" readonly placeholder="SPK No." value="<?= isset($data->code) ? $data->code : '' ?>">
                                    </div>
                                </div>
                                <div class="row mb-3 g-2">
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_workshop">Reporting Dept/Workshop</label>
                                        <select name="data_workshop" id="data_workshop" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($dept as $d): ?>
                                                <option <?= ($data->dept == $d->id) ? 'selected' : '' ?> value="<?= $d->id ?>"><?= $d->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_staff">Reporting Staff <strong class="text-danger">*</strong></label>
                                        <select name="data_staff" id="data_staff" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($karyawan as $k) : ?>
                                                <option <?= ($data->report_by == $k->id) ? 'selected' : '' ?> value="<?= $k->id ?>"><?= "$k->NIK - $k->nama" ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_tanggal">Report Date <strong class="text-danger">*</strong></label>
                                        <input type="date" name="data_tanggal" id="data_tanggal" class="form-control rounded-0" value="<?= $data->report_date ?>" required>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_mamterial">Part No. <strong class="text-danger">*</strong></label>
                                        <select name="data_material" id="data_material" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($material as $m): ?>
                                                    <option <?= ($data->part_no == $m->id) ? 'selected' : '' ?> value="<?= $m->id ?>"><?= "$m->code - $m->name" ?></option>
                                            <?php endforeach; ?>
                                        </select> 
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_name">Part Name</label>
                                        <input type="text" name="data_name" id="data_name" class="form-control rounded-0 bg-body-secondary" readonly maxlength="150" placeholder="Part Name" autocomplete="off" value="<?= $data->part_name ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_model">Part Model</label>
                                        <input type="text" name="data_model" id="data_model" class="form-control rounded-0 bg-body-secondary" readonly maxlength="150" placeholder="Part Model" autocomplete="off" value="<?= $data->part_model ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_mold">Mold No.</label>
                                        <input type="text" name="data_mold" id="data_mold" class="form-control rounded-0 bg-body-secondary" readonly maxlength="150" placeholder="Mold No." autocomplete="off" value="<?= $data->mold_no ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_repair">Repair Reason</label>
                                        <select name="data_repair" id="data_repair" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($repair as $r): ?>
                                                <option <?= ($data->repair_reason == $r->id) ? 'selected' : '' ?> value="<?= $r->id ?>"><?= $r->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-5 col-lg-5 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="fupload">Upload File <strong class="text-danger">*</strong></label>
                                        <input type="file" multiple class="custom-file-input form-control rounded-0" id="fupload" required name="fupload[]" accept="image/jpeg, image/png, image/gif, image/webp">
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_keterangan">Problem Description <strong class="text-danger">*</strong></label>
                                        <textarea name="data_keterangan" id="data_keterangan" class="form-control rounded-0" required placeholder="Describe the problem here" rows="5"><?= $data->description ?></textarea>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label">Problem Image</label>
                                        <div class="row mb-3 g-2">
                                            <?php foreach($details as $item): ?>
                                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 clearfix">
                                                    <a href="" class="link-underline-opacity-0">
                                                        <img src="<?= base_url() . 'uploads/mold_spk/'.$item->file_name ?>" class="img-thumbnail img-fluid" alt="<?= $item->file_name ?>">
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
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