<?php

namespace App\Controllers\MasterData\MaterialManagement\EquipmentType;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\MaterialManagement\EquipmentType\EquipmentTypeModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Services;

class EquipmentType extends BaseController
{
    protected $typeModel;
    protected $masterModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;
    protected $module;

    public function __construct()
    {
        $this->module = 'Equipment Type';
        $this->typeModel = new EquipmentTypeModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'm_tipe_equipment';
        $column_order = ['code', 'name', 'remark'];
        $column_search = ['code', 'name', 'remark'];
        $order = ['code' => 'asc'];

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    public function index()
    {
        $data = [
            'title' => 'Equipment Type',
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/MaterialManagement/EquipmentType/equipmentType.js' . '"></script>'
            ]
        ];

        return view('MasterData/MaterialManagement/EquipmentType/index', $data);
    }

    function loadTable()
    {
        log_action($this->module, "Table", "info", current_url(), "Generating material category list");

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
            ->setStatusCode(ResponseInterface::HTTP_OK, "Generate successfully")
            ->setJSON($output);
    }

    function saveData()
    {
        $action = "save";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), 'Request method not allowed');

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $id = generate_uuid();
            $code = $this->masterModel->generateCode('m_tipe_equipment', 'code', 'ECT-', 4);
            $name = ucwords(trim($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_name' => [
                    'rules' => 'required|min_length[1]|max_length[150]',
                    'errors' => [
                        'required' => "Equipment type name is required",
                        'min_length' => "The minimum character of equipment type name is {param}",
                        'max_length' => "The maximum character of equipment type name is {param}"
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
                'code' => $code,
                'name' => $name,
                'remark' => $remark,
                'created_by' => $this->NIK
            ];

            $insert = $this->typeModel->insert($data);
            if (!$insert) {
                log_action($this->module, $action, 'error', current_url(), "Save failed, there was an error during processing your request", '', json_encode([
                    "data" => $this->typeModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed, there was an error during processing your request");
            }

            log_action($this->module, $action, 'error', current_url(), "Save success", '', json_encode([
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

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured" . $e->getMessage());
        }
    }

    function getData()
    {
        $aksi = "Get";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, 'error', current_url(), "Request method not allowed");

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
                log_action($this->module, $aksi, "error", current_url(), "Equipment type ID is missing in JSON input");
                throw new \Exception("Equipment type ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Equipment type ID is missing in JSON input");
            }

            $idType = dekripsi($json_data['token']);

            $get_data = $this->typeModel->getDataById($idType);
            if (!$get_data) {
                log_action($this->module, $aksi, "error", current_url(), "Equipment type data not found");

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Equipment type data not found");
            }

            $data = [
                'token' => enkripsi($get_data->id),
                'code' => $get_data->code,
                'name' => $get_data->name,
                'remark' => $get_data->remark
            ];

            return pesan(ResponseInterface::HTTP_OK, "Equipment type data found", $data);
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

    function updateData()
    {
        $aksi = "update";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $token = trim($this->request->getPost('data_token'));
            $type_id = dekripsi($token);
            $code = trim($this->request->getPost('data_code'));
            $name = ucwords(trim($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Equipment type token is required"
                    ]
                ],
                'data_code' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Equipment type code is required"
                    ]
                ],
                'data_name' => [
                    'rules' => 'required|min_length[1]|max_length[150]',
                    'errors' => [
                        'required' => "Equipment type name is required",
                        'min_length' => "The minimum character of equipment type name is {param}",
                        'max_length' => "The maximum character of equipment type name is {param}",
                    ]
                ]
            ];

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());

                log_action($this->module, $aksi, "error", current_url(), "Validation failed", '', json_encode([
                    'data' => $this->validasi->getErrors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed " . $error_message);
            }

            $data = [
                'name' => $name,
                'remark' => $remark,
                'updated_by' => $this->NIK
            ];

            $update = $this->typeModel->update($type_id, $data);
            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Update failed, there was an error during processing your request", '', json_encode([
                    'data' => $this->typeModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed, there was an error during processing your request");
            }

            log_action($this->module, $aksi, "success", current_url(), "Update success", '', json_encode([
                'data' => $data,
                'where' => $type_id
            ]));

            return pesan(ResponseInterface::HTTP_OK, "Update success");
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
                log_action($this->module, $aksi, "error", current_url(), "Equipment type ID is missing in JSON input");
                throw new \Exception("Equipment type ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Equipment type is missing in JSON input");
            }

            $id_type = dekripsi($json_data['token']);

            $check_data = $this->typeModel->getDataById($id_type);
            if (!$check_data) {
                log_action($this->module, $aksi, "error", current_url(), "Equipment type data not found");
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Equipment type data not found");
            }

            $delete = $this->typeModel->delete($id_type);
            if (!$delete) {
                log_action($this->module, $aksi, "error", current_url(), "Delete failed", '', json_encode([
                    'data' => $this->typeModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Delete failed, there was an error during processing your request");
            }

            log_action($this->module, $aksi, "success", current_url(), "Delete success", '', json_encode([
                'data' => $id_type
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
