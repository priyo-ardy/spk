<?php

namespace App\Controllers\AppSetup\UserManagement\UserList;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AppSetup\UserManagement\UserList\UserListModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Database;
use Config\Services;

class UserList extends BaseController
{
    protected $module;
    protected $userModel;
    protected $masterModel;
    protected $dataTable;
    protected $db;
    protected $validasi;
    protected $enkripsi;

    public function __construct()
    {
        $this->module = "User management";
        $this->userModel = new UserListModel();
        $this->masterModel = new MasterModel();
        $this->db = Database::connect();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = "";
    }

    public function index()
    {
        $aksi = "open";

        log_action($this->module, $aksi, "info", current_url(), "Opening user list page");

        $data = [
            'title' => 'List of User',
            'footer' => [
                'AppSetup/UserManagement/UserList/user.js'
            ]
        ];

        return view('AppSetup/UserManagement/UserList/index', $data);
    }
}
