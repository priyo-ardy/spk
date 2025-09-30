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
                        <li class="breadcrumb-item">Common Data</li>
                        <li class="breadcrumb-item">Machine</li>
                        <li class="breadcrumb-item"><?= $data->code ?></li>
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
                                    <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_code">Machine Code</label>
                                        <input type="text" name="data_code" id="id_code" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" readonly placeholder="Machine Code" value="<?= isset($data->code) ? $data->code : '' ?>">
                                        <input type="hidden" name="data_token" id="id_token" class="form-control rounded-0 bg-body-secondary text-primary fw-bolder" readonly placeholder="Machine Code" value="<?= isset($token) ? $token : '' ?>">
                                    </div>
                                </div>
                                <div class="row mb-3 g-2">
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_workshop">Workshop&ensp;<strong class="text-danger">*</strong></label>
                                        <select name="data_workshop" id="id_workshop" class="form-control select2 select2bs5" required disabled>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($workshop as $w) : ?>
                                                <option <?= ($data->workshop == $w->id) ? 'selected' : '' ?> value="<?= $w->id ?>"><?= $w->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_mesin">Machine No.&ensp;<strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_mesin" id="id_mesin" class="form-control rounded-0 bg-body-secondary" maxlength="150" placeholder="Machine No" autocomplete="off" readonly value="<?= $data->nomor_mesin ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_nama">Machine Name&ensp;<strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_nama" id="id_nama" class="form-control rounded-0 bg-body-secondary" maxlength="150" placeholder="Machine Name" autocomplete="off" readonly value="<?= $data->name ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_spesifikasi">Specification&ensp;<strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_spesifikasi" id="id_spesifikasi" class="form-control rounded-0 bg-body-secondary" maxlength="150" placeholder="Machine Specification" autocomplete="off" readonly value="<?= $data->model ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_brand">Brand&ensp;<strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_brand" id="id_brand" class="form-control rounded-0 bg-body-secondary" maxlength="150" placeholder="Machine Brand" autocomplete="off" readonly value="<?= $data->brand ?>">
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_serial">Serial No<strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_serial" id="id_serial" class="form-control rounded-0 bg-body-secondary" maxlength="150" placeholder="Machine Serial" autocomplete="off" readonly value="<?= $data->serial_no ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_tonnage">Tonnage&ensp;<strong class="text-danger">*</strong></label>
                                        <select name="data_tonnage" id="id_tonnage" class="form-control select2 select2bs5" required disabled>
                                            <option value="">-- Choose --</option>
                                            <?php foreach ($tonnage as $t) : ?>
                                                <option <?= ($data->tonnage == $t->id) ? 'selected' : '' ?> value="<?= $t->id ?>"><?= $t->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_rate">Machine Rate&ensp;<strong class="text-danger">*</strong></label>
                                        <input type="text" name="data_rate" id="id_rate" class="form-control rounded-0 bg-body-secondary" maxlength="150" placeholder="Machine Rate" autocomplete="off" readonly value="<?= $data->rate ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_tanggal">Manufacturing Date&ensp;<strong class="text-danger">*</strong></label>
                                        <input type="date" name="data_tanggal" id="id_tanggal" class="form-control rounded-0 bg-body-secondary" value="<?= date("Y-m-d") ?>" required readonly value="<?= $data->mfg_date ?>">
                                    </div>
                                    <div class="form-group col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="data_beli">Purchase Date&ensp;<strong class="text-danger">*</strong></label>
                                        <input type="date" name="data_beli" id="id_beli" class="form-control rounded-0 bg-body-secondary" value="<?= date("Y-m-d") ?>" required readonly value="<?= $data->purchase_date ?>">
                                    </div>
                                    <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="data_keterangan">Description</label>
                                        <textarea name="data_keterangan" id="id_keterangan" class="form-control summernote rounded-0" placeholder="Describe the problem here" rows="5"><?= $data->remark ?></textarea>
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