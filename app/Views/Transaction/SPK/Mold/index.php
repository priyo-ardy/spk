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
                        <li class="breadcrumb-item active">List of Mold SPK</li>
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
                                        <th class="align-middle bg-body-secondary">SPK No.</th>
                                        <th class="align-middle bg-body-secondary">Date</th>
                                        <th class="align-middle bg-body-secondary">Requested Dept.</th>
                                        <th class="align-middle bg-body-secondary">Reported By</th>
                                        <th class="align-middle bg-body-secondary">Part No.</th>
                                        <th class="align-middle bg-body-secondary">Part Name</th>
                                        <th class="align-middle bg-body-secondary">Part Model</th>
                                        <th class="align-middle bg-body-secondary">Mold/Jig No.</th>
                                        <th class="align-middle bg-body-secondary">Repair Reason</th>
                                        <th class="align-middle bg-body-secondary">Problem Description</th>
                                        <th class="align-middle bg-body-secondary">Problem Image</th>
                                        <th class="align-middle bg-body-secondary">Defect</th>
                                        <th class="align-middle bg-body-secondary">Sub Defect</th>
                                        <th class="align-middle bg-body-secondary">Repeat Problem</th>
                                        <th class="align-middle bg-body-secondary">Position</th>
                                        <th class="align-middle bg-body-secondary">Team Leader</th>
                                        <th class="align-middle bg-body-secondary">Status</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->include('Transaction/SPK/Mold/modal') ?>
<?= $this->endSection(); ?>