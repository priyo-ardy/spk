<?php

namespace App\Controllers\AppSetup\UserManagement\UserList;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AppSetup\UserManagement\UserList\UserListModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Database;
use Config\Services;
use Exception;

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
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();

        try {
            $user_id = generate_uuid();
            $user_name = trim($this->request->getPost('user_name'));
            $full_name = trim($this->request->getPost('full_name'));
            $user_phone = trim($this->request->getPost('phone_number'));
            $phone_hash = phone_hash($user_phone);
            $user_email = trim($this->request->getPost('email_address'));
            $email_hash = email_hash($user_email);
            $user_password = trim($this->request->getPost('user_password'));
            $password_hash = password_hash($user_password, PASSWORD_BCRYPT);
            $user_level = trim($this->request->getPost('user_level'));
            $user_image = $this->request->getFile('user_image');

            $rules = [
                'user_name' => [
                    'rules' => 'required|min_length[4]|max_length[50]',
                    'errors' => [
                        'required' => "Username is required",
                        'min_length' => "The minimum character of username is {param} character",
                        'max_length' => "The maximum character of username is {param} character"
                    ]
                ],
                'full_name' => [
                    'rules' => 'required|min_length[3]|max_length[150]',
                    'errors' => [
                        'required' => "User full name is required",
                        'min_length' => "The minimum character of user full name is {param} character",
                        'max_length' => "The maximum character of user full name is {param} character"
                    ]
                ],
                'phone_number' => [
                    'rules' => 'required|min_length[1]|max_length[20]|numeric',
                    'errors' => [
                        'required' => "User phone number is required",
                        'min_length' => "The minimum character of user phone number is {param} character",
                        'max_length' => "The maximum character of user phone number is {param} character",
                        'numeric' => "The user phone number just accept numeric format only"
                    ]
                ],
                'email_address' => [
                    'rules' => 'required|min_length[5]|max_length[150]|valid_email',
                    'errors' => [
                        'required' => "The user email address is required",
                        'min_length' => "The minimum charcter of user email address is {param} character",
                        'max_length' => "The maximum charcter of user email address is {param} character",
                        'valid_email' => "The user email address must have a valid email format"
                    ]
                ],
                'user_password' => [
                    'rules' => 'required|min_length[6]|max_length[20]',
                    'errors' => [
                        'required' => "The user password is required",
                        'min_length' => "The minimum user password is {param} character",
                        'max_length' => "The maximum user password is {param} character",
                    ]
                ],
                'user_level' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "The user level is required"
                    ]
                ],
            ];

            if (!empty($user_image)) {
                $rules = array_merge($rules, [
                    'user_image' => [
                        'label' => 'Foto Karyawan',
                        'rules' => 'uploaded[user_image]|max_size[user_image,51200]|is_image[user_image]|mime_in[user_image,image/jpg,image/jpeg,image/png]|ext_in[user_image,jpg,jpeg,png]',
                        'errors' => [
                            'uploaded' => 'Employee photo is required',
                            'max_size' => 'Employee photo maximum size is 50MB',
                            'is_image' => 'Employee photo must image file type (JPG/JPEG/PNG)',
                            'mime_in' => 'Employee photo file format must JPG, JPEG atau PNG',
                            'ext_in' => 'Employee photo file extension mus .jpg, .jpeg atau .png'
                        ]
                    ],
                ]);
            }

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode("<br>", $this->validasi->getErrors());
                log_action($this->module, $aksi, "error", current_url(), "Validation failed", '', json_encode([
                    'data' => $this->validasi->getErrors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed " . $error_message);
            }

            $check_phone = $this->userModel->checkUserPhone($phone_hash);
            if ($check_phone) {
                return pesan(ResponseInterface::HTTP_CONFLICT, "Phone number already registered");
            }

            $check_email = $this->userModel->checkUserEmail($email_hash);
            if ($check_email) {
                return pesan(ResponseInterface::HTTP_CONFLICT, "Email address already registered");
            }

            $data = [
                'user_id' => $user_id,
                'user_name' => $user_name,
                'full_name' => ucwords($full_name),
                'user_phone' => enkripsi($user_phone),
                'phone_hash' => $phone_hash,
                'user_email' => enkripsi($user_email),
                'email_hash' => $email_hash,
                'user_password' => $password_hash,
                'user_level' => $user_level,
                'created_by' => $this->NIK,
            ];

            if (!empty($user_image)) {
                $uploadPath = WRITEPATH . 'uploads/user_image';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                    chmod($uploadPath, 0777);
                }

                if ($user_image->isValid() && !$user_image->hasMoved()) {
                    $fileName = $user_name . '.' . $user_image->getExtension();
                    $user_image->move($uploadPath, $fileName, true);

                    $data = array_merge($data, [
                        'user_image' => $fileName
                    ]);
                }
            }

            $insert = $this->userModel->insert($data);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", "failed to create a new user", '', json_encode([
                    'data' => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to create a new user data, there was an error during processing your request");
            }

            if (!$insert) {
                log_action($this->module, $aksi, "error", "Failed to create a new user data, there was an error during processing your request, there was an error during processing your request, please try again later or contact your administrator", '', json_encode([
                    'data' => $this->userModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to create a new user data, there was an error during processing your request, there was an error during processing your request, please try again later or contact your administrator");
            }

            log_action($this->module, $aksi, "success", current_url(), 'Successfully created a new user data', '', json_encode([
                'data' => $data
            ]));

            return pesan(ResponseInterface::HTTP_CREATED, "Successfully created a new user data with username <strong>$user_name</strong>");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured, please check log for details", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured, please check the log data for details " . $e->getMessage() . " " . json_encode($data));
        }
    }
}
