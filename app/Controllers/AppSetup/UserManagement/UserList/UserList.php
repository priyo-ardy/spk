<?php

namespace App\Controllers\AppSetup\UserManagement\UserList;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AppSetup\UserManagement\UserList\UserListModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use CodeIgniter\HTTP\Response;
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

        $table = "vw_user_auth";
        $column_order = ['user_name', 'full_name', 'nama_status', 'nama_level'];
        $column_search = ['user_name', 'full_name', 'nama_status', 'nama_level'];
        $order = array('user_name' => 'asc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    function loadTable()
    {
        try {
            $aksi = "table";
            log_action($this->module, $aksi, "info", current_url(), "Generating list of users");

            $lists = $this->dataTable->get_datatables();
            $data = [];
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $no = $start + 1;
            foreach ($lists as $item) {
                $row = [];
                $status = '';

                if ($this->NIK == $item->user_name) {
                    $status = 'disabled';
                }

                $row[] = $no;
                $row[] = '
                    <a href="' . base_url() . 'users/show/' . enkripsi($item->user_id) . '" class="text-primary fw-bolder link-underline-opacity-0 link-underline-opacity-100-hover" title="Click to edit">' . $item->user_name . '</a>
                ';
                $row[] = $item->full_name;
		$row[] ='';
		$row[] = '';
                //$row[] = ($item->user_phone) ? substr_replace(dekripsi($item->user_phone), '******', -6) : '';
                //$row[] = ($item->user_email) ? sensor_email(dekripsi($item->user_email)) : '';
                $row[] = $item->nama_level;
                $row[] = $item->nama_status;
                $row[] = ($item->last_login) ? date("d/M/Y H:i:s", strtotime($item->last_login)) : '';
                $row[] = $item->login_from;
                $row[] = '
                    <button type="button" class="btn btn-primary rounded-0 btn-sm" onclick="changePassword(`' . enkripsi($item->user_id) . '`)">
                        <i class="bi bi-key"></i>&ensp;Change Password
                    </button>
                ';
                $row[] = '
                    <button type="button" ' . $status . ' class="btn btn-sm shadow-none border-0" onclick="disableUser(`' . enkripsi($item->user_id) . '`)">
                        <i class="fas fa-times text-danger fw-bolder"></i>
                    </button>
                ';

                $no++;

                $data[] = $row;
            }

            $output = [
                "draw" => $_POST["draw"],
                "recordsTotal" => $this->dataTable->count_all(),
                "recordsFiltered" => $this->dataTable->count_filtered(),
                "data" => $data
            ];

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_OK, "Generate successfully")
                ->setJSON($output);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));


            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
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
            $token = $json_data['token'];


            if ($token !== '') {
                $user_id = dekripsi($token);
                $check_username = $this->userModel->checkUser($user_name, $user_id);
            } else {
                $check_username = $this->userModel->checkUser($user_name);
            }

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
            $token = $json_data['token'];

            $hash = phone_hash($user_phone);

            if ($token == '') {
                $check_user_phone = $this->userModel->checkUserPhone($hash);
            } else {
                $user_id = ($token) ? dekripsi($token) : '';
                $check_user_phone = $this->userModel->checkUserPhone($hash, $user_id);
            }

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
            $token = $json_data['token'];


            $hash = email_hash($user_email);

            if ($token == '') {
                $check_user_email = $this->userModel->checkUserEmail($hash);
            } else {
                $user_id = ($token) ? dekripsi($token) : '';
                $check_user_email = $this->userModel->checkUserEmail($hash, $user_id);
            }

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
            $password_hash = password_hash($user_password, PASSWORD_DEFAULT);
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

            if (!empty($user_image) && $user_image->getBasename() !== '') {
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
                'phone_hash' => phone_hash($user_phone),
                'user_email' => enkripsi($user_email),
                'email_hash' => $email_hash,
                'user_password' => $password_hash,
                'user_level' => $user_level,
                'created_by' => $this->NIK,
            ];

            if (!empty($user_image) && $user_image->getBasename() !== '') {
                $uploadPath = FCPATH . 'uploads/user_image';
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

    function getUser($token)
    {
        $aksi = "Open user details";
        log_action($this->module, $aksi, "info", current_url(), "Opening user details page");
        $get = $this->userModel->where('user_id', dekripsi($token))->first();

        $image_path = FCPATH . 'uploads/user_image/' . $get->user_image;

        $data = [
            'title' => 'Show User Details | ' . $get->user_name . ' | ' . $get->full_name,
            'token' => $token,
            'user_name' => $get->user_name,
            'user_image' => (file_exists($image_path)) ? 'uploads/user_image/' . $get->user_image : 'image/no-foto.jpg',
            'data' => $get,
            'footer' => [
                '<script src="' . base_url() . 'js/AppSetup/UserManagement/UserList/edit.js' . '"></script>'
            ]
        ];

        return view('AppSetup/UserManagement/UserList/edit', $data);
    }

    function updateData()
    {
        $aksi = "update user";

        $this->db->transStart();
        try {
            $token = trim($this->request->getPost('data_token'));
            $user_id = dekripsi($token);
            $user_name = trim($this->request->getPost('user_name'));
            $full_name = trim($this->request->getPost('full_name'));
            $user_phone = trim($this->request->getPost('phone_number'));
            $phone_hash = phone_hash($user_phone);
            $user_email = trim($this->request->getPost('email_address'));
            $email_hash = email_hash($user_email);
            $user_password = trim($this->request->getPost('user_password'));
            $password_hash = password_hash($user_password, PASSWORD_DEFAULT);
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
                'user_level' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "The user level is required"
                    ]
                ],
            ];

            if (!empty($user_image) && $user_image->getBasename() !== '') {
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

            $check_phone = $this->userModel->checkUserPhone($phone_hash, $user_id);
            if ($check_phone) {
                return pesan(ResponseInterface::HTTP_CONFLICT, "Phone number already registered");
            }

            $check_email = $this->userModel->checkUserEmail($email_hash, $user_id);
            if ($check_email) {
                return pesan(ResponseInterface::HTTP_CONFLICT, "Email address already registered");
            }

            $get = $this->userModel->where('user_id', $user_id)->first();

            $data = [
                'user_name' => $user_name,
                'full_name' => ucwords($full_name),
                'user_phone' => enkripsi($user_phone),
                'phone_hash' => $phone_hash,
                'user_email' => enkripsi($user_email),
                'email_hash' => $email_hash,
                'user_password' => $password_hash,
                'user_level' => $user_level,
                'updated_by' => $this->NIK,
            ];

            if (!empty($user_image) && $user_image->getBasename() !== '') {
                $uploadPath = FCPATH . 'uploads/user_image';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                    chmod($uploadPath, 0777);
                }

                if ($user_image->isValid() && !$user_image->hasMoved()) {
                    $fileName = $user_name . '.' . $user_image->getExtension();
                    if (file_exists($uploadPath . $get->user_image)) {
                        unlink($uploadPath . $get->user_image);
                    }

                    $user_image->move($uploadPath, $fileName, true);

                    $data = array_merge($data, [
                        'user_image' => $fileName
                    ]);
                }
            }

            $update = $this->userModel->update($user_id, $data);

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Update failed, something wrong happen", '', json_encode([
                    'data' => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed, there was an error during processing your request, please try again later or contact your administrator");
            }

            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Update failed, there was an error during processing your request, please try again later or contact your administrator", '', json_encode([
                    'data' => $this->userModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed, there was an error during processing your request, please try again later or contact your administrator");
            }

            log_action(
                $this->module,
                $aksi,
                "success",
                current_url(),
                "Update success",
                json_encode([
                    'data' => $this->userModel->where('user_id', $user_id)->first()
                ]),
                json_encode([
                    'data' => $data,
                    'where' => $user_id
                ])
            );

            return pesan(ResponseInterface::HTTP_OK, "Update success");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Update Failed, Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed !: <br>" . $e->getMessage());
        }
    }

    function prevData()
    {
        $aksi = "prev data";

        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input request is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "User token is missing in the JSON input");
                throw new \Exception("User token is missing in the JSON input");
            }

            $token = $json_data['token'];
            $user_id = dekripsi($token);

            $get = $this->userModel->where('user_id', $user_id)->first();
            if (!$get) {
                log_action($this->module, $aksi, "error", current_url(), "User not found");

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "User not found");
            }

            $prev_data = $this->userModel->getPrevData($get->user_name);
            if (!$prev_data) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the first data");
            }

            $data = [
                'token' => enkripsi($prev_data->user_id)
            ];

            return pesan(ResponseInterface::HTTP_OK, "Previous data found", $data);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_NOT_FOUND, $e->getMessage());
        }
    }

    function nextData()
    {
        $aksi = "next data";

        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input request is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "User token is missing in the JSON input");
                throw new \Exception("User token is missing in JSON input");
            }

            $token = $json_data['token'];
            $user_id = dekripsi($token);

            $get = $this->userModel->where('user_id', $user_id)->first();
            if (!$get) {
                log_action($this->module, $aksi, "error", current_url(), "User not found");

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "User not found");
            }

            $next_data = $this->userModel->getNextData($get->user_name);
            if (!$next_data) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the last data");
            }

            $data = [
                'token' => enkripsi($next_data->user_id)
            ];

            return pesan(ResponseInterface::HTTP_OK, "Previous data found", $data);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_NOT_FOUND, $e->getMessage());
        }
    }

    function changeUserPassword()
    {
        $aksi = "change user password";

        $this->db->transBegin();
        try {
            $token = trim($this->request->getPost('user_token'));
            $new_password = trim($this->request->getPost('new_password'));

            $user_id = dekripsi($token);
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $get = $this->userModel->where('user_id', $user_id)->first();
            if (!$get) {
                log_action($this->module, $aksi, "error", current_url(), "User not found", '', json_encode([
                    'data' => $user_id
                ]));

                throw new \Exception("User not found");
            }

            $data = [
                'user_password' => $password_hash,
                'updated_by' => $this->NIK
            ];

            $update = $this->userModel->update($user_id, $data);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();

                log_action($this->module, $aksi, "error", current_url(), "Change password failed", '', json_encode([
                    'data' => $this->db->error()
                ]));

                throw new \Exception("Failed to changed password");
            }

            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Failed to change password", '', json_encode([
                    'data' => $this->userModel->errors()
                ]));

                throw new \Exception("Failed to change user password, there was an error during processing your request, please try again later or contact your administrator");
            }

            log_action(
                $this->module,
                $aksi,
                "success",
                current_url(),
                "User password changed successfully",
                json_encode([
                    'data' => $get
                ]),
                json_encode([
                    'data' => $data,
                    'where' => $user_id
                ])
            );

            return pesan(ResponseInterface::HTTP_OK, "User password changed successfully");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    function disableUser()
    {
        $aksi = 'disable user';

        $this->db->transStart();
        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input request is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "User token is missing in the JSON input");
                throw new \Exception("User token is missing in the JSON input");
            }

            $token = $json_data['token'];
            $user_id = dekripsi($token);

            $get = $this->userModel->where('user_id', $user_id)->first();
            if (!$get) {
                log_action($this->module, $aksi, "error", current_url(), "User not found", '', json_encode([
                    'data' => $user_id
                ]));

                throw new \Exception("User not found");
            }

            $delete = $this->userModel->delete($user_id);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();

                log_action($this->module, $aksi, "error", current_url(), "Failed to disable user data", '', json_encode([
                    'data' => $this->db->error()
                ]));

                throw new \Exception("Failed to disable user data");
            }

            if (!$delete) {
                log_action($this->module, $aksi, "error", current_url(), "Failed to disable user data", '', json_encode([
                    'data' => $this->userModel->errors()
                ]));

                throw new \Exception("Failed to disable user data, there was an error during processing your request, please try again later or contact your administrator");
            }

            log_action($this->module, $aksi, "success", current_url(), "Successfully disabled user data", '', json_encode(['data' => $user_id]));

            return pesan(ResponseInterface::HTTP_OK, "Successfully disable user data");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
