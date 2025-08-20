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
                        <li class="breadcrumb-item">Equipment</li>
                        <li class="breadcrumb-item">List of Equipment SPK</li>
                        <li class="breadcrumb-item active">Create</li>
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
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_code">SPK No.</label>
                                        <input type="text" name="data_code" id="data_code" class="form-control rounded-0 bg-body-secondary" readonly placeholder="SPK No.">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_workshop">Reporting Dept/Workshop</label>
                                        <select name="data_workshop" id="data_workshop" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <option value="1">1. Injection</option>
                                            <option value="2">2. Assembly</option>
                                            <option value="3">3. Final Inspection</option>
                                            <option value="4">4. R&D</option>
                                            <option value="5">5. Quality</option>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_staff">Reporting Staff <strong class="text-danger">*</strong></label>
                                        <select name="data_staff" id="data_staff" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <option value="0014">0014 - EDI SALURIYANTO</option>
                                            <option value="0065">0065 - SUPRIYANTO</option>
                                            <option value="0145">0145 - SUKRON AZIZ</option>
                                            <option value="0205">0205 - RAMADHAN SAPTA WIYUDA</option>
                                            <option value="0275">0275 - TEGUH SUSILO</option>
                                            <option value="0009">0009 - JOKO PRASETYO UTOMO</option>
                                            <option value="0346">0346 - MUHAMAD SHOLIKUL HADI</option>
                                            <option value="0355">0355 - MARIMAN</option>
                                            <option value="0357">0357 - LUKMAN DWI PRAMANA</option>
                                            <option value="0363">0363 - AFALULLOH</option>
                                            <option value="0368">0368 - AGUS UTAMA</option>
                                            <option value="0440">0440 - AHMAT NUR SAHLY</option>
                                            <option value="0013">0013 - AGUS SETIANTO</option>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_tanggal">Report Date <strong class="text-danger">*</strong></label>
                                        <input type="date" name="data_tanggal" id="data_tanggal" class="form-control rounded-0" value="<?= date("Y-m-d") ?>" required>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-4 col-lg-4 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_mamterial">Equipment Name <strong class="text-danger">*</strong></label>
                                        <select name="data_material" id="data_material" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <?php foreach($mesin as $m): ?>
                                                    <option value="<?= $m->id ?>"><?= "$m->nomor_mesin - $m->name" ?></option>
                                            <?php endforeach; ?>
                                        </select> 
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_nomor">Equipment No.</label>
                                        <input type="text" name="data_nomor" id="data_nomor" class="form-control rounded-0 bg-body-secondary" readonly maxlength="150" placeholder="Equipment No." autocomplete="off">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_model">Equipment Model</label>
                                        <input type="text" name="data_model" id="data_model" class="form-control rounded-0 bg-body-secondary" readonly maxlength="150" placeholder="Equipment Model" autocomplete="off">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_tipe">Equipment Type</label>
                                        <select name="data_tipe" id="data_tipe" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <option value="1">1. Machine Equipment</option>
                                            <option value="2">2. Transportation Equipment</option>
                                            <option value="3">3. Laboratorium Equipment</option>
                                            <option value="4">4. Electronic Equipment</option>
                                            <option value="5">5. Other Equipment</option>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_spv">Team Leader/Supervisor</label>
                                        <select name="data_spv" id="data_spv" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <option value="0009">0009 - Joko Prasetyo Utomo</option>
                                            <option value="0013">0013 - Agus Setianto</option>
                                            <option value="0014">0014 - Edi Saluriyanto</option>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="fupload">Upload File <strong class="text-danger">*</strong></label>
                                        <input type="file" class="custom-file-input form-control rounded-0" id="fupload" required name="fupload" accept="image/jpeg, image/png, image/gif, image/webp">
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_keterangan">Problem Description <strong class="text-danger">*</strong></label>
                                        <textarea name="data_keterangan" id="data_keterangan" class="form-control rounded-0" required placeholder="Describe the problem here" rows="5"></textarea>
                                        <div class="invalid-feedback">This field is required</div>
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