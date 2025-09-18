<?php

namespace App\Controllers\MasterData\CommonData\RepairReason;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\RepairReason\RepairReasonModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Services;
use Psr\Http\Message\ResponseFactoryInterface;

class RepairReason extends BaseController
{
    protected $repairReasonModel;
    protected $masterModel;
    protected $dataTable;
    protected $module;
    protected $validasi;
    protected $enkripsi;

    public function __construct()
    {
        $this->module = "Repair Reason";
        $this->repairReasonModel = new RepairReasonModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'm_repair';
        $column_order = ['code', 'name'];
        $column_search = ['code', 'name'];
        $order = array('code' => 'asc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    public function index()
    {
        $data = [
            'title' => 'Repair Management',
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/RepairReason/repairReason.js' . '"></script>'
            ]
        ];

        return view('Masterdata/CommonData/RepairReason/index', $data);
    }

    function loadTable()
    {
        log_action($this->module, "table", "info", current_url(), "Generating repair reason list");

        $lists = $this->dataTable->get_datatables();
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $no = $start + 1;
        $data = [];

        foreach ($lists as $list) {
            $row = [];

            $row[] = $no++;
            $row[] = '
                <a href="#" class="text-primary fw-bolder link-underline-opacity-0 link-underline-opacity-100-hover" onclick="getData(`' . enkripsi($list->id) . '`)">' . $list->code . '</a>
            ';

            $row[] = $list->name;
            $row[] = $list->remark;
            $row[] = '
                <button type="button" class="text-danger btn text-danger shadow-none btn-sm rounded-0" onclick="deleteData(`' . enkripsi($list->id) . '`)">
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
            ->setStatusCode(ResponseInterface::HTTP_OK, "Generated successfully")
            ->setJSON($output);
    }

    function saveData()
    {
        $action = "save";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $id = generate_uuid();
            $code = $this->masterModel->generateCode('m_repair', 'code', 'RPR-', 4);
            $name = ucwords(trim($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_name' => [
                    'rules' => 'required|min_length[1]|max_length[150]',
                    'errors' => [
                        'required' => "Tonnage name is required",
                        'min_length' => "The minimum character of tonnage name is {param}",
                        'max_length' => "The maximum character of tonnage name is {param}",
                    ]
                ]
            ];

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());

                log_action($this->module, $action, "error", current_url(), "Validation failed", '', json_encode([
                    'data' => $this->validasi->getErrors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed" . $error_message);
            }

            $data = [
                'id' => $id,
                'code' => $code,
                'name' => $name,
                'remark' => $remark,
                'created_by' => $this->NIK
            ];

            $insert = $this->repairReasonModel->insert($data);
            if (!$insert) {
                log_action($this->module, $action, 'error', current_url(), "Save failed. There was an error during your process", '', json_encode([
                    'data' => $this->repairReasonModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed. There was an error during the process");
            }

            log_action($this->module, $action, 'success', current_url(), "Save success", '', json_encode([
                'data' => $data
            ]));

            return pesan(ResponseInterface::HTTP_CREATED, "Save success");
        } catch (\Exception $e) {
            log_action($this->module, $action, 'error', current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));
        }
    }

    function getData()
    {
        $action = "get";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $action, 'error', current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $action, 'error', current_url(), "Repair data id is missing in JSON input");
                throw new \Exception("Repair data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Repair data ID is missing in JSON input");
            }

            $idRepair = dekripsi($json_data['token']);

            $getData = $this->repairReasonModel->getDataById($idRepair);
            if (!$getData) {
                log_action($this->module, $action, "error", current_url(), "Repair data not found");
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Repair data not found");
            }

            $data = [
                'token' => enkripsi($getData->id),
                'code' => $getData->code,
                'name' => $getData->name,
                'remark' => $getData->remark
            ];

            return pesan(ResponseInterface::HTTP_OK, "Repair data found", $data);
        } catch (\Exception $e) {
            log_action($this->module, $action, 'error', current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));
        }
    }

    function updateData()
    {
        $action = "update";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request method not allowed");
        }

        try {
            $token = trim($this->request->getPost('data_token'));
            $id = dekripsi($token);
            $code = trim($this->request->getPost('data_code'));
            $name = ucwords(trim($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Repair token is required"
                    ]
                ],
                'data_code' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Repair code is required"
                    ]
                ],
                'data_name' => [
                    'rules' => 'required|min_length[1]|max_length[150]',
                    'errors' => [
                        'required' => "Repair name is required",
                        'min_length' => "The minimum character of tonange name is {param}",
                        'max_length' => "The maximum character of tonange name is {param}",
                    ]
                ]
            ];

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());
                log_action($this->module, $action, 'error', current_url(), "Validation failed", '', json_encode([
                    'data' => $this->validasi->getErrors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed" . $error_message);
            }

            $data = [
                'name' => $name,
                'remark' => $remark,
                'updated_by' => $this->NIK
            ];

            $update = $this->repairReasonModel->update($id, $data);
            if (!$update) {
                log_action($this->module, $action, 'error', current_url(), "Update failed, there was an error during your update process", '', json_encode([
                    'data' => $this->repairReasonModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed, there was an error during your update process");
            }

            log_action($this->module, $action, 'success', current_url(), "Update success", '', json_encode([
                'data' => $data,
                'where' => $id
            ]));

            return pesan(ResponseInterface::HTTP_OK, "Update success");
        } catch (\Exception $e) {
            log_action($this->module, $action, 'error', current_url(), "Unexpected error occured", '', json_encode([
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
        $aksi = "delete";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "Repair data ID is missing in JSON input");
                throw new \Exception("Repair data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Repair data ID is missing in JSON input");
            }

            $idRepair = dekripsi($json_data['token']);

            $check_data = $this->repairReasonModel->getDataById($idRepair);
            if (!$check_data) {
                log_action($this->module, $aksi, "error", current_url(), "Repair data not found");
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Repair data not found");
            }

            $delete = $this->repairReasonModel->delete($idRepair);
            if (!$delete) {
                log_action($this->module, $aksi, "error", current_url(), "Delete failed", '', json_encode([
                    'data' => $this->repairReasonModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Delete failed, there was an error during processing your request");
            }

            log_action($this->module, $aksi, "success", current_url(), "Delete success", '', json_encode([
                'data' => $idRepair
            ]));
            return pesan(ResponseInterface::HTTP_OK, "Delete success");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, 'error', current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }
}
