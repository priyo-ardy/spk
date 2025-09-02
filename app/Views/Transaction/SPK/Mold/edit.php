<?= $this->extend('Layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    .image-container {
        position: relative;
        /* display: inline-block; */
        /* margin: 10px; */
    }

    .action-buttons {
        position: absolute;
        top: 5px;
        right: 5px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-container:hover .action-buttons {
        opacity: 1;
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
                            <button type="button" <?= ($data->status == 0) ? '' : 'hidden' ?> id="btnEngineerConfirm" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Engineer Confirmation">
                                <i class="bi bi-check-circle"></i>&ensp;Mold Engineer Confirmation
                            </button>
                            <button type="button" <?= ($data->status == 1) ? '' : 'hidden' ?> id="btnPlannerConfirm" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Planner Confirmation">
                                <i class="bi bi-check-circle"></i>&ensp;Planner Confirmation
                            </button>
                            <button type="button" <?= ($data->status == 2) ? '' : 'hidden' ?> id="btnQualityConfirm" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Quality Confirmation">
                                <i class="bi bi-check-circle"></i>&ensp;Quality Confirmation
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
                                    <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_code">SPK No.</label>
                                        <input type="text" name="data_code" id="data_code" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" required readonly placeholder="SPK No." value="<?= isset($data->code) ? $data->code : '' ?>">
                                        <input type="hidden" name="data_token" id="data_token" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" readonly placeholder="SPK No." value="<?= isset($token) ? $token : '' ?>">
                                    </div>
                                </div>
                                <div class="row mb-3 g-2">
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_workshop">Reporting Dept/Workshop</label>
                                        <select name="data_workshop" id="data_workshop" class="form-control select2 select2bs5" required disabled>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($dept as $d): ?>
                                                <option <?= ($data->dept == $d->id) ? 'selected' : '' ?> value="<?= $d->id ?>"><?= $d->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_staff">Reporting Staff <strong class="text-danger">*</strong></label>
                                        <select name="data_staff" id="data_staff" class="form-control select2 select2bs5" required disabled>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($karyawan as $k) : ?>
                                                <option <?= ($data->report_by == $k->id) ? 'selected' : '' ?> value="<?= $k->id ?>"><?= "$k->NIK - $k->nama" ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_tanggal">Report Date <strong class="text-danger">*</strong></label>
                                        <input type="date" name="data_tanggal" id="data_tanggal" class="form-control rounded-0 bg-body-secondary" value="<?= $data->report_date ?>" required>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_mamterial">Part No. <strong class="text-danger">*</strong></label>
                                        <select name="data_material" id="data_material" class="form-control select2 select2bs5" required disabled>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($material as $m): ?>
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
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_defect">Defect <strong class="text-danger">*</strong></label>
                                        <select name="data_defect" id="data_defect" disabled class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($defects as $defect): ?>
                                                <option <?= ($data->defect == $defect->id) ? 'selected' : ''; ?> value="<?= $defect->id ?>"><?= $defect->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_sub_defect">Sub Defect <strong class="text-danger">*</strong></label>
                                        <select name="data_sub_defect" id="data_sub_defect" disabled class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($sub_defect as $sd): ?>
                                                <option <?= ($data->sub_defect == $sd->id) ? 'selected' : '' ?> value="<?= $sd->id ?>"><?= $sd->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_berulang">Repeat Problem <strong class="text-danger">*</strong></label>
                                        <select name="data_berulang" id="data_berulang" disabled class="form-control select2 select2bs5" required>
                                            <option <?= ($data->berulang == '0') ? 'selected' : ''; ?> value="0">No</option>
                                            <option <?= ($data->berulang == '1') ? 'selected' : ''; ?> value="1">Yes</option>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_posisi">Problem Position <strong class="text-danger">*</strong></label>
                                        <select name="data_posisi" id="data_posisi" disabled class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($posisi as $pos): ?>
                                                <option <?= ($data->position == $pos->id) ? 'selected' : '' ?> value="<?= $pos->id ?>"><?= $pos->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_repair">Repair Reason</label>
                                        <select name="data_repair" id="data_repair" class="form-control select2 select2bs5" required disabled>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($repair as $r): ?>
                                                <option <?= ($data->repair_reason == $r->id) ? 'selected' : '' ?> value="<?= $r->id ?>"><?= $r->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-5 col-lg-5 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="fupload">Upload File <strong class="text-danger">*</strong></label>
                                        <input type="file" multiple class="custom-file-input form-control rounded-0" disabled id="fupload" name="fupload[]" accept="image/jpeg, image/png, image/gif, image/webp">
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_keterangan">Problem Description <strong class="text-danger">*</strong></label>
                                        <textarea name="data_keterangan" id="data_keterangan" class="form-control summernote rounded-0" required placeholder="Describe the problem here" rows="5"><?= $data->description ?></textarea>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label">Problem Image</label>
                                        <div class="row mb-3 g-2">
                                            <?php foreach ($details as $item): ?>
                                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 clearfix image-container">
                                                    <a href="<?= base_url() . 'uploads/mold_spk/' . $item->file_name ?>?#" class="link-underline-opacity-0" data-lightbox="preview" data-title="<?= $item->file_name ?>">
                                                        <!-- <a href="#" class="link-underline-opacity-0" data-lightbox="preview" data-title="<?= $item->file_name ?>"> -->
                                                        <img src="<?= base_url() . 'uploads/mold_spk/' . $item->file_name ?>" class="img-thumbnail img-fluid" alt="<?= $item->file_name ?>">
                                                    </a>
                                                    <div class="action-buttons">
                                                        <button type="button" class="btn btn-sm rounded-0 btn-danger btn-delete" data-id="1" onclick="deleteImage('<?= enkripsi(($item->id)) ?>')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
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

<?= $this->include('Transaction/SPK/Mold/modal_edit.php') ?>
<?= $this->endSection(); ?>