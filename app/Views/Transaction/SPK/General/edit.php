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
    .image-container:hover .action-buttons{
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
                        <li class="breadcrumb-item">Equipment</li>
                        <li class="breadcrumb-item">List of Equipment SPK</li>
                        <li class="breadcrumb-item">Show</li>
                        <li class="breadcrumb-item active"><?= $data->code ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <form id="formData">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="btn-group" role="group" aria-label="toolbar">
                            <button type="button" id="btnBack" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Back">
                                <i class="bi bi-arrow-left"></i>&ensp;Back
                            </button>
                            <button type="button" id="btnUpdate" hidden class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Update">
                                <i class="bi bi-floppy"></i>&ensp;Update
                            </button>
                            <button type="button" id="btnEdit" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                <i class="bi bi-pencil-square"></i>&ensp;Edit
                            </button>
                            <button type="button" id="btnCancel" hidden class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel">
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
                                <div class="row mb-2">
                                    <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 clearfix" style="display: none;">
                                        <input type="text" name="data_token" id="data_token" class="form-control rounded-0 bg-body-secondary" readonly value="<?= $token ?>">
                                    </div>
                                </div>
                                <div class="row mb-3 g-2">
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_code">SPK No.</label>
                                        <input type="text" name="data_code" id="data_code" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" readonly placeholder="SPK No." value="<?= $data->code ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_workshop">Reporting Dept/Workshop</label>
                                        <select name="data_workshop" id="data_workshop" class="form-control select2 select2bs5" disabled required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($dept as $d): ?>
                                                <option <?= ($data->dept == $d->id) ? 'selected' : '' ?> value="<?= $d->id ?>"><?= $d->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_staff">Reporting Staff <strong class="text-danger">*</strong></label>
                                        <select name="data_staff" id="data_staff" class="form-control select2 select2bs5" disabled required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($karyawan as $k) : ?>
                                                <option <?= ($data->report_by == $k->id) ? 'selected' : '' ?> value="<?= $k->id ?>"><?= "$k->NIK - $k->nama" ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_tanggal">Report Date <strong class="text-danger">*</strong></label>
                                        <input type="date" name="data_tanggal" id="data_tanggal" class="form-control rounded-0 bg-body-secondary" readonly value="<?= $data->report_date ?>" required>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_mamterial">Equipment Name <strong class="text-danger">*</strong></label>
                                        <select name="data_material" id="data_material" class="form-control select2 select2bs5" disabled required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($mesin as $m): ?>
                                                    <option <?= ($data->equipment == $m->id) ? 'selected' : ''; ?> value="<?= $m->id ?>"><?= "$m->nomor_mesin - $m->name" ?></option>
                                            <?php endforeach; ?>
                                        </select> 
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_nomor">Equipment No.</label>
                                        <input type="text" name="data_nomor" id="data_nomor" class="form-control rounded-0 bg-body-secondary" readonly maxlength="150" placeholder="Equipment No." autocomplete="off" value="<?= $data->equipment_no ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_model">Equipment Model</label>
                                        <input type="text" name="data_model" id="data_model" class="form-control rounded-0 bg-body-secondary" readonly maxlength="150" placeholder="Equipment Model" autocomplete="off" value="<?= $data->equipment_model ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_tipe">Equipment Type</label>
                                        <select name="data_tipe" id="data_tipe" class="form-control select2 select2bs5" disabled required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($equipment_type as $et): ?>
                                                <option <?= ($data->equipment_type == $et->id) ? 'selected' : '' ?> value="<?= $et->id ?>"><?= $et->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_spv">Team Leader/Supervisor</label>
                                        <select name="data_spv" id="data_spv" class="form-control select2 select2bs5" disabled required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($leader as $l): ?>
                                                <option <?= ($data->leader ==  $l->id) ? 'selected' : '' ?> value="<?= $l->id ?>"><?= $l->nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="fupload">Upload File <strong class="text-danger">*</strong></label>
                                        <input type="file" class="custom-file-input form-control rounded-0" id="fupload" disabled name="fupload[]" multiple accept="image/jpeg, image/png, image/gif, image/webp">
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_keterangan">Problem Description <strong class="text-danger">*</strong></label>
                                        <textarea name="data_keterangan" id="data_keterangan" class="form-control summernote rounded-0 bg-body-secondary" readonly required placeholder="Describe the problem here" rows="5"><?= $data->description ?></textarea>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-6 col-lg-6 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="image-preview">Problem Location</label>
                                        <div class="row g-2">
                                            <?php foreach($details as $item): ?>
                                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 clearfix image-container">
                                                    <a href="<?= base_url() . 'uploads/equipment_spk/'.$item->file_name ?>?#" class="link-underline-opacity-0" data-lightbox="preview" data-title="<?= $item->file_name ?>">
                                                        <img src="<?= base_url() . 'uploads/equipment_spk/'.$item->file_name ?>" class="img-thumbnail img-fluid" alt="<?= $item->file_name ?>">
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
<?= $this->endSection(); ?>