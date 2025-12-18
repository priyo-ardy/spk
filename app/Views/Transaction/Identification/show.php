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
                        <li class="breadcrumb-item">SPK Identification</li>
                        <li class="breadcrumb-item">Show</li>
                        <li class="breadcrumb-item active"><?= $data->kode_spk ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
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
            <div class="row mb-3 g-2">
                <div class="col-12">
                    <div class="card rounded-0">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="form-group col-12 mb-3" style="display: none;">
                                    <input type="hidden" name="data_token" id="data_token" class="form-control rounded-0 bg-secondary-subtle" readonly value="<?= enkripsi($data->id) ?>">
                                </div>
                            </div>
                            <div class="row mb-3 g-2">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 clearfix">
                                    <div class="form-group mb-3">
                                        <label class="form-label">SPK No.</label>
                                        <input type="text" name="data_code" id="data_code" class="form-control form-control-lg rounded-0 bg-body-secondary text-primary fw-bolder" readonly value="<?= $data->kode_spk ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">SPK Type</label>
                                    <select name="data_tipe" id="data_tipe" class="form-control select2 select2bs5">
                                        <option <?= ($data->kategori == '') ? "selected" : '' ?> value="">-- Choose --</option>
                                        <option <?= ($data->kategori == '1') ? "selected" : '' ?> value="1">1. SPK Mold Repair</option> <!-- hanya keluar part no -->
                                        <option <?= ($data->kategori == '2') ? "selected" : '' ?> value="2">2. SPK Mesin</option> <!-- hanya keluar mesin & equipment -->
                                        <option <?= ($data->kategori == '3') ? "selected" : '' ?> value="3">3. SPK Preventive Maintenance Request</option> <!-- all part no -->
                                        <option <?= ($data->kategori == '4') ? "selected" : '' ?> value="4">4. SPK Equipment Request</option> <!-- material custom/input manual -->
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">SPK Reporting Date</label>
                                    <input type="date" name="data_tgl_lapor" id="data_tgl_lapor" class="form-control rounded-0 bg-body-secondary" readonly value="<?= $data->tgl_lapor ?>">
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Generate Date</label>
                                    <input type="date" name="data_generate" id="data_generate" class="form-control rounded-0 bg-body-secondary" readonly value="<?= $data->tanggal ?>">
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Required Completion Date <strong class="text-danger fw-bolder">*</strong></label>
                                    <input type="date" name="data_tgl_selesai" id="data_tgl_selesai" class="form-control rounded-0" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Reported Department</label>
                                    <select name="data_dept" id="data_dept" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($dept as $d) : ?>
                                            <option <?= ($data->dept == $d->id) ? "selected" : '' ?> value="<?= $d->id ?>"><?= $d->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Reported By</label>
                                    <select name="data_pelapor" id="data_pelapor" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($karyawan as $k): ?>
                                            <option <?= ($data->pelapor == $k->id) ? "selected" : "" ?> value="<?= $k->id ?>"><?= "$k->NIK - $k->nama" ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Part No/Machine No/Equipment No</label>
                                    <select name="data_material" id="data_material" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($material as $m) : ?>
                                            <option <?= ($data->material == $m->id) ? "selected" : '' ?> value="<?= $m->id ?>"><?= $m->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Part/Machine/Equipment Specification</label>
                                    <input type="text" name="data_spesifikasi" id="data_spesifikasi" class="form-control rounded-0 bg-body-secondary" value="<?= $data->material_model ?>">
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Repair Reason</label>
                                    <select name="data_repair" id="data_repair" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($repair as $r) : ?>
                                            <option <?= ($data->alasan_repair == $r->id) ? "selected" : '' ?> value="<?= $r->id ?>"><?= $r->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Defect/Problem</label>
                                    <select name="data_defect" id="data_defect" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($defect as $dfc): ?>
                                            <option <?= ($data->defect == $dfc->id) ? "selected" : '' ?> value="<?= $dfc->id ?>"><?= $dfc->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Sub Defect/Problem</label>
                                    <select name="data_sub_defect" id="data_sub_defect" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($sub_defect as $sdfc): ?>
                                            <option <?= ($data->sub_defect == $sdfc->id) ? "selected" : '' ?> value="<?= $sdfc->id ?>"><?= $sdfc->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Problem Position</label>
                                    <select name="data_posisi" id="data_posisi" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($posisi as $ps): ?>
                                            <option value="<?= $ps->id ?>"><?= $ps->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Repeat Problem</label>
                                    <select name="data_berulang" id="data_berulang" class="form-control select2 select2bs5">
                                        <option <?= ($data->berulang == '') ? 'selected' : '' ?> value="">-- Choose --</option>
                                        <option <?= ($data->berulang == '0') ? 'selected' : '' ?> value="0">No</option>
                                        <option <?= ($data->berulang == '1') ? 'selected' : '' ?> value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Problem Category</label>
                                    <select name="data_kategori" id="data_kategori" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($problem_category as $pct): ?>
                                            <option value="<?= $pct->id ?>"><?= $pct->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Repair Location</label>
                                    <select name="data_lokasi_repair" id="data_lokasi_repair" class="form-control select2 select2bs5">
                                        <option <?= ($data->lokasi_repair == '') ? 'selected' : '' ?> value="">-- Choose --</option>
                                        <option <?= ($data->lokasi_repair == '1') ? 'selected' : '' ?> value="1">Internal Repair</option>
                                        <option <?= ($data->lokasi_repair == '2') ? 'selected' : '' ?> value="2">External Repair</option>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Supplier</label>
                                    <select name="data_supplier" id="data_supplier" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($supplier as $s): ?>
                                            <option <?= ($data->supplier == $s->id) ? "selected" : ''  ?> value="<?= $s->id ?>"><?= $s->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-3 clearfix">
                                    <label class="form-label">Team Leader/Supervisor</label>
                                    <select name="data_leader" id="data_leader" class="form-control select2 select2bs5">
                                        <option value="">-- Choose --</option>
                                        <?php foreach ($leader as $l): ?>
                                            <option <?= ($data->leader == $l->id) ? "selected" : '' ?> value="<?= $l->id ?>"><?= "$l->NIK - $l->nama" ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-12 mb-3">
                                    <label class="form-label">Initial Analysis Problem</label>
                                    <textarea name="data_analysis" id="data_analysis" class="form-control rounded-0 summernote" placeholder="Describe the problem here" readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>