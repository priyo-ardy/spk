<?php

namespace App\Controllers\Transaction\SPK\Mold;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\Mold\MoldSpkModel;
use App\Models\Transaction\SPK\Mold\MoldSpkDetailsModel;
use App\Models\MasterData\MaterialManagement\Material\MaterialModel;
use App\Models\MasterData\CommonData\Dept\DeptModel;
use App\Models\MasterData\CommonData\Employee\EmployeeModel;
use App\Models\MasterData\CommonData\Leader\LeaderModel;
use App\Models\MasterData\CommonData\RepairReason\RepairReasonModel;
use App\Models\DataTable\DataTableModel;
use App\Models\Master\MasterModel;
use Config\Services;
use Config\Database;

class MoldSpk extends BaseController
{
    protected $db;
    protected $module;
    protected $spkModel;
    protected $detailModel;
    protected $materialModel;
    protected $deptModel;
    protected $karyawanModel;
    protected $leaderModel;
    protected $masterModel;
    protected $repairModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;

    public function __construct()
    {
        $this->module = "Mold SPK";
        $this->spkModel = new MoldSpkModel();
        $this->detailModel = new MoldSpkDetailsModel();
        $this->materialModel = new MaterialModel();
        $this->deptModel = new DeptModel();
        $this->karyawanModel = new EmployeeModel();
        $this->leaderModel = new LeaderModel();
        $this->masterModel = new MasterModel();
        $this->repairModel = new RepairReasonModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
        $this->db = Database::connect();

        $table = 'vw_mold_spk';
        $column_order = ['code', 'report_date', 'nama_dept', 'nama_karyawan', 'kode_part', 'part_name', 'part_model', 'mold_no', 'nama_alasan_repair', 'description', 'status'];
        $column_search = ['code', 'report_date', 'nama_dept', 'nama_karyawan', 'kode_part', 'part_name', 'part_model', 'mold_no', 'nama_alasan_repair', 'description', 'status'];
        $order = array('code' => 'desc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    function loadTable(){
        log_action($this->module, "table", 'info', current_url(), "Generating list of mold SPK");

        try{
            $lists = $this->dataTable->get_datatables();
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $no = $start + 1;
            $data = [];

            foreach($lists as $item){
                $row = [];

                $row[] = '
                            <a href="'. base_url().'spk_mold/show/'. enkripsi($item->id) .'" onclick="loading()" class="text-primary fw-bolder  link-underline-opacity-0 link-underline-opacity-100-hover">'.$item->code.'</a>
                        ';
                $row[] = $item->report_date;
                $row[] = $item->nama_dept;
                $row[] = $item->nama_karyawan;
                $row[] = $item->kode_part;
                $row[] = $item->part_name;
                $row[] = $item->part_model;
                $row[] = $item->mold_no;
                $row[] = $item->nama_alasan_repair;
                $row[] = character_limiter($item->description, 20);
                $row[] = '
                        <button type="button" class="btn rounded-0 btn-primary btn-sm" onclick="showImage(`'.enkripsi($item->id).'`)">Show Image</button>
                    ';
                $row[] = $item->status;
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
        $aksi = "open";
        log_action($this->module, $aksi, "info", current_url(), "Opening list of mold SPK page");

        $data = [
            'title' => "List of Mold SPK",
            'footer' => [
                '<script src="' . base_url().'js/Transaction/SPK/Mold/mold.js' . '"></script>'
            ]
        ];

        return view('Transaction/SPK/Mold/index', $data);
    }

    public function add()
    {
        $aksi = "open";
        log_action($this->module, $aksi, "info", current_url(), "Opening create new mold SPK page");

        $data = [
            'title' => "Create New Mold SPK",
            'dept' => $this->deptModel->generateList(),
            'karyawan' => $this->karyawanModel->generateList(),
            'material' => $this->materialModel->generateList(),
            'repair' => $this->repairModel->generateList(),
            'footer' => [
                '<script src="' . base_url().'js/Transaction/SPK/Mold/add.js' . '"></script>'
            ]
        ];

        return view('Transaction/SPK/Mold/add', $data);
    }

    function saveData(){
        $aksi = "save";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }
        
        $this->db->transStart();

        try{
            $id_header = generate_uuid();
            $workshop = trim($this->request->getPost('data_workshop'));
            $staff = trim($this->request->getPost('data_staff'));
            $tanggal = trim($this->request->getPost('data_tanggal'));
            $part_no = trim($this->request->getPost('data_material'));
            $part_name = trim($this->request->getPost('data_name'));
            $part_model = trim($this->request->getPost('data_model'));
            $mold_no = trim($this->request->getPost('data_mold'));
            $repair_reason = trim($this->request->getPost('data_repair'));
            $description = trim($this->request->getPost('data_keterangan'));
            $prefix_date = date("Ymd");
            $prefix = "SLMMJ-$prefix_date-$mold_no-";
            $code = $this->masterModel->generateCode('t_spk_mold', 'code', $prefix, 6) ;

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
                        'required' => "Part No. is required"
                    ]
                ],
                'data_repair' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Reason for repair is required"
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

            // Insert header
            $data_header = [
                'id' => $id_header,
                'code' => $code,
                'dept' => $workshop,
                'report_by' => $staff,
                'report_date' => $tanggal,
                'part_no' => $part_no,
                'part_name' => $part_name,
                'part_model' => $part_model,
                'mold_no' => $mold_no,
                'repair_reason' => $repair_reason,
                'description' => $description,
                'created_by' => $this->NIK,
            ];

            $insert_header = $this->spkModel->insert($data_header);
            if(!$insert_header){
                log_action($this->module, $aksi, "error", current_url(), "Save failed", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed, there was an error during processing your request, please try again later or contact your administrator");
            }

            // Handle file uploads - PERBAIKAN BAGIAN INI
            $photos = $this->request->getFileMultiple('fupload');
            $success_count = 0;
            $error_messages = [];
            $uploadPath = FCPATH . 'uploads/mold_spk';
            $baris = 1;
                
            if(!is_dir($uploadPath)){
                mkdir($uploadPath, 0777, true);
                chmod($uploadPath, 0777);
            }

            $data_details = [];

            if($photos) {
                foreach($photos as $photo) {
                    if($photo->isValid() && !$photo->hasMoved()) {
                        $fileName = "$code-$baris." . $photo->getExtension();
                        $photo->move($uploadPath, $fileName, true);
                        $data_details = [
                            'id' => generate_uuid(),
                            'id_spk' => $id_header,
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
                    } else {
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

            if(!empty($error_messages)){
                log_action($this->module, $aksi, "warning", current_url(), "Save completed with some errors", implode(", ", $error_messages));
                return pesan(ResponseInterface::HTTP_OK, "Data saved successfully, but there were some issues with file uploads: " . implode(", ", $error_messages));
            }

            log_action($this->module, $aksi, "success", current_url(), "Data saved successfully with $baris images");
            return pesan(ResponseInterface::HTTP_OK, "Data saved successfully with $baris images");
        } catch(\Exception $e){
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
        log_action($this->module, $aksi, "open", current_url(), "Opening mold SPK details page");
        $id_spk = dekripsi($token);
        
        $get_data = $this->spkModel->getDataById($id_spk);

        $data = [
            'title' => "View Mold SPK Details",
            'token' => enkripsi($get_data->id),
            'data' => $get_data,
            'details' => $this->detailModel->where('id_spk', $id_spk)->orderBy('urut', 'asc')->findAll(),
            'dept' => $this->deptModel->generateList(),
            'karyawan' => $this->karyawanModel->generateList(),
            'material' => $this->materialModel->generateList(),
            'repair' => $this->repairModel->generateList(),
            'footer' => [
                '<script src="' . base_url().'js/Transaction/SPK/Mold/edit.js' . '"></script>'
            ]
        ];

        return view('Transaction/SPK/Mold/edit', $data);
    }

    function updateData(){
        $aksi = "update";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();

        try{
            $token = trim($this->request->getPost('data_token'));
            $id_spk = dekripsi($token);
            $code = trim($this->request->getPost('data_code'));
            $workshop = trim($this->request->getPost('data_workshop'));
            $staff = trim($this->request->getPost('data_staff'));
            $tanggal = trim($this->request->getPost('data_tanggal'));
            $part_no = trim($this->request->getPost('data_material'));
            $part_name = trim($this->request->getPost('data_name'));
            $part_model = trim($this->request->getPost('data_model'));
            $mold_no = trim($this->request->getPost('data_mold'));
            $repair_reason = trim($this->request->getPost('data_repair'));
            $description = trim($this->request->getPost('data_keterangan'));
            $photos = $this->request->getFileMultiple('fupload');

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
                        'required' => "Part No. is required"
                    ]
                ],
                'data_repair' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Reason for repair is required"
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

            if(count($photos) > 1){
                $rules = array_merge([
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
                ]);
            }

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode("<br>", $this->validasi->getErrors());
                log_action($this->module, $aksi, "error", current_url(), "Save failed, failed to make validation");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Save failed, failed to make a validation : " . $error_message);
            }

            // Update header
            $data_header = [
                'dept' => $workshop,
                'report_by' => $staff,
                'report_date' => $tanggal,
                'part_no' => $part_no,
                'part_name' => $part_name,
                'part_model' => $part_model,
                'mold_no' => $mold_no,
                'repair_reason' => $repair_reason,
                'description' => $description,
                'updated_by' => $this->NIK,
            ];

            $update = $this->spkModel->update($id_spk, $data_header);
            if(!$update){
                log_action($this->module, $aksi, "error", current_url(), "Update failed", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Update failed, there was an error during processing you request, please try again later or contact your administrator");
            }

            $get_max_row = $this->detailModel->where('id_spk', $id_spk)->orderBy('urut', 'desc')->limit(1)->first();
            if($get_max_row){
                $baris = $get_max_row->urut + 1;
            }else{
                $baris = 1;
            }

            // Update details
            if(count($photos) > 1){                
                $success_count = 0;
                $error_messages = [];
                $uploadPath = FCPATH . 'uploads/mold_spk';
                    
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
                            'id_spk' => $id_spk,
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
                    }else {
                        $error_messages[] = "Invalid file: " . $photo->getName();
                    }

                    $baris ++;
                }

                $this->db->transComplete();
                if($this->db->transStatus() === false){
                    $this->db->transRollback();
                    log_action($this->module, $aksi, "error", current_url(), "Save failed", '', json_encode([
                        'data' => $this->db->error()
                    ]));

                    return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Save failed due to database transaction error");
                }

                if(!empty($error_messages)){
                    log_action($this->module, $aksi, "warning", current_url(), "Save completed with some errors", implode(", ", $error_messages));
                    return pesan(ResponseInterface::HTTP_OK, "Data saved successfully, but there were some issues with file uploads: " . implode(", ", $error_messages));
                }
            }

            log_action($this->module, $aksi, "success", current_url(), "Data updated successfully");
            return pesan(ResponseInterface::HTTP_OK, "Data udpated successfully");
        } catch(\Exception $e){
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function prevData(){
        $aksi = "Prev Data";
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

            if(!isset($json_data['code'])){
                log_action($this->module, $aksi, "error", current_url(), "SPK no. is missing in JSON input");
                throw new \Exception("Job data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK no. is missing in JSON input");
            }

            $code = $json_data['code'];
            $get_prev_data = $this->spkModel->prevData($code);
            if(!$get_prev_data){
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the fist data");
            }

            $this->db->transComplete();
            if($this->db->transStatus() === false){
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Getting prevoius data failed", '', json_encode([
                    'data' => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to getting previous data due to database transaction error");
            }

            $data = [
                'token' => enkripsi($get_prev_data->id)
            ];

            return pesan(ResponseInterface::HTTP_OK, "Prev data found", $data);
        } catch(\Exception $e){
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function nextData(){
        $aksi = "Next Data";
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

            if(!isset($json_data['code'])){
                log_action($this->module, $aksi, "error", current_url(), "SPK no. is missing in JSON input");
                throw new \Exception("Job data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK no. is missing in JSON input");
            }

            $code = $json_data['code'];
            $get_prev_data = $this->spkModel->nextData($code);
            if(!$get_prev_data){
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the last data");
            }

            $this->db->transComplete();
            if($this->db->transStatus() === false){
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Getting next data failed", '', json_encode([
                    'data' => $this->db->error()
                ]));

                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to getting next data due to database transaction error");
            }

            $data = [
                'token' => enkripsi($get_prev_data->id)
            ];

            return pesan(ResponseInterface::HTTP_OK, "Prev data found", $data);
        } catch(\Exception $e){
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }

    function deleteImage(){
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
                log_action($this->module, $aksi, "error", current_url(), "Job data ID is missing in JSON input");
                throw new \Exception("Job data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Job data ID is missing in JSON input");
            }

            $id_detail = dekripsi($json_data['token']);

            $get = $this->detailModel->where('id', $id_detail)->first();
            if(!$get){
                log_action($this->module, $aksi, "error", current_url(), "Image not found");
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Image data not found");
            }

            $uploadPath = FCPATH . 'uploads/mold_spk/';
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
        $aksi = "Show Image";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

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
                log_action($this->module, $aksi, "error", current_url(), "Job data ID is missing in JSON input");
                throw new \Exception("Job data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Job data ID is missing in JSON input");
            }

            $id_spk = dekripsi($json_data['token']);
            $get_header = $this->spkModel->where('id', $id_spk)->first();
            $get_data = $this->detailModel->where('id_spk', $id_spk)->orderBy('urut', 'asc')->findAll();
            if(!$get_data){
                log_action($this->module, $aksi, "error", current_url(), "Mold SPK image not available");

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Mold SPK image not available");
            }

            $data = [];
            $header = [
                'title' => $get_header->code
            ];

            foreach($get_data as $item){
                $data[] = [
                    'file_name' => base_url().'uploads/mold_spk/' . $item->file_name,
                    'image_file' => $item->file_name
                ];
            }

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_OK)
                ->setJSON([
                    'header' => $header,
                    'data' => $data
                ]);
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
        $filename = "oem_list_". date("Ymd_his") . '.xlsx';
        $headers =  ['SPK No', 'Reported Date', 'Requested Dept', 'Reported By', 'Part No', 'Part Name', 'Part Model', 'Mold No', 'Repair Reason', 'Problem Description'];
        
        $dataCallback = function ($offset, $limit){
            $column = 'code, report_date, nama_dept, nama_karyawan, kode_part, part_name, part_model, mold_no, nama_alasan_repair, description';
            return $this->masterModel->getChunkedData('vw_mold_spk', $offset, $limit, 'code', $column);
        };

        return export_to_excel($filename, $headers, $dataCallback);
    }
}
