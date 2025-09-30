<?php

namespace App\Controllers\MasterData\CommonData\Machine;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\Machine\MachineModel;
use App\Models\MasterData\CommonData\Workshop\WorkshopModel;
use App\Models\MasterData\CommonData\Tonnage\TonnageModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Database;
use Config\Services;

class Machine extends BaseController
{
    protected $db;
    protected $mesinModel;
    protected $workshopModel;
    protected $tonageModel;
    protected $module;
    protected $validasi;
    protected $enkripsi;
    protected $masterModel;
    protected $dataTable;

    public function __construct()
    {
        $this->module = "Machine Management";
        $this->mesinModel = new MachineModel();
        $this->workshopModel = new WorkshopModel();
        $this->tonageModel = new TonnageModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
        $this->db = Database::connect();

        $table = 'vw_mesin';
        $column_order = ['code', 'nomor_mesin', 'name', 'model', 'brand', 'serial_no', 'workshop'];
        $column_search = ['code', 'nomor_mesin', 'name', 'model', 'brand', 'serial_no', 'workshop'];
        $order = array('code' => 'asc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }


    public function index()
    {
        $data = [
            'title' => "Machine Management",
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Machine/machine.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/Machine/index', $data);
    }

    function loadTable()
    {
        log_action($this->module, 'table', 'info', current_url(), "Generating machine list");

        $lists = $this->dataTable->get_datatables();
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $no = $start + 1;
        $data = [];

        foreach ($lists as $list) {
            $row = [];

            $row[] = $no++;
            $row[] = '
                <a href="' . base_url() . 'machine/details/'  . enkripsi($list->id) . '" onclick="loading()" class="text-primary fw-bolder link-underline-opacity-0 link-underline-opacity-100-hover">' . $list->code . '</a>
            ';
            $row[] = $list->nomor_mesin;
            $row[] = $list->name;
            $row[] = $list->model != NULL ? $list->model : '-';
            $row[] = $list->workshop != NULL ? $list->workshop : '-';
            $row[] = $list->tonnage != NULL ? $list->tonnage : '-';
            $row[] = $list->brand != NULL ? $list->brand : '-';
            $row[] = $list->serial_no != NUll ? $list->serial_no : '-';
            $row[] = $list->rate != NULL ? $list->rate : '-';
            $row[] = $list->mfg_date != NULL ? $list->mfg_date : '-';
            $row[] = $list->purchase_date != NULL ? $list->purchase_date : '-';
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

    function add()
    {
        $data = [
            'title' => 'Add New Machine',
            'tonnage' => $this->tonageModel->generateList(),
            'workshop' => $this->workshopModel->generateList(),
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Machine/add.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/Machine/add', $data);
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
            $mesin = trim($this->request->getPost('data_mesin'));
            $nama = trim($this->request->getPost('data_nama'));
            $model = trim($this->request->getPost('data_model'));
            $brand = trim($this->request->getPost('data_brand'));
            $serial = trim($this->request->getPost('data_serial'));
            $tonnage = trim($this->request->getPost('data_tonnage'));
            $rate = trim($this->request->getPost('data_rate'));
            $mfgDate = trim($this->request->getPost('data_tanggal'));
            $purchaseDate = trim($this->request->getPost('data_beli'));
            $keterangan = trim($this->request->getPost('data_keterangan'));
            $code = $this->masterModel->generateCode('m_mesin', 'code', 'MCH-', 5);

            $rules = [
                'data_workshop' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine workshop is required"
                    ]
                ],
                'data_mesin' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine number is required"
                    ]
                ],
                'data_nama' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine name is required"
                    ]
                ],
                'data_model' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine specifications is required"
                    ]
                ],
                'data_brand' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine brand is required"
                    ]
                ],
                'data_serial' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine serial no is required"
                    ]
                ],
                'data_tonnage' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine tonnage is required"
                    ]
                ],
                'data_rate' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine rate is required"
                    ]
                ],
                'data_tanggal' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine manufactuirng date is required",
                        'valid_date' => "Manufacturing date must have valid date format"
                    ]
                ],
                'data_beli' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine purchase date is required",
                        'valid_date' => "Purchase date must have valid date format"
                    ]
                ],
            ];

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode("<br>", $this->validasi->getErrors());
                log_action($this->module, $action, "error", current_url(), "Save failed, failed to make validation");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Save failed, failed to make a validation : $error_message");
            }

            $data = [
                'id' => $id,
                'workshop' => $workshop,
                'code' => $code,
                'nomor_mesin' => $mesin,
                'name' => $nama,
                'model' => $model,
                'tonnage' => $tonnage,
                'brand' => $brand,
                'serial_no' => $serial,
                'rate' => $rate,
                'mfg_date' => $mfgDate,
                'purchase_date' => $purchaseDate,
                'remark' => $keterangan,
                'created_by' => $this->NIK
            ];

            $insert = $this->mesinModel->insert($data);
            if (!$insert) {
                log_action($this->module, $action, "error", current_url(), "Save failed", '', json_encode([
                    'data' => $this->mesinModel->errors()
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
            log_action($this->module, $action, "error", current_url(), "Unexpected error occured", '', json_encode([
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
        log_action($this->module, $action, 'open', current_url(), "Opening machine details page");
        $idMesin = dekripsi($token);

        $getData = $this->mesinModel->getDataById($idMesin);

        $data = [
            'title' => 'Machine Details',
            'data' => $getData,
            'token' => enkripsi($getData->id),
            'tonnage' => $this->tonageModel->generateList(),
            'workshop' => $this->workshopModel->generateList(),
            'footer' => [
                '<script src="' . base_url() . 'js/MasterData/CommonData/Machine/edit.js' . '"></script>'
            ]
        ];

        return view('MasterData/CommonData/Machine/edit', $data);
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
            $idMesin = dekripsi($token);
            $code = trim($this->request->getPost('data_code'));
            $workshop = trim($this->request->getPost('data_workshop'));
            $mesin = trim($this->request->getPost('data_mesin'));
            $nama = trim($this->request->getPost('data_nama'));
            $model = trim($this->request->getPost('data_model'));
            $brand = trim($this->request->getPost('data_brand'));
            $serial = trim($this->request->getPost('data_serial'));
            $tonnage = trim($this->request->getPost('data_tonnage'));
            $rate = trim($this->request->getPost('data_rate'));
            $mfgDate = trim($this->request->getPost('data_tanggal'));
            $purchaseDate = trim($this->request->getPost('data_beli'));
            $keterangan = trim($this->request->getPost('data_keterangan'));

            $rules = [
                'data_workshop' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine workshop is required"
                    ]
                ],
                'data_mesin' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine number is required"
                    ]
                ],
                'data_nama' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine name is required"
                    ]
                ],
                'data_model' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine specifications is required"
                    ]
                ],
                'data_brand' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine brand is required"
                    ]
                ],
                'data_serial' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine serial no is required"
                    ]
                ],
                'data_tonnage' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine tonnage is required"
                    ]
                ],
                'data_rate' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine rate is required"
                    ]
                ],
                'data_tanggal' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine manufactuirng date is required",
                        'valid_date' => "Manufacturing date must have valid date format"
                    ]
                ],
                'data_beli' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Machine purchase date is required",
                        'valid_date' => "Purchase date must have valid date format"
                    ]
                ],
            ];

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());
                log_action($this->module, $action, 'error', current_url(), "Save failed. Failed to make validation");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Save faile. Faile to make validation : " . $error_message);
            }

            $data = [
                'workshop' => $workshop,
                'nomor_mesin' => $mesin,
                'name' => $nama,
                'model' => $model,
                'tonnage' => $tonnage,
                'brand' => $brand,
                'serial_no' => $serial,
                'rate' => $rate,
                'mfg_date' => $mfgDate,
                'purchase_date' => $purchaseDate,
                'remark' => strip_tags($keterangan),
                'updated_by' => $this->NIK
            ];

            $update = $this->mesinModel->update($idMesin, $data);
            if (!$update) {
                log_action($this->module, $action, "error", current_url(), "Update failed", '', json_encode([
                    'data' => $this->mesinModel->errors()
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
            log_action($this->module, $action, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function prevData()
    {
        $action = "Prev Data";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $action, 'error', current_url(), "Request method not found");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();

        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $action, 'error', current_url(), "Input is not valid JSON object");
                throw new \Exception("Input is not valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON input");
            }

            if (!isset($json_data['code'])) {
                log_action($this->module, $action, 'error', current_url(), "Machine no is missing in JSON input");
                throw new \Exception("Machine no is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Machine no is missing in JSON input");
            }

            $code = $json_data['code'];
            $getPrevData = $this->mesinModel->prevData($code);
            if (!$getPrevData) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Your are in the first data");
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $action, 'error', current_url(), "Failed to get previous data", '', json_encode([
                    "data" => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to get previous data");
            }

            $data = [
                'token' => enkripsi($getPrevData->id)
            ];

            return pesan(ResponseInterface::HTTP_OK, "Prev data found", $data);
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
                log_action($this->module, $action, 'error', current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['code'])) {
                log_action($this->module, $action, 'error', current_url(), "Machine no is missing in JSON input");
                throw new \Exception("Machine no is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Machine no is missing in JSON input");
            }

            $code = $json_data['code'];
            $getNextData = $this->mesinModel->nextData($code);
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

            return pesan(ResponseInterface::HTTP_OK, "Next data found", $data);
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

    function exportData()
    {
        $filename = "machine_list" . date("Ymd_his") . 'xlsx';
        $headers = ['No Mesin', 'Workshop', 'Nama Mesin', 'model', 'Tonnage', 'Brand', 'Serial No', 'Rate', 'Mfg Date', 'Purchase Date'];

        $dataCallback = function ($offset, $limit) {
            $column = 'nomor_mesin, workshop, name, model, tonnage, brand, serial_no, rate, mfg_date, purchase_date';
            return $this->masterModel->getChunkedData('vw_mesin', $offset, $limit, 'nomor_mesin', $column);
        };

        return export_to_excel($filename, $headers, $dataCallback);
    }
}



// function getDataById()
// {
//     $aksi = "get";
//     if ($this->request->getMethod() !== 'POST') {
//         log_action($this->module, "get", "error", current_url(), "Request method not allowed");

//         return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
//     }

//     try {
//         $json_data = $this->request->getJSON(true);
//         if (!is_array($json_data)) {
//             log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
//             throw new \Exception("Input is not a valid JSON object");
//             return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
//         }

//         if (!isset($json_data['token'])) {
//             log_action($this->module, $aksi, "error", current_url(), "Job data ID is missing in JSON input");
//             throw new \Exception("Job data ID is missing in JSON input");
//             return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Job data ID is missing in JSON input");
//         }

//         $id_mesin = $json_data['token'];

//         $get = $this->mesinModel->where('id', $id_mesin)->first();
//         if (!$get) {
//             log_action($this->module, $aksi, "error", current_url(), 'Machine data not found');

//             return pesan(ResponseInterface::HTTP_NOT_FOUND, "Machine data not found");
//         }

//         $data = [
//             'id' => $get->id,
//             'code' => $get->code,
//             'workshop' => $get->workshop,
//             'nomor_mesin' => $get->nomor_mesin,
//             'name' => $get->name,
//             'specification' => $get->model,
//             'tonnage' => $get->tonnage,
//             'brand' => $get->brand,
//             'serial_no' => $get->serial_no,
//             'rate' => $get->rate,
//             'mfg_date' => $get->mfg_date,
//             'purchase_date' => $get->purchase_date,
//             'remark' => $get->remark
//         ];

//         return pesan(ResponseInterface::HTTP_OK, "Machine data found", $data);
//     } catch (\Exception $e) {
//         log_action($this->module, $aksi, "error", current_url(), "Unexpected error occurred", '', json_encode([
//             'message' => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//             'trace' => $e->getTraceAsString()
//         ]));

//         return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
//     }
// }
