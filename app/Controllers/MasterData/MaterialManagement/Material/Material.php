<?php

namespace App\Controllers\MasterData\MaterialManagement\Material;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\MaterialManagement\Material\MaterialModel;
use App\Models\MasterData\CommonData\Workshop\WorkshopModel;
use App\Models\MasterData\MaterialManagement\MaterialCategory\MaterialCategoryModel;
use App\Models\MasterData\CommonData\Satuan\SatuanModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Database;
use Config\Services;
use App\Models\MasterData\CommonData\Machine\MachineModel;

class Material extends BaseController
{
    protected $db;
    protected $module;
    protected $masterModel;
    protected $workshopModel;
    protected $satuanModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;
    protected $materialModel;
    protected $materialKategoriModel;

    public function __construct()
    {
        $this->module = "Material Management";
        $this->materialModel = new MaterialModel();
        $this->materialKategoriModel = new MaterialCategoryModel();
        $this->workshopModel = new WorkshopModel();
        $this->satuanModel = new SatuanModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
        $this->db = Database::connect();

        $table = 'vw_material';
        $colum_order = ['code', 'kategori', 'name', 'workshop'];
        $column_search = ['code', 'kategori', 'name', 'workshop'];
        $order = array('code' => 'asc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $colum_order, $column_search, $order);
    }

    public function index()
    {
        $data = [
            'title' => 'Material Management',
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/MaterialManagement/Material/material.js' . '"></script>'
            ]
        ];

        return view('MasterData/MaterialManagement/Material/index', $data);
    }

