<aside class="app-sidebar bg-body-secondary" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= base_url() . 'MainMenu'; ?>" class="brand-link">
            <img
                src="<?= base_url() . 'image/favicon.png'; ?>"
                alt="AdminLTE Logo"
                class="brand-image opacity-75" />
            <span class="brand-text fw-light">Schlemmer Indonesia</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-header">DASHBOARD</li>
                <li class="nav-item">
                    <a href="<?= base_url() . 'dashboard' ?>" class="nav-link" onclick="loading()">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-header">TRANSACTION</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-folder2-open"></i>
                        <p>
                            SPK
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url() . 'spk' ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    List of SPK
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-folder2-open"></i>
                        <p>
                            Idenfication
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url() . 'identification' ?>" class="nav-link" onclick="loading();">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    SPK Identification
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-folder2-open"></i>
                        <p>
                            Varification
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url() . 'verification' ?>" class="nav-link" onclick="loading();">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    SPK Verification
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">MASTER DATA</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-gear"></i>
                        <p>
                            Common Data
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('tonnage') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Tonnage
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('workshop') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Workshop
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('satuan') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    UoM (Unit of Measure)
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('material_category') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Material Category
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('material') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    List of Material
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('machine') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Machine
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('equipment_type') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Equipment Type
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('defect') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Defect/Problem
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('sub_defect') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Sub Defect/Problem
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('posisi_defect') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Defect Position
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('repair_reason') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Repair Reason
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() . 'problem_category' ?>" onclick="loading();" class="nav-link">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Problem Category
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('departemen') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    list of Department
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('leader') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Leader/Supervisor
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('karyawan') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    List of Employee
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('lokasi') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    Location
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('supplier') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    List of Supplier
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">APP SETUP</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-person-gear"></i>
                        <p>
                            User Management
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('users') ?>" class="nav-link" onclick="loading()">
                                <i class="nav-icon bi bi-arrow-right-circle"></i>
                                <p>
                                    User List
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-building"></i>
                        <p>
                            Master Data Seeder
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url() . 'seeder' ?>" class="nav-link" onlick="loading()">
                                <i class="bi bi-arrow-right-circle"></i>
                                <p>
                                    Master Data Seeder
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url() . 'logout' ?>" class="nav-link" onclick="loading()">
                        <i class="nav-icon bi bi-box-arrow-left"></i>
                        <p>
                            Log Out
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>