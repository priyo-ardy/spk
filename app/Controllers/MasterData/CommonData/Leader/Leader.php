<?php

namespace App\Controllers\MasterData\CommonData\Leader;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\Leader\LeaderModel;
use App\Models\MasterData\CommonData\Karyawan\KaryawanModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Services;
use Config\Database;

class Leader extends BaseController
{
    protected $module;
    protected $karyawanModel;
    protected $leaderModel;
    protected $masterModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;
    protected $db;

    public function __construct()
    {
        $this->module = "Leader";
        $this->karyawanModel = new KaryawanModel();
        $this->leaderModel = new LeaderModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
        $this->db = Database::connect();

        $table = 'm_leader';
        $column_order = ['NIK', 'nama'];
        $column_search = ['NIK', 'nama'];
        $order = array('NIK' => 'asc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    public function index()
    {
        $data = [
            'title' => 'Group Leader',
            'karyawan' => $this->karyawanModel->generateList(),
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Leader/leader.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/Leader/index', $data);
    }

    function loadTable()
    {
        log_action($this->module, 'table', 'info', current_url(), 'Generating group leader list');

        $lists = $this->dataTable->get_datatables();
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $no = $start + 1;
        $data = [];

        foreach ($lists as $list) {
            $row = [];

            $row[] = $no++;
            $row[] = '<a href="#" class="text-primary fw-bolder link-underline-opacity-0 link-underline-opacity-100-hover" onclick="getData(`' . enkripsi($list->id) . '`)">' . $list->NIK . '</a>';
            $row[] = $list->nama;
            $row[] = $list->remark;
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
            ->setStatusCode(ResponseInterface::HTTP_OK, "Generate successfully")
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
            $NIK = $this->request->getPost('data_NIK');
            $name = ucwords(trim($this->request->getPost('data_nama')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_NIK' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Defect name is required'
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
                'remark' => $remark,
                'created_by' => $this->NIK
            ];

            $insert = $this->leaderModel->insert($data);
            if (!$insert) {
                log_action($this->module, $action, 'error', current_url(), 'Save failed, there was an error during processing your request', '', json_encode([
                    'data' => $this->leaderModel->errors()
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

    function getNamaByNIK()
    {
        $action = 'Nama Karyawan';
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

            if (!isset($json_data['NIK'])) {
                log_action($this->module, $action, "error", current_url(), 'Employee name is missing in JSON input');
                throw new \Exception('Employee name is missing in JSON input');
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, 'Employee name is missing in JSON input');
            }

            $NIK = $json_data['NIK'];

            $get = $this->karyawanModel->getEmployeeName($NIK);
            if (!$get) {
                log_action($this->module, $action, 'error', current_url(), 'Employee data not found');
                return pesan(ResponseInterface::HTTP_NOT_FOUND, 'Employee data not found');
            }

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_OK, "Get employee name")
                ->setJSON([
                    'data' => [
                        'nama' => $get->nama
                    ]
                ]);
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
                log_action($this->module, $action, "error", current_url(), 'Leader data ID is missing in JSON input');
                throw new \Exception('Leader data ID is missing in JSON input');
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, 'Leader data ID is missing in JSON input');
            }

            $idLeader = dekripsi($json_data['token']);

            $getData = $this->leaderModel->getDataById($idLeader);
            if (!$getData) {
                log_action($this->module, $action, 'error', current_url(), 'Leader data not found');

                return pesan(ResponseInterface::HTTP_NOT_FOUND, 'Leader data not found');
            }

            $data = [
                'token' => enkripsi($getData->id),
                'NIK' => $getData->NIK,
                'nama' => $getData->nama,
                'remark' => $getData->remark,
                'created_by' => $this->NIK
            ];

            return pesan(ResponseInterface::HTTP_OK, 'Leader data found', $data);
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
            $NIK = trim($this->request->getPost('data_NIK'));
            $nama = ucwords(trim($this->request->getPost('data_nama')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Leader token is required'
                    ]
                ],
                'data_NIK' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'NIK is required'
                    ]
                ],
                'data_nama' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Employee name is required'
                    ]
                ],
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
                'NIK' => $NIK,
                'nama' => $nama,
                'remark' => $remark,
                'updated_by' => $this->NIK
            ];

            $update = $this->leaderModel->update($id, $data);
            if (!$update) {
                log_action($this->module, $action, 'error', current_url(), 'Update failed, there was an error during update process', '', json_encode([
                    'data' => $this->leaderModel->errors()
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
                log_action($this->module, $action, 'error', current_url(), 'Leader ID is missing in JSON input');
                throw new \Exception("Leader ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Leader ID is missing in JSON input");
            }

            $idLeader = dekripsi($json_data['token']);

            $checkData = $this->leaderModel->getDataById($idLeader);
            if (!$checkData) {
                log_action($this->module, $action, 'error', current_url(), 'Leader data not found');
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Leader data not found");
            }

            $delete = $this->leaderModel->delete($idLeader);
            if (!$delete) {
                log_action($this->module, $action, 'error', current_url(), "Delete failed", '', json_encode([
                    'data' => $this->leaderModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Delete failed');
            }

            log_action($this->module, $action, "success", current_url(), 'Delete success', '', json_encode([
                'data' => $idLeader
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
