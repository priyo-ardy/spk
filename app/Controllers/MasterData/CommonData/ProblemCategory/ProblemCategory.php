<?php

namespace App\Controllers\MasterData\CommonData\ProblemCategory;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\ProblemCategory\ProblemCategoryModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;
use Config\Database;

class ProblemCategory extends BaseController
{
    protected $problemModel;
    protected $masterModel;
    protected $dataTable;
    protected $validasi;
    protected $db;
    protected $module;

    public function __construct()
    {
        $this->problemModel = new ProblemCategoryModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->db = Database::connect();
        $this->module = "Problem Category";

        $table = 'm_problem_category';
        $column_order = ['code', 'name', 'remark'];
        $column_search = ['code', 'name', 'remark'];
        $order = array('code' => 'asc');
        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }
    public function index()
    {
        $data = [
            'title' => $this->module,
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/ProblemCategory/problem_category.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/ProblemCategory/index', $data);
    }

    public function loadTable()
    {
        log_action($this->module, "Table", "info", current_url(), "Generating pproblem category list");

        $lists = $this->dataTable->get_datatables();
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $no = $start + 1;
        $data = [];

        foreach ($lists as $list) {
            $row = [];
            $nama_kategori = '';

            switch ($list->category) {
                case 1:
                    $nama_kategori = 'SPK Mold Repair';
                    break;
                case 2:
                    $nama_kategori = 'SPK Mesin';
                    break;
                case 3:
                    $nama_kategori = 'SPK Preventive Maintenance Request';
                    break;
                case 4:
                    $nama_kategori = 'SPK Equipment Request';
                    break;
                default:
                    $nama_kategori = 'Unregistered';
                    break;
            }

            $row[] = $no++;
            $row[] = '
                <a href="#" class="text-primary fw-bolder link-underline-opacity-0 link-underline-opacity-100-hover" onclick="getData(`' . enkripsi($list->id) . '`)">' . $list->code . '</a>
            ';
            $row[] = $nama_kategori;
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

    public function saveData()
    {
        if ($this->request->getMethod() !== 'POST') {
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $rules = [
                'data_kategori' => [
                    'label' => "SPK Category",
                    'rules' => 'required',
                    'errors' => [
                        'required' => "{field} is required"
                    ]
                ],
                'data_name' => [
                    'label' => 'Problem Category Name',
                    'rules' => 'required|min_length[1]|max_length[150]|trim',
                    'errors' => [
                        'required' => "{field} is required",
                        'min_length' => "The minimum character of {field} is {param}",
                        'max_length' => "The maximum character of {field} is {param}"
                    ]
                ]
            ];

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, $error_message);
            }

            $id = generate_uuid();
            $code = $this->masterModel->generateCode('m_problem_category', 'code', 'PCG-', 6);
            $kategori = $this->request->getPost('data_kategori');
            $name = trim(ucwords($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $data = [
                'id' => $id,
                'category' => $kategori,
                'code' => $code,
                'name' => $name,
                'remark' => $remark,
                'created_by' => session()->get('user_name'),
            ];

            $this->db->transStart();
            $this->problemModel->save($data);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to save data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Data saved successfully");
        } catch (\Exception $e) {
            log_action($this->module, 'save', "error", current_url(), "Unexpected error occurred", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function getData($token)
    {
        $id = dekripsi($token);

        $getData = $this->problemModel->getDataById($id);

        if (!$getData) {
            throw PageNotFoundException::forPageNotFound();
        }

        $data = [
            'token' => enkripsi($getData->id),
            'category' => $getData->category,
            'name' => $getData->name,
            'remark' => $getData->remark
        ];

        return pesan(ResponseInterface::HTTP_OK, "Data found", $data);
    }

    function updateData()
    {
        if ($this->request->getMethod() !== 'POST') {
            if ($this->request->isAJAX()) {
                return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
            }

            return redirect()->back()->with('error', 'Request method not allowed');
        }

        try {
            $rules = [
                'data_token' => [
                    'label' => "Problem category token",
                    'rules' => 'required',
                    'errors' => [
                        'required' => "{field} is required"
                    ]
                ],
                'data_kategori' => [
                    'label' => "SPK Category",
                    'rules' => 'required',
                    'errors' => [
                        'required' => "{field} is required"
                    ]
                ],
                'data_name' => [
                    'label' => 'Problem Category Name',
                    'rules' => 'required|min_length[1]|max_length[150]|trim',
                    'errors' => [
                        'required' => "{field} is required",
                        'min_length' => "The minimum character of {field} is {param}",
                        'max_length' => "The maximum character of {field} is {param}"
                    ]
                ]
            ];

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, $error_message);
            }

            $token = trim($this->request->getPost('data_token'));
            $id = dekripsi($token);
            $kategori = trim($this->request->getPost('data_kategori'));
            $name = trim(ucwords($this->request->getPost('data_name')));
            $remark = trim($this->request->getPost('data_remark'));

            $cekData = $this->problemModel->getDataById($id);
            if (!$cekData) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Data not found");
            }

            $data = [
                'category' => $kategori,
                'name' => $name,
                'remark' => $remark,
                'updated_by' => session()->get('user_name'),
            ];

            $this->db->transStart();
            $this->problemModel->update($id, $data);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to update data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Data updated successfully");
        } catch (\Exception $e) {
            log_action($this->module, 'update', "error", current_url(), "Unexpected error occurred", '', json_encode([
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
        if ($this->request->getMethod() !== 'POST') {
            if ($this->request->isAJAX()) {
                return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
            }

            return redirect()->back()->with('error', 'Request method not allowed');
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Problem category token is missing in JSON input");
            }

            $token = trim($json_data['token']);
            $id = dekripsi($token);

            $cekData = $this->problemModel->getDataById($id);
            if (!$cekData) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Data not found");
            }

            $this->db->transStart();
            $this->problemModel->delete($id);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to delete data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Data deleted successfully");
        } catch (\Exception $e) {
            log_action($this->module, 'delete', "error", current_url(), "Unexpected error occurred", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function exportData() {}

    function seederData()
    {
        $get = $this->problemModel->orderBy('code', 'asc')->findAll();

        return pesan(ResponseInterface::HTTP_OK, "Data found", $get);
    }
}
