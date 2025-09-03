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
                                <div class="row g-2">
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="user_name">Username <strong class="text-danger">*</strong></label>
                                        <input type="text" name="user_name" id="user_name" class="form-control rounded-0" required maxlength="50" placeholder="Username" autofocus autocomplete="off">
                                        <div class="invalid-feedback" id="feedback_username"></div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="full_name">Full Name <strong class="text-danger">*</strong></label>
                                        <input type="text" name="full_name" id="full_name" class="form-control rounded-0" required maxlength="150" placeholder="Full Name" autocomplete="off">
                                        <div class="invalid-feedback" id="feedback_fullname"></div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="phone_number">Phone Number <strong class="text-danger">*</strong></label>
                                        <input type="number" name="phone_number" id="phone_number" class="form-control rounded-0" required maxlength="20" placeholder="Phone Number" autocomplete="off">
                                        <div class="invalid-feedback" id="feedback_phone"></div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="email_address">Email Address <strong class="text-danger">*</strong></label>
                                        <input type="email" name="email_address" id="email_address" class="form-control rounded-0" required maxlength="150" placeholder="Email Address" autocomplete="off">
                                        <div class="invalid-feedback" id="feedback_email"></div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="user_password">Password <strong class="text-danger">*</strong></label>
                                        <input type="password" name="user_password" id="user_password" class="form-control rounded-0" required maxlength="20" placeholder="Password" autocomplete="off">
                                        <div class="invalid-feedback" id="feedback_password"></div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="user_level">User Level <strong class="text-danger">*</strong></label>
                                        <select name="user_level" id="user_level" class="form-control select2 select2bs5" required>
                                            <option value="">-- Choose --</option>
                                            <option value="0">Super Administrator</option>
                                            <option value="1">Administrator</option>
                                            <option value="3">Planner/ME</option>
                                            <option value="4">Mold Engineer</option>
                                            <option value="5">Quality</option>
                                            <option value="6">User</option>
                                        </select>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-6 col-sm-12 clearfix mb-3">
                                        <label class="form-label" for="user_image">User Image</label>
                                        <input type="file" class="custom-file-input form-control rounded-0" id="user_image" name="user_image" accept="image/jpeg, image/png, image/gif, image/webp">
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