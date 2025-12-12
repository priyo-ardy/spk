<?php

namespace App\Controllers\MasterData\CommonData\Supplier;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\Supplier\SupplierModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Services;
use Config\Database;

class Supplier extends BaseController
{
    protected $module;
    protected $supplierModel;
    protected $masterModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;
    protected $db;

    public function __construct()
    {
        $this->module = "Supplier";
        $this->supplierModel = new SupplierModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
        $this->db = Database::connect();

        $table = 'm_supplier';
        $column_order = [];
        $column_search = [];
        $order = array('code' => 'ASC');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    public function index()
    {
        $aksi = "Supplier list";
        log_action($this->module, $aksi, 'info', current_url(), 'Opening list of supplier page');

        $data = [
            'title' => "List of Supplier",
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Supplier/supplier.js' . '"></script>',
            ]
        ];

        return view('MasterData/CommonData/Supplier/index', $data);
    }

    function loadTable()
    {
        $aksi = "Load supplier table";
        log_action($this->module, $aksi, 'generate table', current_url(), "Load supplier table");

        $lists = $this->dataTable->get_datatables();
        $data = [];

        foreach ($lists as $item) {
            $row = [];

            $row[] = '
                <a href="" class="text-primary fw-bolder link-underline-opacity-0 link-underline-opacity-100-hover" onclick="loading()">' . $item->code . '</a>
            ';
            $row[] = $item->name;
            $row[] = $item->address;
            $row[] = ($item->phone_no) ? dekripsi($item->phone_no) : '';
            $row[] = ($item->email_address) ? dekripsi($item->email_address) : '';
            $row[] = $item->contact_person;
            $row[] = $item->remark;
            $row[] = '
                <button type="button" class="text-danger btn text-danger shadow-none btn-sm rounded-0" onclick="deleteData(`' . enkripsi($item->id) . '`)">
                    <i class="fas fa-times"></i>
                </button>
            ';

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
    }

    function addData()
    {
        $aksi = "Add supplier";
        log_action($this->module, $aksi, 'info', current_url(), 'Open add new supplier data');


        $data = [
            'title' => 'Add New Supplier',
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Supplier/add.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/Supplier/add', $data);
    }

    function saveData()
    {
        $aksi = "Save supplier";
        log_action($this->module, $aksi, "info", current_url(), "Preparing to save new supplier data");

        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Failed to save new supplier data, invalid request method");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        $this->db->transStart();
        try {
            $id = generate_uuid();
            $code = $this->masterModel->generateCode('m_supplier', 'code', 'SUPP-', 6);
            $name = htmlspecialchars(trim($this->request->getPost('data_name')), ENT_QUOTES, 'UTF-8');
            $alamat = htmlspecialchars(trim($this->request->getPost('data_alamat')), ENT_QUOTES, 'UTF-8');
            $phone = htmlspecialchars(trim($this->request->getPost('data_phone')), ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars(trim($this->request->getPost('data_email')), ENT_QUOTES, 'UTF-8');
            $contact_person = htmlspecialchars(trim($this->request->getPost('data_contact')), ENT_QUOTES, 'UTF-8');
            $remark = strip_tags(trim($this->request->getPost('data_remark')));
            $encrypt_phone = ($phone) ? enkripsi($phone) : null;
            $encrypt_email = ($email) ? enkripsi($email) : null;
            $hash_phone = ($phone)  ? phone_hash($phone) : null;
            $hash_email = ($email)  ? email_hash($email) : null;

            $rules = [
                'data_name' => [
                    'rules' => 'required|min_length[3]|max_length[150]|trim|htmlspecialchars',
                    'errors' => [
                        'required' => 'Supplier name is required',
                        'min_length' => 'The minimum character of supplier name is {param} character',
                        'max_length' => 'The maximum character of supplier name is {param} character'
                    ]
                ],
            ];

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());
                log_action($this->module, $aksi, "error", current_url(), "Validation failed", '', json_encode([
                    'error_message' => $error_message
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, $error_message);
            }

            $data = [
                'id' => $id,
                'code' => $code,
                'name' => $name,
                'address' => $alamat,
                'phone_no' => $encrypt_phone,
                'phone_hash' => $hash_phone,
                'email_address' => $encrypt_email,
                'email_hash' => $hash_email,
                'contact_person' => $contact_person,
                'remark' => $remark,
                'created_by' => $this->NIK,
            ];

            $insert = $this->supplierModel->insert($data);

            if ($insert) {
                $this->db->transCommit();
                log_action($this->module, $aksi, "info", current_url(), "Successfully saved new supplier data");
                return pesan(ResponseInterface::HTTP_CREATED, "Successfully saved new supplier data", ['token' => enkripsi($id)]);
            } else {
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Failed to save new supplier data");
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to save new supplier data");
            }
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured :<br>" . $e->getMessage());
        }
    }

    function showData($id)
    {
        $id = dekripsi($id);
        $aksi = "Show supplier data";

        log_action($this->module, $aksi, 'info', current_url(), 'Open show supplier data');

        $data = [
            'title' => 'Show Supplier Data',
            'data' => $this->supplierModel->where('id', $id)->first(),
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Supplier/edit.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/Supplier/edit', $data);
    }

    function updateData()
    {
        $aksi = "Update supplier data";
        log_action($this->module, $aksi, 'info', current_url(), 'Preparing to update supplier data');

        $this->db->transStart();
        try {
            $token = htmlspecialchars(trim($this->request->getPost('data_token')), ENT_QUOTES, 'UTF-8');
            $id = dekripsi($token);
            $code = htmlspecialchars(trim($this->request->getPost('data_code')), ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars(trim($this->request->getPost('data_name')), ENT_QUOTES, 'UTF-8');
            $alamat = htmlspecialchars(trim($this->request->getPost('data_alamat')), ENT_QUOTES, 'UTF-8');
            $phone = htmlspecialchars(trim($this->request->getPost('data_phone')), ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars(trim($this->request->getPost('data_email')), ENT_QUOTES, 'UTF-8');
            $contact_person = htmlspecialchars(trim($this->request->getPost('data_contact')), ENT_QUOTES, 'UTF-8');
            $remark = strip_tags(trim($this->request->getPost('data_remark')));
            $encrypt_phone = ($phone) ? enkripsi($phone) : null;
            $encrypt_email = ($email) ? enkripsi($email) : null;
            $hash_phone = ($phone)  ? phone_hash($phone) : null;
            $hash_email = ($email)  ? email_hash($email) : null;

            $rules = [
                'data_token' => [
                    'rules' => 'required|trim|htmlspecialchars',
                    'errors' => [
                        'required' => 'Data token is required'
                    ]
                ],
                'data_code' => [
                    'rules' => 'required|min_length[3]|max_length[20]|trim|htmlspecialchars',
                    'errors' => [
                        'required' => 'Supplier code is required',
                        'min_length' => 'The minimum character of supplier code is {param} character',
                        'max_length' => 'The maximum character of supplier code is {param} character'
                    ]
                ],
                'data_name' => [
                    'rules' => 'required|min_length[3]|max_length[150]|trim|htmlspecialchars',
                    'errors' => [
                        'required' => 'Supplier name is required',
                        'min_length' => 'The minimum character of supplier name is {param} character',
                        'max_length' => 'The maximum character of supplier name is {param} character'
                    ]
                ],
                'data_alamat' => [
                    'rules' => 'trim|htmlspecialchars',
                ],
                'data_phone' => [
                    'rules' => 'trim|htmlspecialchars',
                ],
                'data_email' => [
                    'rules' => 'trim|valid_email',
                    'errors' => [
                        'valid_email' => 'Invalid email address format'
                    ]
                ],
                'data_contact' => [
                    'rules' => 'trim|htmlspecialchars',
                ],
                'data_remark' => [
                    'rules' => 'trim|htmlspecialchars',
                ],
            ];

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());
                log_action($this->module, $aksi, "error", current_url(), "Validation failed", '', json_encode([
                    'error_message' => $error_message
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, $error_message);
            }

            $data = [
                'name' => $name,
                'address' => $alamat,
                'phone_no' => $encrypt_phone,
                'phone_hash' => $hash_phone,
                'email_address' => $encrypt_email,
                'email_hash' => $hash_email,
                'contact_person' => $contact_person,
                'remark' => $remark,
                'updated_by' => $this->NIK,
            ];

            $update = $this->supplierModel->update($id, $data);

            if ($update) {
                $this->db->transCommit();
                log_action($this->module, $aksi, "success", current_url(), "Supplier data updated successfully", '', json_encode([
                    'id' => $id,
                    'code' => $code,
                    'name' => $name,
                    'alamat' => $alamat,
                    'phone' => $phone,
                    'email' => $email,
                    'contact_person' => $contact_person,
                    'remark' => $remark,
                ]));

                return pesan(ResponseInterface::HTTP_OK, "Supplier data updated successfully");
            }

            $this->db->transRollback();
            log_action($this->module, $aksi, "error", current_url(), "Failed to update supplier data", '', json_encode([
                'id' => $id,
                'code' => $code,
                'name' => $name,
                'alamat' => $alamat,
                'phone' => $phone,
                'email' => $email,
                'contact_person' => $contact_person,
                'remark' => $remark,
            ]));

            return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Failed to update supplier data");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured :<br>" . $e->getMessage());
        }
    }

    function prevData()
    {
        $aksi = "Prev data";
        log_action($this->module, $aksi, "info", current_url(), "Preparing previous supplier data");

        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Failed to get previous supplier data, invalid request method");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['code'])) {
                log_action($this->module, $aksi, "error", current_url(), "Supplier code is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Supplier code is missing in JSON input");
            }

            $code = $json_data['code'];

            $prev_data = $this->supplierModel->getPrevData($code);
            if (!$prev_data) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the first data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Previous supplier data found", ['token' => enkripsi($prev_data->id)]);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured :<br>" . $e->getMessage());
        }
    }

    function nextData()
    {
        $aksi = "Next data";
        log_action($this->module, $aksi, "info", current_url(), "Preparing next supplier data");

        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Failed to get next supplier data, invalid request method");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['code'])) {
                log_action($this->module, $aksi, "error", current_url(), "Supplier code is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Supplier code is missing in JSON input");
            }

            $code = $json_data['code'];

            $next_data = $this->supplierModel->getNextData($code);
            if (!$next_data) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the last data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Previous supplier data found", ['token' => enkripsi($next_data->id)]);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured :<br>" . $e->getMessage());
        }
    }

    function deleteData()
    {
        $aksi = "Delete data";
        log_action($this->module, $aksi, "info", current_url(), "Preparing to delete supplier data");

        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Failed to delete supplier data, invalid request method");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "Supplier data ID is missing in JSON input");
                throw new \Exception("Supplier data ID is missing in JSON input");
            }

            $id = dekripsi($json_data['token']);

            $getData = $this->supplierModel->where('id', $id)->first();
            if (!$getData) {
                log_action($this->module, $aksi, "error", current_url(), "Supplier data not found");
                throw new \Exception("Supplier data not found");
            }

            $delete = $this->supplierModel->delete($id);
            if (!$delete) {
                log_action($this->module, $aksi, "error", current_url(), "Failed to delete supplier data");
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to delete supplier data");
            }

            log_action($this->module, $aksi, "success", current_url(), "Supplier data deleted successfully");
            return pesan(ResponseInterface::HTTP_OK, "Supplier data deleted successfully");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured :<br>" . $e->getMessage());
        }
    }

    function exportData()
    {
        $aksi = "Export data";
        log_action($this->module, $aksi, "info", current_url(), "Preparing to export supplier data");

        if ($this->request->getMethod() !== 'GET') {
            log_action($this->module, $aksi, "error", current_url(), "Failed to export supplier data, invalid request method");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        try {
            $filename = "supplier_list" . date("Ymd_his") . 'xlsx';
            $headers = ['Code', 'Name', 'Address', 'Phone No', 'Email Addreess', 'Contact Person', 'Remark'];

            $dataCallback = function ($offset, $limit) {
                $column = 'code, name, 	address, phone_no, email_address, contact_person, remark';
                return $this->masterModel->getChunkedData('m_supplier', $offset, $limit, 'code', $column);
            };

            return export_decrypted_data($filename, $headers, ['phone_no', 'email_address'], $dataCallback);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured :<br>" . $e->getMessage());
        }
    }
}
