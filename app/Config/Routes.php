<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth\Auth::index');
$routes->post('/proses', 'Auth\Auth::prosesLogin');
$routes->get('forgot-password', 'Auth\Auth::forgotPassword');
$routes->post('reset-password', 'Auth\Auth::resetPassword');
$routes->post('change', 'Auth\Auth::changePassword');
$routes->get('logout', 'Auth\Auth::logOut');
$routes->get('recover-password/(:any)', 'Auth\Auth::recoverPassword/$1');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->group('/dashboard', static function ($routes) {
        $routes->get('', 'Dashboard\Dashboard::index');
    });

    // Routes for system administrator and administrator
    $routes->group('/spk', static function ($routes) {
        $routes->get('', 'Transaction\SPK\SPK\SPK::index');
        $routes->post('table', 'Transaction\SPK\SPK\SPK::loadTable');
        $routes->get('add', 'Transaction\SPK\SPK\SPK::add');
        $routes->post('save', 'Transaction\SPK\SPK\SPK::saveData');
        $routes->get('show/(:any)', 'Transaction\SPK\SPK\SPK::showData/$1');
        $routes->post('update', 'Transaction\SPK\SPK\SPK::updateData');
        $routes->post('delete_image', 'Transaction\SPK\SPK\SPK::deleteImage');
        $routes->post('submit', 'Transaction\SPK\SPK\SPK::submitData');
        $routes->post('undo', 'Transaction\SPK\SPK\SPK::undoData');
        $routes->post('approve', 'Transaction\SPK\SPK\SPK::approveData');
        $routes->post('un_approve', 'Transaction\SPK\SPK\SPK::unApproveData');
        $routes->post('prev', 'Transaction\SPK\SPK\SPK::pevData');
        $routes->post('next', 'Transaction\SPK\SPK\SPK::nextData');
        $routes->get('export', 'Transaction\SPK\SPK\SPK::exportData');
        $routes->get('image/(:any)', 'Transaction\SPK\SPK\SPK::showImage/$1');
    });

    // Routes SPK for mold engineer
    $routes->group('/mold_spk', static function ($routes) {
        $routes->get('', 'Transaction\SPK\Mold\MoldSpk::index');
        $routes->post('table', 'Transaction\SPK\Mold\MoldSpk::loadTable');
        $routes->post('get_data', 'Transaction\SPK\Mold\MoldSpk::getSpkData');
        $routes->post('confirm', 'Transaction\SPK\Mold\MoldSpk::konfirmSelesai');
        $routes->post('image', 'Transaction\SPK\Mold\MoldSpk::showImage');
        $routes->post('get_finish', 'Transaction\SPK\Mold\MoldSpk::getPlannerConfirm');
        $routes->post('finish', 'Transaction\SPK\Mold\MoldSpk::finishTransaction');
    });

    $routes->group('/planer', static function ($routes) {
        $routes->get('', 'Transaction\SPK\Planner\PlannerSPK::index');
        $routes->post('table', 'Transaction\SPK\Planner\PlannerSPK::loadTable');
        $routes->post('get_data', 'Transaction\SPK\Planner\PlannerSPK::getData');
        $routes->post('confirm', 'Transaction\SPK\Planner\PlannerSPK::konfirmSelesai');
    });

    $routes->group('/quality', static function ($routes) {
        $routes->get('', 'Transaction\SPK\Quality\QualitySPK::index');
        $routes->post('get_data', 'Transaction\SPK\Quality\QualitySPK::getQualityData');
        $routes->post('submit', 'Transaction\SPK\Quality\QualitySPK::submitData');
    });

    // Transaction -> Identification -> mold
    $routes->group('identifikasi_mold', static function ($routes) {
        $routes->get('', 'Transaction\Identification\Mold\IdentificationMold::index');
    });

    // SPK Identification
    $routes->group('identification', static function ($routes) {
        $routes->get('', 'Transaction\Identification\Identification::index');
        $routes->post('generate-from-spk', 'Transaction\Identification\Identification::generateFromSpk');
    });

    // SPK Verification
    $routes->group('verification', static function ($routes) {
        $routes->get('', 'Transaction\Verification\Verification::index');
    });

    // Master Data -> Material Management -> Material Category
    $routes->group('/material_category', static function ($routes) {
        $routes->get('', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::index');
        $routes->post('table', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::loadTable');
        $routes->post('save', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::saveData');
        $routes->post('get', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::getData');
        $routes->post('update', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::updateData');
        $routes->post('delete', 'MasterData\MaterialManagement\MaterialCategory\MaterialCategory::deleteData');
    });

    $routes->group('/equipment_type', static function ($routes) {
        $routes->get('', 'MasterData\MaterialManagement\EquipmentType\EquipmentType::index');
        $routes->post('table', 'MasterData\MaterialManagement\EquipmentType\EquipmentType::loadTable');
        $routes->post('save', 'MasterData\MaterialManagement\EquipmentType\EquipmentType::saveData');
        $routes->post('get', 'MasterData\MaterialManagement\EquipmentType\EquipmentType::getData');
        $routes->post('update', 'MasterData\MaterialManagement\EquipmentType\EquipmentType::updateData');
        $routes->post('delete', 'MasterData\MaterialManagement\EquipmentType\EquipmentType::deleteData');
    });

    $routes->group('/departemen', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Departemen\Departemen::index');
        $routes->post('table', 'MasterData\CommonData\Departemen\Departemen::loadTable');
        $routes->post('save', 'MasterData\CommonData\Departemen\Departemen::saveData');
        $routes->post('get', 'MasterData\CommonData\Departemen\Departemen::getData');
        $routes->post('update', 'MasterData\CommonData\Departemen\Departemen::updateData');
        $routes->post('delete', 'MasterData\CommonData\Departemen\Departemen::deleteData');
    });

    $routes->group('/karyawan', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Karyawan\Karyawan::index');
        $routes->post('table', 'MasterData\CommonData\Karyawan\Karyawan::loadTable');
        $routes->post('save', 'MasterData\CommonData\Karyawan\Karyawan::saveData');
        $routes->post('get', 'MasterData\CommonData\Karyawan\Karyawan::getData');
        $routes->post('update', 'MasterData\CommonData\Karyawan\Karyawan::updateData');
        $routes->post('delete', 'MasterData\CommonData\Karyawan\Karyawan::deleteData');
    });

    $routes->group('/lokasi', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Lokasi\Lokasi::index');
        $routes->post('table', 'MasterData\CommonData\Lokasi\Lokasi::loadTable');
        $routes->post('save', 'MasterData\CommonData\Lokasi\Lokasi::saveData');
        $routes->post('get', 'MasterData\CommonData\Lokasi\Lokasi::getData');
        $routes->post('update', 'MasterData\CommonData\Lokasi\Lokasi::updateData');
        $routes->post('delete', 'MasterData\CommonData\Lokasi\Lokasi::deleteData');
    });

    // Master Data -> Common Data -> Workshop
    $routes->group('/workshop', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Workshop\Workshop::index');
        $routes->post('table', 'MasterData\CommonData\Workshop\Workshop::loadTable');
        $routes->post('save', 'MasterData\CommonData\Workshop\Workshop::saveData');
        $routes->post('get', 'MasterData\CommonData\Workshop\Workshop::getData');
        $routes->post('update', 'MasterData\CommonData\Workshop\Workshop::updateData');
        $routes->post('delete', 'MasterData\CommonData\Workshop\Workshop::deleteData');
    });

    // Master Data -> Common Data -> Defect
    $routes->group('/defect', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Defect\Defect::index');
        $routes->post('table', 'MasterData\CommonData\Defect\Defect::loadTable');
        $routes->post('save', 'MasterData\CommonData\Defect\Defect::saveData');
        $routes->post('get', 'MasterData\CommonData\Defect\Defect::getData');
        $routes->post('update', 'MasterData\CommonData\Defect\Defect::updateData');
        $routes->post('delete', 'MasterData\CommonData\Defect\Defect::deleteData');
        $routes->post('generate_defect', 'MasterData\CommonData\Defect\Defect::generateDefectList');
    });

    $routes->group('/satuan', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Satuan\Satuan::index');
        $routes->post('table', 'MasterData\CommonData\Satuan\Satuan::loadTable');
        $routes->post('save', 'MasterData\CommonData\Satuan\Satuan::saveData');
        $routes->post('get', 'MasterData\CommonData\Satuan\Satuan::getData');
        $routes->post('update', 'MasterData\CommonData\Satuan\Satuan::updateData');
        $routes->post('delete', 'MasterData\CommonData\Satuan\Satuan::deleteData');
    });

    // Master Data -> Common Data -> Sub Defect
    $routes->group('/sub_defect', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\SubDefect\SubDefect::index');
        $routes->post('table', 'MasterData\CommonData\SubDefect\SubDefect::loadTable');
        $routes->post('save', 'MasterData\CommonData\SubDefect\SubDefect::saveData');
        $routes->post('get', 'MasterData\CommonData\SubDefect\SubDefect::getData');
        $routes->post('update', 'MasterData\CommonData\SubDefect\SubDefect::updateData');
        $routes->post('delete', 'MasterData\CommonData\SubDefect\SubDefect::deleteData');
        $routes->post('get_list', 'MasterData\CommonData\SubDefect\SubDefect::getSubDefectByDefect');
    });

    $routes->group('/posisi_defect', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\PosisiDefect\PosisiDefect::index');
        $routes->post('table', 'MasterData\CommonData\PosisiDefect\PosisiDefect::loadTable');
        $routes->post('save', 'MasterData\CommonData\PosisiDefect\PosisiDefect::saveData');
        $routes->post('get', 'MasterData\CommonData\PosisiDefect\PosisiDefect::getData');
        $routes->post('update', 'MasterData\CommonData\PosisiDefect\PosisiDefect::updateData');
        $routes->post('delete', 'MasterData\CommonData\PosisiDefect\PosisiDefect::deleteData');
    });

    // Master Data -> Common Data -> Tonnage
    $routes->group('/tonnage', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Tonnage\Tonnage::index');
        $routes->post('table', 'MasterData\CommonData\Tonnage\Tonnage::loadTable');
        $routes->post('save', 'MasterData\CommonData\Tonnage\Tonnage::saveData');
        $routes->post('get', 'MasterData\CommonData\Tonnage\Tonnage::getData');
        $routes->post('update', 'MasterData\CommonData\Tonnage\Tonnage::updateData');
        $routes->post('delete', 'MasterData\CommonData\Tonnage\Tonnage::deleteData');
    });

    // Master Data -> Common Data -> Machine
    $routes->group('/machine', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Machine\Machine::index');
        $routes->get('add', 'MasterData\CommonData\Machine\Machine::add');
        $routes->get('details/(:any)', 'MasterData\CommonData\Machine\Machine::details/$1');
        $routes->post('table', 'MasterData\CommonData\Machine\Machine::loadTable');
        $routes->post('save', 'MasterData\CommonData\Machine\Machine::saveData');
        $routes->post('update', 'MasterData\CommonData\Machine\Machine::updateData');
        $routes->post('prev', 'MasterData\CommonData\Machine\Machine::prevData');
        $routes->post('next', 'MasterData\CommonData\Machine\Machine::nextData');
        $routes->get('export', 'MasterData\CommonData\Machine\Machine::exportData');
    });

    // Master Data -> Material Management -> Material
    $routes->group('/material', static function ($routes) {
        $routes->get('', 'MasterData\MaterialManagement\Material\Material::index');
        $routes->post('generate_material', 'MasterData\MaterialManagement\Material\Material::generateMaterialList');
        $routes->get('add', 'MasterData\MaterialManagement\Material\Material::addData');
        $routes->get('details/(:any)', 'MasterData\MaterialManagement\Material\Material::details/$1');
        $routes->post('get_material', 'MasterData\MaterialManagement\Material\Material::getMaterialData');
        $routes->post('table', 'MasterData\MaterialManagement\Material\Material::loadTable');
        $routes->post('save', 'MasterData\MaterialManagement\Material\Material::saveData');
        $routes->post('update', 'MasterData\MaterialManagement\Material\Material::updateData');
        $routes->post('prev', 'MasterData\MaterialManagement\Material\Material::prevData');
        $routes->post('next', 'MasterData\MaterialManagement\Material\Material::nextData');
        $routes->get('export', 'MasterData\MaterialManagement\Material\Material::exportData');
    });

    $routes->group('/repair_reason', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\RepairReason\RepairReason::index');
        $routes->post('table', 'MasterData\CommonData\RepairReason\RepairReason::loadTable');
        $routes->post('save', 'MasterData\CommonData\RepairReason\RepairReason::saveData');
        $routes->post('get', 'MasterData\CommonData\RepairReason\RepairReason::getData');
        $routes->post('update', 'MasterData\CommonData\RepairReason\RepairReason::updateData');
        $routes->post('delete', 'MasterData\CommonData\RepairReason\RepairReason::deleteData');
    });

    $routes->group('/leader', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Leader\Leader::index');
        $routes->post('table', 'MasterData\CommonData\Leader\Leader::loadTable');
        $routes->post('save', 'MasterData\CommonData\Leader\Leader::saveData');
        $routes->post('get', 'MasterData\CommonData\Leader\Leader::getData');
        $routes->post('update', 'MasterData\CommonData\Leader\Leader::updateData');
        $routes->post('delete', 'MasterData\CommonData\Leader\Leader::deleteData');
        $routes->post('namaKaryawan', 'MasterData\CommonData\Leader\Leader::getNamaByNIK');
    });

    $routes->group('/supplier', static function ($routes) {
        $routes->get('', 'MasterData\CommonData\Supplier\Supplier::index');
        $routes->post('table', 'MasterData\CommonData\Supplier\Supplier::loadTable');
        $routes->get('add', 'MasterData\CommonData\Supplier\Supplier::addData');
        $routes->post('save', 'MasterData\CommonData\Supplier\Supplier::saveData');
        $routes->get('show/(:any)', 'MasterData\CommonData\Supplier\Supplier::showData/$1');
        $routes->post('update', 'MasterData\CommonData\Supplier\Supplier::updateData');
        $routes->post('prev', 'MasterData\CommonData\Supplier\Supplier::prevData');
        $routes->post('next', 'MasterData\CommonData\Supplier\Supplier::nextData');
        $routes->post('delete', 'MasterData\CommonData\Supplier\Supplier::deleteData');
        $routes->get('export', 'MasterData\CommonData\Supplier\Supplier::exportData');
    });

    // App Setup -> User Management -> User List
    $routes->group('/users', static function ($routes) {
        $routes->get('', 'AppSetup\UserManagement\UserList\UserList::index');
        $routes->get('add', 'AppSetup\UserManagement\UserList\UserList::add');
        $routes->post('check_user', 'AppSetup\UserManagement\UserList\UserList::checkUserName');
        $routes->post('check_phone', 'AppSetup\UserManagement\UserList\UserList::checkUserPhone');
        $routes->post('check_email', 'AppSetup\UserManagement\UserList\UserList::checkUserEmail');
        $routes->post('save', 'AppSetup\UserManagement\UserList\UserList::saveUser');
        $routes->post('table', 'AppSetup\UserManagement\UserList\UserList::loadTable');
        $routes->get('show/(:any)', 'AppSetup\UserManagement\UserList\UserList::getUser/$1');
        $routes->post('update', 'AppSetup\UserManagement\UserList\UserList::updateData');
        $routes->post('prev', 'AppSetup\UserManagement\UserList\UserList::prevData');
        $routes->post('next', 'AppSetup\UserManagement\UserList\UserList::nextData');
        $routes->post('change_password', 'AppSetup\UserManagement\UserList\UserList::changeUserPassword');
        $routes->post('disable', 'AppSetup\UserManagement\UserList\UserList::disableUser');
    });

    $routes->group('/seeder', static function ($routes) {
        $routes->get('', 'AppSetup\Seeder\MasterData::index');
        $routes->post('generate', 'AppSetup\Seeder\MasterData::generateData');
    });

    $routes->group('/setup', static function ($routes) {
        $routes->get('', 'Admin\Setup\Setup::index');
    });

    // Identification
    $routes->group('/identification', static function ($routes) {
        $routes->get('show/(:any)', 'Transaction\Identification\Identification::showData/$1');
    });
});