    function loadTable()
    {
        log_action($this->module, "table", 'info', current_url(), "Generating material list");

        $lists = $this->dataTable->get_datatables();
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $no = $start + 1;
        $data = [];

        foreach ($lists as $list) {
            $row = [];

            $row[] = $no++;
            $row[] = '
                <a href="' . base_url() . 'material/details/' . enkripsi($list->id) . '" onclick="loading()" class="text-primary fw-bolder link-inderline-opacity-0 link-underline-opacity-100-hover">' . $list->code . '</a>
            ';
            $row[] = $list->workshop != NULL ? $list->workshop : '-';
            $row[] = $list->kategori != NULL ? $list->kategori : '-';
            $row[] = $list->name != NULL ? $list->name : '-';
            $row[] = $list->cust_part_name != NULL ? $list->cust_part_name : '-';
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

    function addData()
    {
        $data = [
            'title' => 'Add New Material',
            'kategori' => $this->materialKategoriModel->generateList(),
            'workshop' => $this->workshopModel->generateList(),
            'satuan' => $this->satuanModel->generateList(),
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/MaterialManagement/Material/add.js' . '"></script>'
            ]
        ];

        return view('MasterData/MaterialManagement/Material/add', $data);
    }

    function saveData()
    {
        $action = "save";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();

        try {
            $id = generate_uuid();
            $workshop = trim($this->request->getPost('data_workshop'));
            $kategori = trim($this->request->getPost('data_kategori'));
            $name = trim($this->request->getPost('data_name'));
            $custName = trim($this->request->getPost('data_cust_name'));
            $satuan = trim($this->request->getPost('data_uom'));
            $color = trim($this->request->getPost('data_color'));
            $keterangan = trim($this->request->getPost('data_keterangan'));
            $partCode = trim($this->request->getPost('data_code'));
            $getPrefix = $this->materialKategoriModel->checkPrefix($kategori);
            $prefix = $getPrefix ? $getPrefix->prefix : null;
            $code = $prefix === null ? $partCode : $this->materialModel->generateCodeMaterial('m_material', 'code', $prefix . "-", '4');

            $rules = [
                'data_workshop' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Material workshop is required'
                    ]
                ],
                'data_kategori' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Material category is required'
                    ]
                ],
                'data_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Material name is required'
                    ]
                ],
                'data_cust_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Material customer name is required'
                    ]
                ],
                'data_uom' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Material unit is required'
                    ]
                ]
            ];

            if ($prefix === null) {
                $rules = array_merge($rules, [
                    'data_code' => [
                        'rules' => 'required|min_length[3]|max_length[50]|is_unique[m_material.code]',
                        'errors' => [
                            'required' => 'Material code is required',
                            'min_length' => 'Minimum material code is 3 characters ',
                            'max_length' => 'Maximum material code is 50 characters',
                            'is_unique' => 'Material already exists'
                        ]
                    ]
                ]);
            }

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode("<br>", $this->validasi->getErrors());
                log_action($this->module, $action, "error", current_url(), "Save failed, failed to validate the data");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Save failed, failed to validate the data : $error_message");
            }

            $data = [
                'id' => $id,
                'workshop' => $workshop,
                'kategori' => $kategori,
                'name' => $name,
                'cust_part_name' => $custName,
                'uom' => $satuan,
                'color' => $color,
                'keterangan' => $keterangan,
                'created_by' => $this->NIK,
                'code' => $code
            ];

            $insert = $this->materialModel->insert($data);
            if (!$insert) {
                log_action($this->module, $action, "error", current_url(), "Save failed", '', json_encode([
                    'data' => $this->materialModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed, there was an error during processing your request, please try again later or contact your administrator");
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $action, "error", current_url(), "Save failed", '', json_encode([
                    'data' => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed due to database transaction error");
            }

            log_action($this->module, $action, "success", current_url(), "Data saved successfully", '', json_encode([
                'data' => $data
            ]));
            return pesan(ResponseInterface::HTTP_OK, "Save success");
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

    function details($token)
    {
        $action = "details";
        log_action($this->module, $action, 'open', current_url(), "Opening material details page");
        $idMaterial = dekripsi($token);

        $getData = $this->materialModel->getDataById($idMaterial);

        $data = [
            'title' => 'Material Details',
            'data' => $getData,
            'token' => enkripsi($getData->id),
            'workshop' => $this->workshopModel->generateList(),
            'kategori' => $this->materialKategoriModel->generateList(),
            'satuan' => $this->satuanModel->generateList(),
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/MaterialManagement/Material/edit.js' . '"></script>'
            ]
        ];

        return view('MasterData/MaterialManagement/Material/edit', $data);
    }

    function updateData()
    {
        $action = "update";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), "Rquest method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();

        try {
            $token = trim($this->request->getPost('data_token'));
            $idMaterial = dekripsi($token);
            $kategori = trim($this->request->getPost('data_kategori'));
            $workshop = trim($this->request->getPost('data_workshop'));
            $name = trim($this->request->getPost('data_name'));
            $custName = trim($this->request->getPost('data_cust_name'));
            $satuan = trim($this->request->getPost('data_uom'));
            $color = trim($this->request->getPost('data_color'));
            $keterangan = trim($this->request->getPost('data_keterangan'));
            $code = trim($this->request->getPost('data_code'));

            $rules = [
                'data_kategori' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Material category is required"
                    ]
                ],
                'data_workshop' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Material workshop is required"
                    ]
                ],
                'data_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Material name is required"
                    ]
                ],
                'data_cust_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Customer material name is required"
                    ]
                ],
                'data_uom' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Material unit is required"
                    ]
                ],
                'data_color' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Material color is required"
                    ]
                ],
            ];

            if ($kategori === 'd40102ed-f432-4ce6-af65-6814b4cbc974') {
                $rules = array_merge($rules, [
                    'data_code' => [
                        'rules' => 'required|min_length[3]|max_length[50]|is_unique[m_material.code]',
                        'errors' => [
                            'required' => 'Material code is required',
                            'min_length' => 'Minimum material code is 3 characters ',
                            'max_length' => 'Maximum material code is 50 characters',
                            'is_unique' => 'Material already exists'
                        ]
                    ]
                ]);
            }

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());
                log_action($this->module, $action, 'error', current_url(), "Save failed. Failed to make validation");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Save faile. Faile to make validation : " . $error_message);
            }

            $data = [
                'kategori' => $kategori,
                'code' => $code,
                'name' => $name,
                'cust_part_name' => $custName,
                'color' => $color,
                'uom' => $satuan,
                'workshop' => $workshop,
                'keterangan' => $keterangan,
                'updated_by' => $this->NIK
            ];

            $update = $this->materialModel->update($idMaterial, $data);
            if (!$update) {
                log_action($this->module, $action, "error", current_url(), "Update failed", '', json_encode([
                    'data' => $this->materialModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed, there was an error during your update");
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $action, "error", current_url(), "Update failed", '', json_encode([
                    'data' => $this->db->error()
                ]));
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed due to databse error");
            }

            log_action($this->module, $action, "success", current_url(), "Data saved successfully", '', json_encode([
                'data' => $data
            ]));
            return pesan(ResponseInterface::HTTP_OK, "Save success");
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

    function prevData()
    {
        $action = "Prev data";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request method not allowed");
        }

        $this->db->transStart();

        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $action, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['code'])) {
                log_action($this->module, $action, "error", current_url(), "Material no is missing in JSON input");
                throw new \Exception("Material no is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Material no is missing in JSON input");
            }

            $code = $json_data['code'];
            $getPreviousData = $this->materialModel->prevData($code);
            if (!$getPreviousData) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the first data");
            }

            $this->db->transComplete();
            if ($this->db->transStart() === false) {
                $this->db->transRollback();
                log_action($this->module, $action, "error", current_url(), "Failed to get previous data", '', json_encode([
                    "data" => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to get previous data");
            }

            $data = [
                'token' => enkripsi($getPreviousData->id)
            ];

            return pesan(ResponseInterface::HTTP_OK, "Previous data found", $data);
        } catch (\Exception $e) {
            log_action($this->module, $action, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured" . $e->getMessage());
        }
    }

    function nextData()
    {
        $action = "Next Data";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), "Request method not found");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $action, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
            }

            if (!isset($json_data['kategori'])) {;
                log_action($this->module, $action, "error", current_url(), "Document type is missing from JSON input");
                throw new \Exception("Document type is missing from JSON input");
            }

            if (!isset($json_data['code'])) {
                log_action($this->module, $action, 'error', current_url(), "Machine no is missing in JSON input");
                throw new \Exception("Machine no is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Machine no is missing in JSON input");
            }

            $code = $json_data['code'];
            $getNextData = $this->materialModel->nextData($code);
            if (!$getNextData) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the last data");
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $action, 'error', current_url(), "Failed to get next data", '', json_encode([
                    'data' => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Faile to get next data due to database transaction error");
            }

            $data = [
                'token' => enkripsi($getNextData->id)
            ];
            if (!isset($json_data['token'])) {
                log_action($this->module, $action, "error", current_url(), "Material token is missing in JSON input");
                throw new \Exception("Material token is missing in JSON input");
            }

            $kategori = $json_data['kategori'];
            $id_material = $json_data['token'];

            switch ($kategori) {
                case 1:
                    $get_material = $this->materialModel->getPartData($id_material);
                    break;
                case 2:
                    $get_material =  $this->machineModel->getMachineData($id_material);
                    break;
                case 3:
                    break;
                case 4:
                    break;
            }



            return pesan(ResponseInterface::HTTP_OK, "Next data found", $data);
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

    function exportData()
    {
        $filename = "material_list" . date("Ymd_his") . 'xlsx';
        $headers = ['Kategori', 'Workshop', 'Part No', 'Part Name', 'Customer Part Name', 'Unit'];

        $dataCallback = function ($offset, $limit) {
            $column = 'kategori, workshop, code, name, cust_part_name, satuan';
            return $this->masterModel->getChunkedData('vw_material', $offset, $limit, 'code', $column);
        };

        return export_to_excel($filename, $headers, $dataCallback);
    }
}
