<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth\Auth::index');
$routes->post('/proses', 'Auth\Auth::prosesLogin');
$routes->get('logout', 'Auth\Auth::logOut');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->group('/dashboard', static function ($routes) {
        $routes->get('', 'Dashboard\Dashboard::index');
    });

    $routes->group('/spk', static function ($routes) {
        $routes->get('', 'Transaction\SPK\SPK\SPK::index');
        $routes->get('add', 'Transaction\SPK\SPK\SPK::add');
    });

    // Transaction -> SPK -> General
    $routes->group('/spk_general', static function ($routes) {
        $routes->get('', 'Transaction\SPK\General\GeneralSpk::index');
        $routes->get('add', 'Transaction\SPK\General\GeneralSpk::add');
        $routes->post('save', 'Transaction\SPK\General\GeneralSpk::saveData');
        $routes->post('table', 'Transaction\SPK\General\GeneralSpk::loadTable');
        $routes->get('show/(:any)', 'Transaction\SPK\General\GeneralSpk::showData/$1');
        $routes->post('update', 'Transaction\SPK\General\GeneralSpk::updateData');
        $routes->post('image', 'Transaction\SPK\General\GeneralSpk::showImage');
        $routes->post('delete_image', 'Transaction\SPK\General\GeneralSpk::DeleteImage');
        $routes->get('export', 'Transaction\SPK\General\GeneralSpk::exportData');
        $routes->post('prev', 'Transaction\SPK\General\GeneralSpk::prevData');
        $routes->post('next', 'Transaction\SPK\General\GeneralSpk::nextData');
    });

    $routes->group('/spk_mold', static function ($routes) {
        $routes->get('', 'Transaction\SPK\Mold\MoldSpk::index');
        $routes->get('add', 'Transaction\SPK\Mold\MoldSpk::add');
        $routes->post('save', 'Transaction\SPK\Mold\MoldSpk::saveData');
        $routes->post('table', 'Transaction\SPK\Mold\MoldSpk::loadTable');
        $routes->get('show/(:any)', 'Transaction\SPK\Mold\MoldSpk::showData/$1');
        $routes->post('update', 'Transaction\SPK\Mold\MoldSpk::updateData');
        $routes->post('image', 'Transaction\SPK\Mold\MoldSpk::showImage');
        $routes->post('prev', 'Transaction\SPK\Mold\MoldSpk::prevData');
        $routes->post('next', 'Transaction\SPK\Mold\MoldSpk::nextData');
        $routes->post('delete_image', 'Transaction\SPK\Mold\MoldSpk::DeleteImage');
        $routes->post('image', 'Transaction\SPK\Mold\MoldSpk::showImage');
        $routes->get('export', 'Transaction\SPK\Mold\MoldSpk::exportData');
    });

    // Transaction -> Identification -> mold
    $routes->group('identifikasi_mold', static function ($routes) {
        $routes->get('', 'Transaction\Identification\Mold\IdentificationMold::index');
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
    $routes->group('/defect', static function ($routes) {});

    // Master Data -> Common Data -> Sub Defect
    $routes->group('/sub_defect', static function ($routes) {
        $routes->post('get_list', 'MasterData\CommonData\SubDefect\SubDefect::getSubDefectByDefect');
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
        $routes->post('machine_data', 'MasterData\CommonData\Machine\Machine::getDataById');
    });

    // Master Data -> Material Management -> Material
    $routes->group('/material', static function ($routes) {
        $routes->get('', 'MasterData\MaterialManagement\Material\Material::index');
        $routes->post('get_material', 'MasterData\MaterialManagement\Material\Material::getMaterialData');
    });

    // App Setup -> User Management -> User List
    $routes->group('/users', static function ($routes) {
        $routes->get('', 'AppSetup\UserManagement\UserList\UserList::index');
        $routes->get('add', 'AppSetup\UserManagement\UserList\UserList::add');
        $routes->post('check_user', 'AppSetup\UserManagement\UserList\UserList::checkUserName');
        $routes->post('check_phone', 'AppSetup\UserManagement\UserList\UserList::checkUserPhone');
        $routes->post('check_email', 'AppSetup\UserManagement\UserList\UserList::checkUserEmail');
        $routes->post('save', 'AppSetup\UserManagement\UserList\UserList::saveUser');
    });
});
