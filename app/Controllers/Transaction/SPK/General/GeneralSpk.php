<?php

namespace App\Controllers\Transaction\SPK\General;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\General\GeneralSpkModel;
use App\Models\MasterData\CommonData\Machine\MachineModel;
use App\Models\Master\MasterModel;
use Config\Services;

class GeneralSpk extends BaseController
{
    protected $generalModel;
    protected $mesinModel;
    protected $masterModel;
    protected $validasi;
    protected $enkripsi;
    protected $module;

    public function __construct()
    {
        $this->module = "Equipment SPK";
        $this->generalModel = new GeneralSpkModel();
        $this->mesinModel = new MachineModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
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
}
