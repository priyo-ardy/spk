<?php

namespace App\Controllers\MasterData\CommonData\Karyawan;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\Karyawan\KaryawanModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use CodeIgniter\HTTP\ResponsableInterface;
use Config\Services;

class Karyawan extends BaseController
{
    protected $karyawanModel;
    protected $dataTable;
    protected $masterModel;
    protected $module;
    protected $validasi;
    protected $enkripsi;

    public function __construct()
    {
        $this->module = "Employee";
        $this->karyawanModel = new KaryawanModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'm_karyawan';
        $column_order = ['NIK', 'nama'];
        $column_search = ['NIK', 'nama'];
        $order = array('NIK' => 'asc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    public function index()
    {
        $data = [
            'title' => 'Employee Management',
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Karyawan/karyawan.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/Karyawan/index', $data);
    }

    function loadTable()
    {
        log_action($this->module, 'table', 'info', current_url(), 'Generating employee list');

        $lists = $this->dataTable->get_datatables();
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $no = $start + 1;
        $data = [];

        foreach ($lists as $list) {
            $row = [];

            $row[] = $no++;
            $row[] = '<a href="#" class="text-primary fw-bolder link-underline-opacity-0 link-underline-opacity-100-hover" onclick="getData(`' . enkripsi($list->id) . '`)">' . $list->NIK . '</a>';
            $row[] = $list->nama;
            $row[] = '
                <button type="button" class="text-danger btn text-danger shadow-none btn-sm rounded-0" onclick="deleteData(`' . enkripsi($list->id) . '`)">
                    <i class="fas fa-times"></i>
                </button>
            ';

            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->dataTable->count_all(),
            "recordsFilter" => $this->dataTable->count_filtered(),
            "data" => $data
        ];

        return $this->response
            ->setStatusCode(ResponseInterface::HTTP_OK, 'Generate success')
            ->setJSON($output);
    }

    function saveData()
    {
        $action = 'save';
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), 'Request method not allowed');

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, 'Request not allowed');
        }

        try {
            $id = generate_uuid();
            $NIK = trim($this->request->getPost('data_nik'));
            $name = ucwords(trim($this->request->getPost('data_name')));

            $rules = [
                'data_name' => [
                    'rules' => 'required|min_length[1]|max_length[150]',
                    'errors' => [
                        'required' => 'Employee name is required',
                        'min_length' => 'The minimum character of employee name is {param}',
                        'max_length' => 'The maximum character of employee name is {param}'
                    ]
                ]
            ];

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());

                log_action($this->module, $action, 'error', current_url(), 'Validation failed', '', json_encode([
                    'data' => $this->validasi->getErrors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed" . $error_message);
            }

            $data = [
                'id' => $id,
                'NIK' => $NIK,
                'nama' => $name,
                'created_by' => $this->NIK
            ];

            $insert = $this->karyawanModel->insert($data);
            if (!$insert) {
                log_action($this->module, $action, 'error', current_url(), 'Save failed, there was an error during processing your request', '', json_encode([
                    'data' => $this->karyawanModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Save failed, there was an error during processing your request');
            }

            log_action($this->module, $action, 'success', current_url(), 'Save success', '', json_encode([
                'data' => $data
            ]));

            return pesan(ResponseInterface::HTTP_CREATED, 'Save success');
        } catch (\Exception $e) {
            log_action($this->module, $action, 'error', current_url(), 'Unexpected error occured', '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error ocured" . $e->getMessage());
        }
    }

    function getData()
    {
        $action = 'get';
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), 'Request method not allowed');

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, 'Request not allowed');
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $action, 'error', current_url(), 'Input is not a valid JSON object');
                throw new \Exception('Input is not a valid JSON object');
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, 'Input is not a valid JSON object');
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $action, "error", current_url(), 'Employee data ID is missing in JSON input');
                throw new \Exception('Employee data ID is missing in JSON input');
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, 'Employee data ID is missing in JSON input');
            }

            $idKaryawan = dekripsi($json_data['token']);

            $getData = $this->karyawanModel->getDataById($idKaryawan);
            if (!$getData) {
                log_action($this->module, $action, 'error', current_url(), 'Employee data not found');

                return pesan(ResponseInterface::HTTP_NOT_FOUND, 'Employee data not found');
            }

            $data = [
                'token' => enkripsi($getData->id),
                'NIK' => $getData->NIK,
                'name' => $getData->nama,
            ];

            return pesan(ResponseInterface::HTTP_OK, 'Employee data found', $data);
        } catch (\Exception $e) {
            log_action($this->module, $action, 'error', current_url(), 'Unexpected error occured', '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured" . $e->getMessage());
        }
    }

    function updateData()
    {
        $action = "update";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), 'Request method not allowed');
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, 'Request method not allowed');
        }

        try {
            $token = trim($this->request->getPost('data_token'));
            $id = dekripsi($token);
            $NIK = trim($this->request->getPost('data_nik'));
            $name = ucwords(trim($this->request->getPost('data_name')));

            $rules = [
                'data_token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Employee token is required'
                    ]
                ],
                'data_nik' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Employee NIK is required'
                    ]
                ],
                'data_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Employee name is required',
                        'min_length' => "The minimum character of Employee name is {param}",
                        'max_length' => "The maximum character of Employee name is {param}",
                    ]
                ]
            ];

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());

                log_action($this->module, $action, 'error', current_url(), 'Validation failed', '', json_encode([
                    'data' => $this->validasi->getErrors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed" . $error_message);
            }

            $data = [
                'nama' => $name,
                'updated_by' => $this->NIK
            ];

            $update = $this->karyawanModel->update($id, $data);
            if (!$update) {
                log_action($this->module, $action, 'error', current_url(), 'Update failed, there was an error during update process', '', json_encode([
                    'data' => $this->karyawanModel->errors()
                ]));

                log_message('debug', 'Update ID: ' . $id);

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Update failed, there was an error during update process');
            }

            log_action($this->module, $action, 'success', current_url(), 'Update success', '', json_encode([
                'data' => $data,
                'where' => $id
            ]));

            return pesan(ResponseInterface::HTTP_OK, 'Update success');
        } catch (\Exception $e) {
            log_action($this->module, $action, 'error', current_url(), 'Unexpected error occured', '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured" . $e->getMessage());
        }
    }

    function deleteData()
    {
        $action = 'delete';
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), 'Request method is not allowed');
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $action, 'error', current_url(), 'Input is not valid JSON object');
                throw new \Exception("Input is not valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, 'Input is not valid JSON input');
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $action, 'error', current_url(), 'Employee data ID is missing in JSON input');
                throw new \Exception("Employee data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Employee data ID is missing in JSON input");
            }

            $idKaryawan = dekripsi($json_data['token']);

            $checkData = $this->karyawanModel->getDataById($idKaryawan);
            if (!$checkData) {
                log_action($this->module, $action, 'error', current_url(), 'Employee data not found');
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Employee data not found");
            }

            $delete = $this->karyawanModel->delete($idKaryawan);
            if (!$delete) {
                log_action($this->module, $action, 'error', current_url(), "Delete failed", '', json_encode([
                    'data' => $this->karyawanModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Delete failed');
            }

            log_action($this->module, $action, "success", current_url(), 'Delete success', '', json_encode([
                'data' => $idKaryawan
            ]));
            return pesan(ResponseInterface::HTTP_OK, "Delete success");
        } catch (\Exception $e) {
            log_action($this->module, $action, 'error', current_url(), 'Unexpected error occured', '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured" . $e->getMessage());
        }
    }
}
