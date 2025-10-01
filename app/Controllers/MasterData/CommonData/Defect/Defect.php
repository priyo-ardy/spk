<?php

namespace App\Controllers\MasterData\CommonData\Defect;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\Defect\DefectModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use CodeIgniter\HTTP\ResponsableInterface;
use Config\Services;

class Defect extends BaseController
{
    protected $defectModel;
    protected $dataTable;
    protected $masterModel;
    protected $module;
    protected $validasi;
    protected $enkripsi;

    public function __construct()
    {
        $this->module = "Defect";
        $this->defectModel = new DefectModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'm_defect';
        $column_order = ['code', 'name', 'description'];
        $column_search = ['code', 'name', 'description'];
        $order = array('code' => 'asc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_search, $column_order, $order);
    }

    public function index()
    {
        $data = [
            'title' => 'Defect Management',
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Defect/defect.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/Defect/index', $data);
    }

    function loadTable()
    {
        log_action($this->module, 'table', 'info', current_url(), 'Generating defect list');

        $lists = $this->dataTable->get_datatables();
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $no = $start + 1;
        $data = [];

        foreach ($lists as $list) {
            $row = [];

            $row[] = $no++;
            $row[] = '<a href="#" class="text-primary fw-bolder link-underline-opacity-0 link-underline-opacity-100-hover" onclick="getData(`' . enkripsi($list->id) . '`)">' . $list->code . '</a>';
            $row[] = $list->name;
            $row[] = $list->description;
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
            $code = $this->masterModel->generateCode('m_defect', 'code', 'DFC-', 6);
            $name = ucwords(trim($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_name' => [
                    'rules' => 'required|min_length[1]|max_length[150]',
                    'errors' => [
                        'required' => 'Defect name is required',
                        'min_length' => 'The minimum character of defect name is {param}',
                        'max_length' => 'The maximum character of defect name is {param}'
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
                'description' => $remark,
                'created_by' => $this->NIK
            ];

            $insert = $this->defectModel->insert($data);
            if (!$insert) {
                log_action($this->module, $action, 'error', current_url(), 'Save failed, there was an error during processing your request', '', json_encode([
                    'data' => $this->defectModel->errors()
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
                log_action($this->module, $action, "error", current_url(), 'Defect data ID is missing in JSON input');
                throw new \Exception('Defect data ID is missing in JSON input');
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, 'Defect data ID is missing in JSON input');
            }

            $idDefect = dekripsi($json_data['token']);

            $getData = $this->defectModel->getDataById($idDefect);
            if (!$getData) {
                log_action($this->module, $action, 'error', current_url(), 'Defect data not found');

                return pesan(ResponseInterface::HTTP_NOT_FOUND, 'Defect data not found');
            }

            $data = [
                'token' => enkripsi($getData->id),
                'code' => $getData->code,
                'name' => $getData->name,
                'remark' => $getData->description
            ];

            return pesan(ResponseInterface::HTTP_OK, 'Defect data found', $data);
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
            $code = trim($this->request->getPost('data_code'));
            $name = ucwords(trim($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Defect token is required'
                    ]
                ],
                'data_code' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Defect code is required'
                    ]
                ],
                'data_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Defect name is required',
                        'min_length' => "The minimum character of tonange name is {param}",
                        'max_length' => "The maximum character of tonange name is {param}",
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
                'name' => $name,
                'description' => $remark,
                'updated_by' => $this->NIK
            ];

            $update = $this->defectModel->update($id, $data);
            if (!$update) {
                log_action($this->module, $action, 'error', current_url(), 'Update failed, there was an error during update process', '', json_encode([
                    'data' => $this->defectModel->errors()
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
                log_action($this->module, $action, 'error', current_url(), 'Defect data ID is missing in JSON input');
                throw new \Exception("Defect data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Defect data ID is missing in JSON input");
            }

            $idDefect = dekripsi($json_data['token']);

            $checkData = $this->defectModel->getDataById($idDefect);
            if (!$checkData) {
                log_action($this->module, $action, 'error', current_url(), 'Defect data not found');
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Defect data not found");
            }

            $delete = $this->defectModel->delete($idDefect);
            if (!$delete) {
                log_action($this->module, $action, 'error', current_url(), "Delete failed", '', json_encode([
                    'data' => $this->defectModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Delete failed');
            }

            log_action($this->module, $action, "success", current_url(), 'Delete success', '', json_encode([
                'data' => $idDefect
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

    function generateDefectList()
    {
        if ($this->request->getMethod() !== 'POST') {
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
            log_message('error', 'Generate Defect List Error: Request method not allowed');
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_message('error', 'Generate Defect List Error: Input is not valid JSON object');
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, 'Input is not valid JSON input');
            }

            if (!isset($json_data['kategori'])) {
                log_message('error', 'Generate Defect List Error: Kategori is missing in JSON input');
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Kategori is missing in JSON input");
            }

            $kategori = $json_data['kategori'];

            $get_defect = $this->defectModel->where('kategori', $kategori)->findAll();

            if ($get_defect) {
                return pesan(ResponseInterface::HTTP_OK, "Data found", $get_defect);
            } else {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Data not found");
            }
        } catch (\Exception $e) {
            log_message('error', 'Generate Defect List Error: ' . $e->getMessage());
            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured" . $e->getMessage());
        }
    }
}
