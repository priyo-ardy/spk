<?= $this->extend('Layout/template'); ?>

<?= $this->section('content'); ?>
<style>
    .img-preview {
        position: relative;
    }

    .action-buttons {
        position: absolute;
        top: 5px;
        right: 5px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .img-preview:hover .action-buttons {
        opacity: 1;
    }
</style>
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0"><?= "$title | $code"; ?></h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= base_url() . 'dashboard' ?>" onclick="loading()">Dashboard</a></li>
                        <li class="breadcrumb-item">Transaction</li>
                        <li class="breadcrumb-item">SPK</li>
                        <li class="breadcrumb-item">List of SPK</li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                        <li class="breadcrumb-item active"><?= $code ?></li>
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
                            <button type="button" id="btnAdd" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="New">
                                <i class="bi bi-plus-circle"></i>&ensp;New
                            </button>
                            <button <?= ($header->dokumen_status == 0 || $header->dokumen_status == '3') ? '' : 'hidden' ?> type="button" id="btnSave" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Save">
                                <i class="bi bi-floppy"></i>&ensp;Save
                            </button>
                            <button type="button" <?= ($header->dokumen_status == '0') ? '' : 'hidden' ?> id="btnSubmit" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                <i class="bi bi-send"></i>&ensp;Submit
                            </button>
                            <button type="button" <?= ($header->dokumen_status ==  '1') ? '' : 'hidden' ?> id="btnUndo" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                <i class="bi bi-arrow-counterclockwise"></i>&ensp;Undo
                            </button>
                            <button type="button" <?= ($header->dokumen_status ==  '1' || $header->dokumen_status == '3') ? '' : 'hidden'; ?> id="btnApprove" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve">
                                <i class="bi bi-check2-circle"></i>&ensp;Approve
                            </button>
                            <button type="button" <?= ($header->dokumen_status !== '2' || $header->dokumen_status == '5') ? 'hidden' : ''  ?> id="btnUnApprove" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Un-Approve">
                                <i class="bi bi-check2-circle"></i>&ensp;Un-Approve
                            </button>
                            <button type="button" <?= ($header->dokumen_status !== '0') ? 'hidden' : '' ?> id="btnCancel" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel">
                                <i class="bi bi-arrow-counterclockwise"></i>&ensp;Cancel
                            </button>
                            <button type="button" id="btnPrint" <?= ($header->dokumen_status !== '5') ? 'hidden' : '' ?> class="btn shadow-none rounded-0 btn-light border-0" title="Print">
                                <i class="bi bi-printer"></i>&ensp;Print
                            </button>
                            <div class="btn-group" role="group" <?= ($header->dokumen_status !== '2') ? 'hidden' : '' ?>>
                                <button type="button" id="btnAction" class="btn shadow-none rounded-0 btn-light border-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="moldConfirm('<?= enkripsi($header->id) ?>')">1. Mold Engineer Confirmation</a></li>
                                    <li><a class="dropdown-item" href="#">2. ME Confirmation</a></li>
                                    <li><a class="dropdown-item" href="#">3. Planner Confirmation</a></li>
                                    <li><a class="dropdown-item" href="#">4. Mold Engineer Completion</a></li>
                                    <li><a class="dropdown-item" href="#">5. ME Completion</a></li>
                                    <li><a class="dropdown-item" href="#">6. Quality Confirmation</a></li>
                                </ul>
                            </div>
                            <button type="button" id="btnPrev" class="btn shadow-none rounded-0 btn-light border-0" title="Previous Data">
                                <i class="bi bi-chevron-double-left"></i>&ensp;Prev
                            </button>
                            <button type="button" id="btnNext" class="btn shadow-none rounded-0 btn-light border-0" title="Next Data">
                                Next&ensp;<i class="bi bi-chevron-double-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-12 mb-1">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <div class="row g-2 mb-3">
                                    <div class="form-group col-9 clearfix">
                                        <label class="form-label">Document No.</label>
                                        <input type="text" name="data_code" id="data_code" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" readonly value="<?= $code ?>">
                                    </div>
                                    <div class="form-group col-3 clearfix">
                                        <label class="form-label">Document Status</label>
                                        <input type="text" name="data_status" id="data_status" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" readonly value="<?= $nama_status ?>">
                                        <input type="hidden" name="status" id="status" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" readonly value="<?= $status ?>">
                                    </div>
                                    <input type="hidden" name="data_token" id="data_token" class="form-control rounded-0 bg-body-secondary" readonly value="<?= $token ?>">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="doc_type">Document Type <strong class="text-danger">*</strong></label>
                                        <select name="doc_type" id="doc_type" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option <?= ($header->kategori == '') ? 'selected' : '' ?> value="">-- Choose --</option>
                                            <option <?= ($header->kategori == '1') ? 'selected' : '' ?> value="1">1. SPK Mold Repair</option> <!-- hanya keluar part no -->
                                            <option <?= ($header->kategori == '2') ? 'selected' : '' ?> value="2">2. SPK Mesin</option> <!-- hanya keluar mesin & equipment -->
                                            <option <?= ($header->kategori == '3') ? 'selected' : '' ?> value="3">3. SPK Preventive Maintenance Request</option> <!-- all part no -->
                                            <option <?= ($header->kategori == '4') ? 'selected' : '' ?> value="4">4. SPK Equipment Request</option> <!-- material custom/input manual -->
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_lokasi">Location <strong class="text-danger">*</strong></label>
                                        <select name="data_lokasi" id="data_lokasi" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($location_list as $lokasi): ?>
                                                <option <?= ($header->lokasi == $lokasi->id) ? 'selected' : '' ?> value="<?= $lokasi->id; ?>"><?= $lokasi->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_dept">Reported Dept <strong class="text-danger">*</strong></label>
                                        <select name="data_dept" id="data_dept" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($dept_list as $dept): ?>
                                                <option <?= ($header->dept == $dept->id) ? 'selected' : '' ?> value="<?= $dept->id; ?>"><?= $dept->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_pelapor">Reported By <strong class="text-danger">*</strong></label>
                                        <select name="data_pelapor" id="data_pelapor" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($emp_list as $kry): ?>
                                                <option <?= ($header->pelapor == $kry->id) ? 'selected' : '' ?> value="<?= $kry->id ?>"><?= $kry->NIK . " - " . $kry->nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_tanggal">Reported Date <strong class="text-danger">*</strong></label>
                                        <input type="date" name="data_tanggal" id="data_tanggal" class="form-control rounded-0" value="<?= $header->tgl_lapor ?>" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_material">Material <strong class="text-danger">*</strong></label>
                                        <select name="data_material" id="data_material" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($material_list as $material): ?>
                                                <option <?= ($header->material == $material->id) ? 'selected' : '' ?> value="<?= $material->id ?>"><?= $material->code . " - " . $material->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_model">Material Model/Equipment Model</label>
                                        <input type="text" name="data_model" id="data_model" class="form-control rounded-0 bg-body-secondary" readonly placeholder="Material Model/Equipment No." value="<?= $header->material_model ?>" <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_mold">Mold No/Machine No</label>
                                        <input type="text" name="data_mold" id="data_mold" class="form-control rounded-0 bg-body-secondary" readonly placeholder="Mold No." value="<?= $header->nomor_mesin ?>" <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="tipe_equipment">Equipment Type</label>
                                        <select name="tipe_equipment" id="tipe_equipment" <?= ($header->kategori == '1' || ($header->dokumen_status !== '0' && $header->dokumen_status !== '3')) ? 'disabled' : '' ?> class="form-control select2 select2bs5">
                                            <option <?= ($header->tipe_equipment == '') ? 'selected' : '' ?> value="">-- Choose --</option>
                                            <option <?= ($header->tipe_equipment == '1') ? 'selected' : '' ?> value="1">1. Machine Equipment</option>
                                            <option <?= ($header->tipe_equipment == '2') ? 'selected' : '' ?> value="2">2. Transportation Equipment</option>
                                            <option <?= ($header->tipe_equipment == '3') ? 'selected' : '' ?> value="3">3. Final Inspection Equipment</option>
                                            <option <?= ($header->tipe_equipment == '4') ? 'selected' : '' ?> value="4">4. Laboratorium Equipment</option>
                                            <option <?= ($header->tipe_equipment == '5') ? 'selected' : '' ?> value="5">5. Electronic Equipment</option>
                                            <option <?= ($header->tipe_equipment == '6') ? 'selected' : '' ?> value="5">6. Other Equipment</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_leader">Team Leader/Supervisor <strong class="text-danger">*</strong></label>
                                        <select name="data_leader" id="data_leader" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($leader_list as $leader): ?>
                                                <option <?= ($header->leader == $leader->id) ? 'selected' : '' ?> value="<?= $leader->id; ?>"><?= $leader->NIK . " - " . $leader->nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_defect">Defect <strong class="text-danger">*</strong></label>
                                        <select name="data_defect" id="data_defect" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($defect_list as $defect): ?>
                                                <option <?= ($header->defect == $defect->id) ? 'selected' : '' ?> value="<?= $defect->id; ?>"><?= $defect->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_sub_defect">Sub Defect <strong class="text-danger">*</strong></label>
                                        <select name="data_sub_defect" id="data_sub_defect" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($sub_defect as $subdefect): ?>
                                                <option <?= ($header->sub_defect == $subdefect->id) ? 'selected' : '' ?> value="<?= $subdefect->id ?>"><?= $subdefect->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_berulang">Repeat Problem <strong class="text-danger">*</strong></label>
                                        <select name="data_berulang" id="data_berulang" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option <?= ($header->berulang == '') ? 'selected' : '' ?> value="">-- Choose --</option>
                                            <option <?= ($header->berulang == '0') ? 'selected' : '' ?> value="0">No</option>
                                            <option <?= ($header->berulang == '1') ? 'selected' : '' ?> value="1">Yes</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <!-- <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_posisi">Problem Position <strong class="text-danger">*</strong></label>
                                        <select name="data_posisi" id="data_posisi" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($position_list as $position): ?>
                                                <option <?= ($header->posisi == $position->id) ? 'selected' : '' ?> value="<?= $position->id; ?>"><?= $position->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div> -->
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_repair">Repair Reason <strong class="text-danger">*</strong></label>
                                        <select name="data_repair" id="data_repair" class="form-control select2 select2bs5" required <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($reason_list as $reason): ?>
                                                <option <?= ($header->alasan_repair == $reason->id) ? 'selected' : '' ?> value="<?= $reason->id; ?>"><?= $reason->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-12 clearfix">
                                        <label class="form-label">Image preview :</label>
                                        <div class="row g-2 mb-3">
                                            <?php foreach ($details as $row): ?>
                                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix img-preview">
                                                    <a href="<?= base_url() . 'uploads/spk/' . $row->nama_file ?>?#" class="link-underline-opacity-0" data-lightbox="preview" data-title="<?= $row->nama_file ?>">
                                                        <img src="<?= base_url() . 'uploads/spk/' . $row->nama_file ?>" class="img-thumbnail rounded-0 col-12" alt="<?= $row->nama_file ?>">
                                                    </a>
                                                    <div class="action-buttons">
                                                        <button <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'hidden' : '' ?> type="button" class="btn btn-sm rounded-0 btn-danger btn-delete" data-id="1" onclick="deleteImage('<?= enkripsi(($row->id)) ?>')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3 clearfix">
                                        <label class="form-label" for="data_image">Upload Problem Image <strong class="text-danger">*</strong></label>
                                        <input type="file" multiple class="custom-file-input form-control rounded-0" id="data_image" name="data_image[]" accept="image/jpeg, image/png, image/gif, image/webp" <?= ($header->dokumen_status !== '0' && $header->dokumen_status !== '3') ? 'disabled' : '' ?>>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <div class="row g-2 mb-3">
                                    <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 clearfix">
                                        <label class="form-label" for="data_ketarangan">Problem Description <strong class="text-danger">*</strong></label>
                                        <textarea name="data_keterangan" id="data_ketarangan" class="form-control rounded-0 summernote" required><?= $header->deskripsi ?></textarea>
                                        <div class="invalid-feedback"></div>
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
<?= $this->include('Transaction/SPK/Mold/modal') ?>
<?= $this->endSection(); ?>