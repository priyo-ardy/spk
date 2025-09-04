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
                        <li class="breadcrumb-item">App Setup</li>
                        <li class="breadcrumb-item">User Management</li>
                        <li class="breadcrumb-item">List of Users</li>
                        <li class="breadcrumb-item active">Create</li>
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
                            <button type="button" id="btnAdd" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Add User">
                                <i class="bi bi-plus-circle"></i>&ensp;Add
                            </button>
                            <button type="button" id="btnEdit" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                <i class="bi bi-pencil-square"></i>&ensp;Edit
                            </button>
                            <button type="button" disabled id="btnUpdate" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Update">
                                <i class="bi bi-floppy"></i>&ensp;Update
                            </button>
                            <button type="button" disabled id="btnCancel" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel">
                                <i class="bi bi-arrow-counterclockwise"></i>&ensp;Cancel
                            </button>
                            <button type="button" id="btnPrev" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Previous">
                                <i class="bi bi-chevron-double-left"></i>&ensp;Previous
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
                                <div class="row g-2">
                                    <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 clearfix mb-3">
                                        <div class="row mb-3 g-2">
                                            <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 align-middle text-center">
                                                <img src="<?= base_url() . $user_image ?>" alt="employee_photo" class="img-thumbnail" style="height: 350px;" id="img_preview" alt="Employee Photo">
                                            </div>
                                            <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-3 clearfix">
                                                <input type="file" class="custom-file-input form-control rounded-0" id="user_image" name="user_image" accept="image/jpeg, image/png, image/gif, image/webp" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 clearfix mb-3">
                                        <div class="row g-2">
                                            <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                                <label class="form-label" for="user_name">Username <strong class="text-danger">*</strong></label>
                                                <input type="text" name="user_name" id="user_name" class="form-control rounded-0" required maxlength="50" placeholder="Username" autofocus autocomplete="off" value="<?= $data->user_name ?>" disabled>
                                                <div class="invalid-feedback" id="feedback_username"></div>
                                            </div>
                                            <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                                <label class="form-label" for="full_name">Full Name <strong class="text-danger">*</strong></label>
                                                <input type="text" name="full_name" id="full_name" class="form-control rounded-0" required maxlength="150" placeholder="Full Name" autocomplete="off" value="<?= $data->full_name ?>" disabled>
                                                <div class="invalid-feedback" id="feedback_fullname"></div>
                                            </div>
                                            <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                                <label class="form-label" for="phone_number">Phone Number <strong class="text-danger">*</strong></label>
                                                <input type="number" name="phone_number" id="phone_number" class="form-control rounded-0" required maxlength="20" placeholder="Phone Number" autocomplete="off" value="<?= ($data->user_phone) ? dekripsi($data->user_phone) : ''; ?>" disabled>
                                                <div class="invalid-feedback" id="feedback_phone"></div>
                                            </div>
                                            <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                                <label class="form-label" for="email_address">Email Address <strong class="text-danger">*</strong></label>
                                                <input type="email" name="email_address" id="email_address" class="form-control rounded-0" required maxlength="150" placeholder="Email Address" autocomplete="off" value="<?= ($data->user_email) ? dekripsi($data->user_email) : ''; ?>" disabled>
                                                <div class="invalid-feedback" id="feedback_email"></div>
                                            </div>
                                            <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                                <label class="form-label" for="user_password">Password <strong class="text-danger">*</strong></label>
                                                <input type="password" name="user_password" id="user_password" class="form-control rounded-0" maxlength="20" placeholder="Password" autocomplete="off" disabled>
                                                <div class="invalid-feedback" id="feedback_password"></div>
                                            </div>
                                            <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                                <label class="form-label" for="user_level">User Level <strong class="text-danger">*</strong></label>
                                                <select name="user_level" id="user_level" class="form-control select2 select2bs5" required disabled>
                                                    <option <?= ($data->user_level == '') ? 'selected' : ''  ?> value="">-- Choose --</option>
                                                    <option <?= ($data->user_level == '0') ? 'selected' : ''  ?> value="0">Super Administrator</option>
                                                    <option <?= ($data->user_level == '1') ? 'selected' : ''  ?> value="1">Administrator</option>
                                                    <option <?= ($data->user_level == '2') ? 'selected' : ''  ?> value="2">Planner/ME</option>
                                                    <option <?= ($data->user_level == '3') ? 'selected' : ''  ?> value="3">Mold Engineer</option>
                                                    <option <?= ($data->user_level == '4') ? 'selected' : ''  ?> value="4">Quality</option>
                                                    <option <?= ($data->user_level == '5') ? 'selected' : ''  ?> value="5">User</option>
                                                </select>
                                                <div class="invalid-feedback">This field is required</div>
                                            </div>
                                            <div class="form-group col-xl-6 col-lg-6 col-md-12 col-6 col-sm-12 clearfix" style="display: none;">
                                                <label class="form-label" for="data_token">Token</label>
                                                <input type="text" name="data_token" id="data_token" class="form-control rounded-0" readonly value="<?= $token ?>">
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