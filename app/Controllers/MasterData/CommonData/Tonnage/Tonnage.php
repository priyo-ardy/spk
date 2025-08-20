<?php

namespace App\Controllers\MasterData\CommonData\Tonnage;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\Tonnage\TonnageModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Services;

class Tonnage extends BaseController
{
    protected $tonnageModel;
    protected $dataTable;
    protected $masterModel;
    protected $validasi;
    protected $enkripsi;
    protected $module;

    public function __construct()
    {
        $this->module = "Tonnage";
        $this->tonnageModel = new TonnageModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'm_tonnage';
        $column_order = ['code', 'name', 'remark'];
        $column_search = ['code', 'name', 'remark'];
        $order = array('code' => 'asc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    function loadTable()
    {
        log_action($this->module, "table", "info", current_url(), "Generating tonange list");

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

    public function index()
    {
        $data = [
            'title' => "Tonnage Setup",
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Tonnage/tonnage.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/Tonnage/index', $data);
    }

    function saveData()
    {
        $aksi = "save";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, 'error', current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $id = generate_uuid();
            $code = $this->masterModel->generateCode('m_tonnage', 'code', 'TNG-', 4);
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

                log_action($this->module, $aksi, "error", current_url(), "Validation failed", '', json_encode([
                    'data' => $this->validasi->getErrors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed " . $error_message);
            }

            $data = [
                'id' => $id,
                'code' => $code,
                'name' => $name,
                'remark' => $remark,
                'created_by' => $this->NIK
            ];

            $insert = $this->tonnageModel->insert($data);
            if (!$insert) {
                log_action($this->module, $aksi, 'error', current_url(), "Save failed, there was an error during processing your request", '', json_encode([
                    'data' => $this->tonnageModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed, there was an error during processing your request");
            }

            log_action($this->module, $aksi, 'success', current_url(), "Save success", '', json_encode([
                'data' => $data
            ]));

            return pesan(ResponseInterface::HTTP_CREATED, "Save success");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occurred", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function getData() {
        $aksi = "Get";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, 'error', current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try{
            $json_data = $this->request->getJSON(true);

            if(!is_array($json_data)){
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if(!isset($json_data['token'])){
                log_action($this->module, $aksi, "error", current_url(), "Job data ID is missing in JSON input");
                throw new \Exception("Job data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Job data ID is missing in JSON input");
            }

            $id_category = dekripsi($json_data['token']);

            $get_data = $this->tonnageModel->getDataById($id_category);
            if(!$get_data){
                log_action($this->module, $aksi, "error", current_url(), "Tonnage data not found");

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Tonnage data not found");
            }

            $data = [
                'token' => enkripsi($get_data->id),
                'code' => $get_data->code,
                'name' => $get_data->name,
                'remark' => $get_data->remark
            ];

            return pesan(ResponseInterface::HTTP_OK, "Tonnage data found", $data);
        } catch(\Exception $e){
            log_action($this->module, $aksi, 'error', current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function updateData() {
        $aksi = "update";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try{
            $token = trim($this->request->getPost('data_token'));
            $id = dekripsi($token);
            $code = trim($this->request->getPost('data_code'));
            $name = ucwords(trim($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Tonange token is required"
                    ]
                ],
                'data_code' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Tonange code is required"
                    ]
                ],
                'data_name' => [
                    'rules' => 'required|min_length[1]|max_length[150]',
                    'errors' => [
                        'required' => "Tonange name is required",
                        'min_length' => "The minimum character of tonange name is {param}",
                        'max_length' => "The maximum character of tonange name is {param}",
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

            $update = $this->tonnageModel->update($id, $data);
            if(!$update){
                log_action($this->module, $aksi, "error", current_url(), "Update failed, there was an error during processing your request", '', json_encode([
                    'data' => $this->tonnageModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed, there was an error during processing your request");
            }

            log_action($this->module, $aksi, "success", current_url(), "Update success", '', json_encode([
                'data' => $data,
                'where' => $id
            ]));

            return pesan(ResponseInterface::HTTP_OK, "Update success");
        } catch(\Exception $e){
            log_action($this->module, $aksi, 'error', current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function deleteData() {
        $aksi = "delete";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try{
            $json_data = $this->request->getJSON(true);

            if(!is_array($json_data)){
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if(!isset($json_data['token'])){
                log_action($this->module, $aksi, "error", current_url(), "Job data ID is missing in JSON input");
                throw new \Exception("Job data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Job data ID is missing in JSON input");
            }

            $id_category = dekripsi($json_data['token']);

            $check_data = $this->tonnageModel->getDataById($id_category);
            if(!$check_data){
                log_action($this->module, $aksi, "error", current_url(), "Tonange data not found");
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Tonange data not found");
            }

            $delete = $this->tonnageModel->delete($id_category);
            if(!$delete){
                log_action($this->module, $aksi, "error", current_url(), "Delete failed", '', json_encode([
                    'data' => $this->tonnageModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Delete failed, there was an error during processing your request");
            }

            log_action($this->module, $aksi, "success", current_url(), "Delete success", '', json_encode([
                'data' => $id_category
            ]));
            return pesan(ResponseInterface::HTTP_OK, "Delete success");
        } catch(\Exception $e){
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
