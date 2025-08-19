<?php

namespace App\Controllers\MasterData\MaterialManagement\MaterialCategory;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use App\Models\MasterData\MaterialManagement\MaterialCategory\MaterialCategoryModel;
use Config\Services;

class MaterialCategory extends BaseController
{
    protected $categoryModel;
    protected $dataTable;
    protected $masterModel;
    protected $validasi;
    protected $enkripsi;
    protected $module;

    public function __construct()
    {
        $this->module = "Material Category";
        $this->categoryModel = new MaterialCategoryModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'm_material_category';
        $column_order = ['code', 'name', 'remark'];
        $column_search = ['code', 'name', 'remark'];
        $order = array('code' => 'asc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
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

    public function index()
    {
        $data = [
            'title' => "Material Category Setup",
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/MaterialManagement/MaterialCategory/material_category.js' . '"></script>'
            ]
        ];

        return view('MasterData/MaterialManagement/MaterialCategory/index', $data);
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
            $code = $this->masterModel->generateCode('m_material_category', 'code', 'MTC-', 4);
            $name = ucwords(trim($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $rules = [
                'data_name' => [
                    'rules' => 'required|min_length[1]|max_length[150]|alpha_numeric',
                    'errors' => [
                        'required' => "Material category name is required",
                        'min_length' => "The minimum character of material category name is {param}",
                        'max_length' => "The maximum character of material category name is {param}",
                        'alpha_numeric' => "Only numbers and letters can be accepted for material category name"
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

            $insert = $this->categoryModel->insert($data);
            if (!$insert) {
                log_action($this->module, $aksi, 'error', current_url(), "Save failed, there was an error during processing your request", '', json_encode([
                    'data' => $this->categoryModel->errors()
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

    function getData() {}

    function updateData() {}

    function deleteData() {}
}
