<?= $this->extend('Layout/404_template'); ?>

<?= $this->section('content'); ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Page or Data Not Found</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= base_url() . 'dashboard' ?>" onclick="loading()">Dashboard</a></li>
                        <li class="breadcrumb-item active">Error 404</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row mb-3 g-2">
                <div class="col-12">
                    <div class="card rounded-0">
                        <div class="card-header rounded-0">
                            <h5 class="card-title">Error 404</h5>
                        </div>
                        <div class="card-body">
                            <p>
                                The data you are looking for is not found, please check the data you are looking for.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->endSection(); ?>