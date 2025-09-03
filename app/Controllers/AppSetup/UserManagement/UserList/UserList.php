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
                '<script src="' . base_url() . 'js/AppSetup/UserManagement/UserList/user.js' . '"></script>'
            ]
        ];

        return view('AppSetup/UserManagement/UserList/index', $data);
    }

    function add()
    {
        $aksi = "open";

        log_action($this->module, $aksi, "info", current_url(), "Opening create new user");

        $data = [
            'title' => 'Create New User',
            'footer' => [
                '<script src="' . base_url() . 'js/AppSetup/UserManagement/UserList/add.js' . '"></script>'
            ]
        ];

        return view('AppSetup/UserManagement/UserList/add', $data);
    }

    function checkUserName()
    {
        $aksi = "Check username";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                // throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input request is not a valid JSON object");
            }

            if (!isset($json_data['username'])) {
                log_action($this->module, $aksi, "error", current_url(), "Username is missing in the JSON input");
                // throw new \Exception("Username is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Username is missing in JSON input");
            }

            $user_name = $json_data['username'];
            $check_username = $this->userModel->checkUser($user_name);
            if ($check_username) {
                log_action($this->module, $aksi, "error", current_url(), "Username $user_name is already taken");
                // throw new \Exception("Username is already taken");
                return pesan(ResponseInterface::HTTP_CONFLICT, "Username already taken");
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Failed to getting user name data", '', json_encode([
                    'data' => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to getting user name data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Username available");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured : " . $e->getMessage());
        }
    }

    function checkUserPhone()
    {
        $aksi = "Check user phone";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                // throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input request is not a valid JSON object");
            }

            if (!isset($json_data['user_phone'])) {
                log_action($this->module, $aksi, "error", current_url(), "Username is missing in the JSON input");
                // throw new \Exception("Username is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Username is missing in JSON input");
            }

            $user_phone = $json_data['user_phone'];
            $hash = phone_hash($user_phone);

            $check_user_phone = $this->userModel->checkUserPhone($hash);
            if ($check_user_phone) {
                log_action($this->module, $aksi, "error", current_url(), "Phone number $hash already taken");
                return pesan(ResponseInterface::HTTP_CONFLICT, "Phone number already taken");
            }

            return pesan(ResponseInterface::HTTP_OK, "Phone number is available");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured : " . $e->getMessage());
        }
    }

    function checkUserEmail()
    {
        $aksi = "Check user email";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                // throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input request is not a valid JSON object");
            }

            if (!isset($json_data['user_email'])) {
                log_action($this->module, $aksi, "error", current_url(), "User phone number is missing in the JSON input");
                // throw new \Exception("User phone number is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "User phone number is missing in JSON input");
            }

            $user_email = $json_data['user_email'];
            $hash = email_hash($user_email);

            $check_user_email = $this->userModel->checkUserEmail($hash);
            if ($check_user_email) {
                log_action($this->module, $aksi, "error", current_url(), "Email address $hash already taken");
                return pesan(ResponseInterface::HTTP_CONFLICT, "Email address already taken");
            }

            return pesan(ResponseInterface::HTTP_OK, "Email address is available");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured : " . $e->getMessage());
        }
    }

    function saveUser()
    {
        $aksi = "save";
    }
}
