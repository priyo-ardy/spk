<?php

namespace App\Controllers\Transaction\SPK\General;

use App\Controllers\BaseController;
use App\Models\DataTable\DataTableModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\General\GeneralSpkModel;
use App\Models\Transaction\SPK\General\GeneralSpkDetailsModel;
use App\Models\MasterData\CommonData\Machine\MachineModel;
use App\Models\MasterData\CommonData\Employee\EmployeeModel;
use App\Models\MasterData\CommonData\Dept\DeptModel;
use App\Models\MasterData\CommonData\EquipmentType\EquipmentTypeModel;
use App\Models\MasterData\CommonData\Leader\LeaderModel;
use App\Models\Master\MasterModel;
use CodeIgniter\HTTP\Response;
use Config\Services;
use Config\Database;

class GeneralSpk extends BaseController
{
    protected $generalModel;
    protected $detailModel;
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
    protected $db;

    public function __construct()
    {
        $this->module = "Equipment SPK";
        $this->generalModel = new GeneralSpkModel();
        $this->detailModel = new GeneralSpkDetailsModel();
        $this->mesinModel = new MachineModel();
        $this->masterModel = new MasterModel();
        $this->karyawanModel = new EmployeeModel();
        $this->deptModel = new DeptModel();
        $this->tipeModel = new EquipmentTypeModel();
        $this->leaderModel = new LeaderModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
        $this->db = Database::connect();

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

        $this->db->transStart();

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
            $photos = $this->request->getFileMultiple('fupload');
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
                    'rules' => 'uploaded[fupload]|max_size[fupload,51200]|is_image[fupload]|mime_in[fupload,image/jpg,image/jpeg,image/png]|ext_in[fupload,jpg,jpeg,png]',
                    'errors' => [
                        'uploaded' => 'Problem position photo is required',
                        'max_size' => 'Problem position photo maximum size is 50MB',
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

            // insert header
            $data_header = [
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
                'description' => $description,
                'created_by' => $this->NIK,
            ];


            $insert_header = $this->generalModel->insert($data_header);
            if(!$insert_header){
                log_action($this->module, $aksi, "error", current_url(), "Save failed", '', json_encode([
                    'data' => $this->generalModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed, there was an error during processing your request, please try again or contact your administrator");
            }

            // Processing multiple image upload
            $success_count = 0;
            $error_message = [];
            $baris = 1;
            $uploadPath = FCPATH . 'uploads/equipment_spk';
                
            if(!is_dir($uploadPath)){
                mkdir($uploadPath, 0777, true);
                chmod($uploadPath, 0777);
            }

            $data_details= [];

            foreach($photos as $photo){
                if($photo->isValid() && !$photo->hasMoved()){
                    $fileName = "$code-$baris." . $photo->getExtension();
                    $photo->move($uploadPath, $fileName, true);
                    $data_details = [
                        'id' => generate_uuid(),
                        'id_spk' => $id,
                        'urut' => $baris,
                        'file_name' => $fileName,
                        'file_size' => $photo->getSize(),
                        'file_path' => $uploadPath,
                        'created_by' => $this->NIK,
                    ];

                    $insert_details = $this->detailModel->insert($data_details);
                    if(!$insert_details){
                        // Rollback transaction jika gagal insert detail
                        $error_messages[] = "Failed to save image data for file: " . $photo->getName();
                        // Hapus file yang sudah diupload
                        if (file_exists($uploadPath . $fileName)) {
                            unlink($uploadPath . $fileName);
                        }
                        else {
                            $success_count++;
                            $baris++;
                        }
                    }else {
                        $error_messages[] = "Invalid file: " . $photo->getName();
                    }
                    $baris++;
                }
            }

            $this->db->transComplete();
            if($this->db->transStatus() === false){
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Save failed", '', json_encode([
                    'data' => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed due to database transaction error");
            }

            if(!empty($error_message)){
                log_action($this->module, $aksi, "warning", current_url(), "Save completed with some errors", implode(", ", $error_messages));
                return pesan(ResponseInterface::HTTP_OK, "Data saved successfully, but there were some issues with file uploads: " . implode(", ", $error_messages));
            }

            log_action($this->module, $aksi, "success", current_url(), "Data saved successfully with $baris images");
            return pesan(ResponseInterface::HTTP_OK, "Data saved successfully with $baris images");
        }catch(\Exception $e){
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
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
        $get_details = $this->detailModel->where('id_spk', $id_spk)->orderBy('urut', 'asc')->findAll();
        if($get_data){
            $data = [
                'title' => "Equipment SPK Details - $get_data->code",
                'token' => $token,
                'data' => $get_data,
                'details' => $get_details,
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

        $this->db->transStart();

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
            $photos = $this->request->getFileMultiple('fupload');
            $description = trim($this->request->getPost('data_keterangan'));
            $success_count = 0;
            

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

            if (count($photos) > 1) {
                $rules = array_merge([
                    'fupload' => [
                        'label' => 'Foto Karyawan',
                        'rules' => 'uploaded[fupload]|max_size[fupload,51200]|is_image[fupload]|mime_in[fupload,image/jpg,image/jpeg,image/png]|ext_in[fupload,jpg,jpeg,png]',
                        'errors' => [
                            'uploaded' => 'Employee photo is required',
                            'max_size' => 'Employee photo maximum size is 50MB',
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

            $data_header = [
                'dept' => $workshop,
                'report_by' => $staff,
                'report_date' => $tanggal,
                'equipment' => $equipment,
                'equipment_no' => $equipment_no,
                'equipment_model' => $equipment_model,
                'equipment_type' => $equipment_type,
                'leader' => $leader,
                'description' => $description,
                'updated_by' => $this->NIK,
            ];

            $update_header = $this->generalModel->update($id, $data_header);
            if(!$update_header){
                log_action($this->response, $aksi, "error", current_url(), "Update failed", '', json_encode([
                    'data' => $this->generalModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed, there was an error during processing your request, please try again or contact your administrator");
            }

            $get_max_row = $this->detailModel->where('id_spk', $id)->orderBy('urut', 'desc')->limit(1)->first();
            if($get_max_row){
                $baris = $get_max_row->urut + 1;
            }else{
                $baris = 1;
            }

            if(count($photos) > 1){
                $error_message = [];
                $uploadPath = FCPATH . 'uploads/equipment_spk';
                if(!is_dir($uploadPath)){
                    mkdir($uploadPath, 0777, true);
                    chmod($uploadPath, 0777);
                }

                foreach($photos as $photo){
                    if($photo->isValid() && !$photo->hasMoved()){
                        $fileName = "$code-$baris." . $photo->getExtension();
                        $photo->move($uploadPath, $fileName, true);
                        $data_details = [
                            'id' => generate_uuid(),
                            'id_spk' => $id,
                            'urut' => $baris,
                            'file_name' => $fileName,
                            'file_size' => $photo->getSize(),
                            'file_path' => $uploadPath,
                            'created_by' => $this->NIK,
                        ];

                        $insert_details = $this->detailModel->insert($data_details, true);

                        if(!$insert_details){
                            // Rollback transaction jika gagal insert detail
                            $error_messages[] = "Failed to save image data for file: " . $photo->getName();
                            // Hapus file yang sudah diupload
                            if (file_exists($uploadPath . $fileName)) {
                                unlink($uploadPath . $fileName);
                            }
                            else {
                                $success_count++;
                                $baris++;
                            }
                        }

                        $success_count++;
                    }else {
                        $error_messages[] = "Invalid file: " . $photo->getName();
                    }

                    $baris++;
                }
            }

            $this->db->transComplete();
            if($this->db->transStatus() === false){
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Update failed", '', json_encode([
                    'data' => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed due to database transaction error");
            }

            if(!empty($error_messages)){
                log_action($this->module, $aksi, "warning", current_url(), "Update completed with some errors", implode(", ", $error_messages));
                return pesan(ResponseInterface::HTTP_OK, "Data updated successfully, but there were some issues with file uploads: " . implode(", ", $error_messages));
            }

            return pesan(ResponseInterface::HTTP_OK, "Update successfully with $success_count new image files");
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

    function DeleteImage(){
        $aksi = "Delete SPK Image";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not found");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();
        try{
            $json_data = $this->request->getJSON(true);
            if(!is_array($json_data)){
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if(!isset($json_data['token'])){
                log_action($this->module, $aksi, "error", current_url(), "Image ID is missing in JSON input");
                throw new \Exception("Image ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Image ID is missing in JSON input");
            }

            $id_detail = dekripsi($json_data['token']);

            $get = $this->detailModel->where('id', $id_detail)->first();
            if(!$get){
                log_action($this->module, $aksi, "error", current_url(), "Image not found");
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Image data not found");
            }

            $uploadPath = FCPATH . 'uploads/equipment_spk/';
            $fileName = $get->file_name;

            unlink($uploadPath . $fileName);

            $delete = $this->detailModel->delete($id_detail, true);
            if(!$delete){
                log_action($this->module, $aksi, "error", current_url(), "Failed to delete the image");

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to delete the image file");
            }

            $this->db->transComplete();
            return pesan(ResponseInterface::HTTP_OK, "Image deleted");
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

            $get_header = $this->generalModel->getDataById($id_spk);
            if(!$get_header){
                log_action($this->module, $aksi, "error", current_url(), "SPK data not found");
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "SPK data not found");
            }

            $get_details = $this->detailModel->where('id_spk', $id_spk)->orderBy('urut', 'asc')->findAll();
            if(!$get_details){
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Image not found");
            }

            $details = [];
            foreach($get_details as $item){
                $details[] = [
                    'image' => base_url().'uploads/equipment_spk/' . $item->file_name
                ];
            }

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_OK)
                ->setJSON([
                    'title' => $get_header->code,
                    'data' => $details
                ]);

            return pesan(ResponseInterface::HTTP_OK, "SPK image data found", $data);
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

    function exportData(){
        $filename = "equipment_spk_list_". date("Ymd_his") . '.xlsx';
        $headers =  ['SPK No.', "Requested Date", 'Requested Dept', 'Reported By', 'Equipment Name', 'Equipment No.', 'Equipment Model', 'Equipment Type', 'Problem Description', 'Team Leader', 'Status'];
        
        $dataCallback = function ($offset, $limit){
            $column = 'code, report_date, nama_pelapor, nama_dept, equipment_name, equipment_no, equipment_model, tipe_equipment, description, nama_leader, nama_status';
            return $this->masterModel->getChunkedData('vw_t_equipment_spk', $offset, $limit, 'code', $column);
        };

        return export_to_excel($filename, $headers, $dataCallback);
    }
}
