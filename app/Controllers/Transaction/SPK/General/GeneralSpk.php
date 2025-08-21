<?php

namespace App\Controllers\Transaction\SPK\General;

use App\Controllers\BaseController;
use App\Models\DataTable\DataTableModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\General\GeneralSpkModel;
use App\Models\MasterData\CommonData\Machine\MachineModel;
use App\Models\MasterData\CommonData\Employee\EmployeeModel;
use App\Models\MasterData\CommonData\Dept\DeptModel;
use App\Models\MasterData\CommonData\EquipmentType\EquipmentTypeModel;
use App\Models\MasterData\CommonData\Leader\LeaderModel;
use App\Models\Master\MasterModel;
use CodeIgniter\HTTP\Response;
use Config\Services;

class GeneralSpk extends BaseController
{
    protected $generalModel;
    protected $mesinModel;
    protected $masterModel;
    protected $dataTable;
    protected $karyawanModel;
    protected $deptModel;
    protected $tipeModel;
    protected $leaderModel;
    protected $validasi;
    protected $enkripsi;
    protected $module;

    public function __construct()
    {
        $this->module = "Equipment SPK";
        $this->generalModel = new GeneralSpkModel();
        $this->mesinModel = new MachineModel();
        $this->masterModel = new MasterModel();
        $this->karyawanModel = new EmployeeModel();
        $this->deptModel = new DeptModel();
        $this->tipeModel = new EquipmentTypeModel();
        $this->leaderModel = new LeaderModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'vw_t_equipment_spk ';
        $column_order = ['code', 'report_date', 'nama_pelapor', 'equipment_name', 'equipment_no', 'equipment_model', 'tipe_equipment', 'description', 'nama_status'];
        $column_search = ['code', 'report_date', 'nama_pelapor', 'equipment_name', 'equipment_no', 'equipment_model', 'tipe_equipment', 'description', 'nama_status'];
        // $column_search = ['code', 'report_date', 'nama_pelapor'];
        $order = array('report_date' => 'desc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    function loadTable(){
        log_action($this->module, "table", 'info', current_url(), "Generating list of equipment SPK");

        try{
            $lists = $this->dataTable->get_datatables();
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $no = $start + 1;
            $data = [];

            foreach($lists as $item){
                $row = [];

                $row[] = '
                            <a href="'. base_url().'spk_general/show/'. enkripsi($item->id) .'" onclick="loading()" class="text-primary fw-bolder  link-underline-opacity-0 link-underline-opacity-100-hover">'.$item->code.'</a>
                        ';
                $row[] = $item->report_date;
                $row[] = $item->nama_dept;
                $row[] = $item->nama_pelapor;
                $row[] = $item->equipment_name;
                $row[] = $item->equipment_no;
                $row[] = $item->equipment_model;
                $row[] = $item->tipe_equipment;
                $row[] = character_limiter($item->description, 20);
                $row[] = '
                        <button type="button" class="btn rounded-0 btn-primary btn-sm" onclick="showImage(`'.enkripsi($item->id).'`)">Show Image</button>
                    ';
                $row[] = $item->nama_leader;
                $row[] = $item->nama_status;
                $row[] = '';

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
        } catch(\Exception $e){
            log_action($this->module, 'table', 'error', current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured : <b>" . $e->getMessage() . "</b>");
        }
    }

    public function index()
    {
        $aksi =  "open";

        log_action($this->module, $aksi, "info", current_url(), "Opening list of equipment SPK");

        $data = [
            'title' => "Equipment SPK",
            'footer' => [
                '<script src="'.base_url().'js/Transaction/SPK/General/general.js'.'"></script>'
            ]
        ];

        return view('Transaction/SPK/General/index', $data);
    }
    
    function add(){
        $aksi =  "add";

        log_action($this->module, $aksi, "info", current_url(), "Opening create a new Equipment SPK");

        $data = [
            'title' => "Crete New Equipment SPK",
            'mesin' => $this->mesinModel->orderBy('code', 'asc')->findAll(),
            'dept' => $this->deptModel->generateList(),
            'karyawan' => $this->karyawanModel->generateList(),
            'equipment_type' => $this->tipeModel->generateList(),
            'leader' => $this->leaderModel->generateList(),
            'footer' => [
                '<script src="'.base_url().'js/Transaction/SPK/General/add.js'.'"></script>'
            ]
        ];

        return view('Transaction/SPK/General/add', $data);
    }

    function saveData(){
        $aksi = "save";

        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try{
            $id  = generate_uuid();
            $workshop = trim($this->request->getPost('data_workshop'));
            $staff = trim($this->request->getPost('data_staff'));
            $tanggal = trim($this->request->getPost('data_tanggal'));
            $equipment = trim($this->request->getPost('data_material'));
            $equipment_no = trim($this->request->getPost('data_nomor'));
            $equipment_model = trim($this->request->getPost('data_model'));
            $equipment_type = trim($this->request->getPost('data_tipe'));
            $leader = trim($this->request->getPost('data_spv'));
            $photo = $this->request->getFile('fupload');
            $description = trim($this->request->getPost('data_keterangan'));
            $parse_equipment_no = str_replace('#', '', $equipment_no);
            $parse_date = date("Ymd");
            $prefix = "SLMMA-$parse_date-$parse_equipment_no-";
            $code = $this->masterModel->generateCode('t_spk_equipment', 'code', $prefix, 6);

            $rules = [
                'data_workshop' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Reported department/workshop is required"
                    ]
                ],
                'data_staff' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Reported staff is required"
                    ]
                ],
                'data_tanggal' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => "Reported date is required",
                        'valid_date' => "Reported date must have a valid date format"
                    ]
                ],
                'data_material' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Equipment name is required"
                    ]
                ],
                'data_tipe' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Equipment type is required"
                    ]
                ],
                'data_spv' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Team leader/supervisor is required"
                    ]
                ],
                'fupload' => [
                    'label' => 'Foto Karyawan',
                    'rules' => 'uploaded[fupload]|max_size[fupload,5120]|is_image[fupload]|mime_in[fupload,image/jpg,image/jpeg,image/png]|ext_in[fupload,jpg,jpeg,png]',
                    'errors' => [
                        'uploaded' => 'Problem position photo is required',
                        'max_size' => 'Problem position photo maximum size is 5MB',
                        'is_image' => 'Problem position photo must image file type (JPG/JPEG/PNG)',
                        'mime_in' => 'Problem position photo file format must JPG, JPEG atau PNG',
                        'ext_in' => 'Problem position photo file extension mus .jpg, .jpeg atau .png'
                    ]
                ],
                'data_keterangan' => [
                    'rules' => 'required|min_length[10]',
                    'errors' => [
                        'required' => "Problem description is required",
                        'min_length' => "The minimum character of problem description is {param}"
                    ]
                ]
            ];

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode("<br>", $this->validasi->getErrors());
                log_action($this->module, $aksi, "error", current_url(), "Save failed, failed to make validation");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Save failed, failed to make a validation : $error_message");
            }

            $fileName = "$code." . $photo->getExtension();

            $uploadPath = FCPATH . 'uploads/equipment_spk';
            if(!is_dir($uploadPath)){
                mkdir($uploadPath, 0777, true);
                chmod($uploadPath, 0777);
            }

            if(!$photo->move($uploadPath, $fileName, true)){
                log_action($this->module, $aksi, "error", current_url(), "Failed to upload the image file", '', json_encode([
                    'data' => $photo->getErrorString()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to upload the image file " . $photo->getErrorString());
            }
            

            $data = [
                'id' => $id,
                'code' => $code,
                'dept' => $workshop,
                'report_by' => $staff,
                'report_date' => $tanggal,
                'equipment' => $equipment,
                'equipment_no' => $equipment_no,
                'equipment_model' => $equipment_model,
                'equipment_type' => $equipment_type,
                'leader' => $leader,
                'photo' => $fileName,
                'description' => $description,
                'status' => 0,
                'created_by' => $this->NIK,
            ];

            $insert = $this->generalModel->insert($data);
            if(!$insert){
                log_action($this->module, $aksi, "error", current_url(), "Save failed, there was an error during processing your request", '', json_encode([
                    'data' => $this->generalModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed, there was an error during processing your request");
            }

            log_action($this->module, $aksi, "success", current_url(), "Save success with transaction code $code");

            return pesan(ResponseInterface::HTTP_CREATED, "Save success with transaction number $code");
        } catch(\Exception $e){
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occurred", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function showData($token){
        $aksi = "show";
        $id_spk = dekripsi($token);

        $get_data = $this->generalModel->getDataById($id_spk);
        if($get_data){
            $data = [
                'title' => "Equipment SPK Details - $get_data->code",
                'token' => $token,
                'data' => $get_data,
                'mesin' => $this->mesinModel->orderBy('code', 'asc')->findAll(),
                'dept' => $this->deptModel->generateList(),
                'karyawan' => $this->karyawanModel->generateList(),
                'equipment_type' => $this->tipeModel->generateList(),
                'leader' => $this->leaderModel->generateList(),
                'footer' => [
                    '<script src="' . base_url().'js/Transaction/SPK/General/edit.js' . '"></script>'
                ]
            ];

            return view('Transaction/SPK/General/edit', $data);
        }
    }

    function updateData(){
        $aksi = "update";

        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try{
            $token  = trim($this->request->getPost('data_token'));
            $id = dekripsi($token);
            $code = trim($this->request->getPost('data_code'));
            $workshop = trim($this->request->getPost('data_workshop'));
            $staff = trim($this->request->getPost('data_staff'));
            $tanggal = trim($this->request->getPost('data_tanggal'));
            $equipment = trim($this->request->getPost('data_material'));
            $equipment_no = trim($this->request->getPost('data_nomor'));
            $equipment_model = trim($this->request->getPost('data_model'));
            $equipment_type = trim($this->request->getPost('data_tipe'));
            $leader = trim($this->request->getPost('data_spv'));
            $photo = $this->request->getFile('fupload');
            $description = trim($this->request->getPost('data_keterangan'));
            

            $rules = [
                'data_workshop' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Reported department/workshop is required"
                    ]
                ],
                'data_staff' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Reported staff is required"
                    ]
                ],
                'data_tanggal' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => "Reported date is required",
                        'valid_date' => "Reported date must have a valid date format"
                    ]
                ],
                'data_material' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Equipment name is required"
                    ]
                ],
                'data_tipe' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Equipment type is required"
                    ]
                ],
                'data_spv' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Team leader/supervisor is required"
                    ]
                ],
                'data_keterangan' => [
                    'rules' => 'required|min_length[10]',
                    'errors' => [
                        'required' => "Problem description is required",
                        'min_length' => "The minimum character of problem description is {param}"
                    ]
                ]
            ];

            if ($photo && $photo->isValid()) {
                $rules = array_merge([
                    'fupload' => [
                        'label' => 'Foto Karyawan',
                        'rules' => 'uploaded[fupload]|max_size[fupload,2048]|is_image[fupload]|mime_in[fupload,image/jpg,image/jpeg,image/png]|ext_in[fupload,jpg,jpeg,png]',
                        'errors' => [
                            'uploaded' => 'Employee photo is required',
                            'max_size' => 'Employee photo maximum size is 2MB',
                            'is_image' => 'Employee photo must image file type (JPG/JPEG/PNG)',
                            'mime_in' => 'Employee photo file format must JPG, JPEG atau PNG',
                            'ext_in' => 'Employee photo file extension mus .jpg, .jpeg atau .png'
                        ]
                    ],
                ]);
            }

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode("<br>", $this->validasi->getErrors());
                log_action($this->module, $aksi, "error", current_url(), "Save failed, failed to make validation");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Save failed, failed to make a validation : $error_message");
            }

            if($photo && $photo->isValid()){
                $fileName = $code . "." . $photo->getExtension();

                $uploadPath = FCPATH . 'uploads/equipment_spk';
                if(!is_dir($uploadPath)){
                    mkdir($uploadPath, 0777, true);
                    chmod($uploadPath, 0777);
                }

                unlink($uploadPath . '/' . $fileName);

                if(!$photo->move($uploadPath, $fileName, true)){
                    log_action($this->module, $aksi, "error", current_url(), "Failed to upload the image file", '', json_encode([
                        'data' => $photo->getErrorString()
                    ]));

                    return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to upload the image file " . $photo->getErrorString());
                }

                $data = [
                    'dept' => $workshop,
                    'report_by' => $staff,
                    'report_date' => $tanggal,
                    'equipment' => $equipment,
                    'equipment_no' => $equipment_no,
                    'equipment_model' => $equipment_model,
                    'equipment_type' => $equipment_type,
                    'leader' => $leader,
                    'photo' => $fileName,
                    'description' => $description,
                    'status' => 0,
                    'updated_by' => $this->NIK,
                ];
            }else{
                $data = [
                    'dept' => $workshop,
                    'report_by' => $staff,
                    'report_date' => $tanggal,
                    'equipment' => $equipment,
                    'equipment_no' => $equipment_no,
                    'equipment_model' => $equipment_model,
                    'equipment_type' => $equipment_type,
                    'leader' => $leader,
                    'description' => $description,
                    'status' => 0,
                    'updated_by' => $this->NIK,
                ];
            }

            $update = $this->generalModel->update($id, $data);
            if(!$update){
                log_action($this->module, $aksi, "error", current_url(), "Update failed", '', json_encode([
                    'data' => $this->generalModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed, there was an error during processing your request");
            }

            log_action($this->module, $aksi, "success", current_url(), "Update success");

            return pesan(ResponseInterface::HTTP_OK, "Update success");

        } catch(\Exception $e){
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occurred", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function showImage(){
        $aksi = "image";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, "get", "error", current_url(), "Request method not allowed");

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

            $id_spk = dekripsi($json_data['token']);

            $get_image = $this->generalModel->getDataById($id_spk);
            if(!$get_image){
                log_action($this->module, $aksi, "error", current_url(), "SPK data not found");
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "SPK data not found");
            }

            $data = [
                'title' => "Equipment SPK - " . $get_image->code,
                'image' => base_url().'uploads/equipment_spk/' . $get_image->photo
            ];

            return pesan(ResponseInterface::HTTP_OK, "SPK data found", $data);
        } catch(\Exception $e){
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occurred", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }
}
