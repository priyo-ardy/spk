<?php

namespace App\Controllers\Transaction\SPK\SPK;

use App\Controllers\BaseController;
use App\Controllers\MasterData\CommonData\SubDefect\SubDefect;
use App\Database\Migrations\RepairReason;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\SPK\SPKModel;
use App\Models\Transaction\SPK\SPK\SPKDetailsModel;
use App\Models\DataTable\DataTableModel;
use App\Models\Master\MasterModel;
use App\Models\MasterData\CommonData\Machine\MachineModel;
use App\Models\MasterData\CommonData\Employee\EmployeeModel;
use App\Models\MasterData\CommonData\Leader\LeaderModel;
use App\Models\MasterData\CommonData\Dept\DeptModel;
use App\Models\MasterData\MaterialManagement\Material\MaterialModel;
use App\Models\MasterData\CommonData\Defect\DefectModel;
use App\Models\MasterData\CommonData\SubDefect\SubDefectModel;
use App\Models\MasterData\CommonData\ProblemPosition\ProblemPositionModel;
use App\Models\MasterData\CommonData\RepairReason\RepairReasonModel;
use App\Models\MasterData\CommonData\Lokasi\LokasiModel;
use App\Models\MasterData\CommonData\Supplier\SupplierModel;
use App\Models\Transaction\Identification\IdentificationModel;
use CodeIgniter\HTTP\Response;
use Config\Services;
use Config\Database;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Beta;

class SPK extends BaseController
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
    protected $defectModel;
    protected $positionModel;
    protected $subDefectModel;
    protected $lokasiModel;
    protected $machineModel;
    protected $supplierModel;
    protected $validasi;
    protected $enkripsi;
    protected $identifikasiModel;

    public function __construct()
    {
        $this->module = "SPK";
        $this->spkModel = new SPKModel();
        $this->detailModel = new SPKDetailsModel();
        $this->materialModel = new MaterialModel();
        $this->deptModel = new DeptModel();
        $this->karyawanModel = new EmployeeModel();
        $this->leaderModel = new LeaderModel();
        $this->masterModel = new MasterModel();
        $this->repairModel = new RepairReasonModel();
        $this->defectModel = new DefectModel();
        $this->subDefectModel = new SubDefectModel();
        $this->positionModel = new ProblemPositionModel();
        $this->lokasiModel = new LokasiModel();
        $this->machineModel = new MachineModel();
        $this->supplierModel = new SupplierModel();
        $this->identifikasiModel = new IdentificationModel();

        $this->db = Database::connect();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        switch (session()->get('level')) {
            case '0': //Super Administrator
            case '1': //Administrator
                $table = 'vw_t_spk';
                break;
            case '2':
                $table = '';
                break;
            case '3':
                $table = 'vw_spk_mold_engineer';
                break;
            case '4':
                $table = '';
                break;
            case '5':
                $table = '';
                break;
        }

        $column_order = ['code'];
        $column_search = ['code'];
        $order = array('tgl_lapor' => 'desc');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    function loadTable()
    {
        $aksi = 'load table';
        log_action($this->module, $aksi, "generate", current_url(), 'Generate list of SPK transaction');

        $lists = $this->dataTable->get_datatables();
        $data = [];

        foreach ($lists as $item) {
            $row = [];

            $row[] = enkripsi($item->id);
            $row[] = $item->nama_kategori;
            $row[] = '
                    <a href="' . base_url() . 'spk/show/' . enkripsi($item->id) . '" class="link-underline-opacity-100-hover fw-bolder" onlick="loading()">' . $item->code . '</a>
                ';
            $row[] = $item->nama_dokumen_status;
            $row[] = $item->nama_prioritas;
            $row[] = $item->nama_flow;
            $row[] = $item->tgl_lapor;
            $row[] = $item->nama_lokasi;
            $row[] = $item->nama_dept;
            $row[] = "$item->NIK - $item->nama_karyawan";
            $row[] = ($item->kategori == '1') ? $item->kode_material : $item->nomor_mesin;
            $row[] = $item->nama_material;
            $row[] = $item->model_material;
            $row[] = $item->nomor_mesin;
            $row[] = $item->nama_tipe_equipment;
            $row[] = $item->nama_alasan_repair;
            $row[] = strip_tags($item->deskripsi);
            $row[] = '
                <a href="#" class="link-underline-opacity-100-hover" onclick="showImage(`' . enkripsi($item->id) . '`)">Show Image</a>
            ';
            $row[] = $item->nama_defect;
            $row[] = $item->nama_sub_defect;
            $row[] = $item->nama_berulang;
            $row[] = $item->nama_leader;
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
        $aksi = "Open";
        log_action($this->module, $aksi, "info", current_url(), "Opening list of SPK page");

        $data = [
            'title' => 'List of SPK',
            'data_level' => session('user_level'),
            'footer' => [
                '<script src="' . base_url() . 'js/Transaction/SPK/SPK/spk.js' . '"></script>'
            ]
        ];

        return view('Transaction/SPK/SPK/index', $data);
    }

    function add()
    {
        $aksi = "Open";
        log_action($this->module, $aksi, "info", current_url(), "Opening create new SPK page");

        $data = [
            'title' => 'Create New SPK',
            'location_list' => $this->lokasiModel->generateList(),
            'dept_list' => $this->deptModel->generateList(),
            'emp_list' => $this->karyawanModel->generateList(),
            'leader_list' => $this->leaderModel->generateList(),
            'defect_list' => $this->defectModel->generateList(),
            'reason_list' => $this->repairModel->generateList(),
            'supplier_list' => $this->supplierModel->generateList(),
            'footer' => [
                '<script src="' . base_url() . 'js/Transaction/SPK/SPK/add.js' . '"></script>'
            ]
        ];

        return view('Transaction/SPK/SPK/add', $data);
    }

    function saveData()
    {
        $aksi =  "save SPK";
        log_action($this->module, $aksi, "info", current_url(), "Preparing to saving new SPK transcation");

        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        $this->db->transStart();

        try {
            $id_header = generate_uuid();
            $doc_type = trim($this->request->getPost('doc_type'));
            $lokasi = trim($this->request->getPost('data_lokasi'));
            $dept = trim($this->request->getPost('data_dept'));
            $pelapor = trim($this->request->getPost('data_pelapor'));
            $tanggal = trim($this->request->getPost('data_tanggal'));
            $material = trim($this->request->getPost('data_material'));
            $model = trim($this->request->getPost('data_model'));
            $mold_no = trim($this->request->getPost('data_mold'));
            $tipe_equipment = trim($this->request->getPost('tipe_equipment'));
            $leader = trim($this->request->getPost('data_leader'));
            $defect = trim($this->request->getPost('data_defect'));
            $sub_defect = trim($this->request->getPost('data_sub_defect'));
            $berulang = trim($this->request->getPost('data_berulang'));
            // $posisi = trim($this->request->getPost('data_posisi'));
            $repair = trim($this->request->getPost('data_repair'));
            $images = $this->request->getFileMultiple('data_image');
            $keterangan = strip_tags(trim($this->request->getPost('data_keterangan')));
            $lokasi_repair = trim($this->request->getPost('lokasi_repair'));
            $supplier = trim($this->request->getPost('data_supplier'));
            $jig_status = trim($this->request->getPost('data_jig'));
            $date = date("Ymd", strtotime($tanggal));
            $error_message = [];
            $success_count = 0;
            $baris = 1;
            $prefix = '';
            $mold = '';

            switch ($doc_type) {
                case 1:
                    $prefix = 'SLMMJ';
                    $mold = $mold_no;
                    break;
                case 2:
                    $prefix = 'SLMMA';
                    $mold = str_replace('#', '', $mold_no);
                    break;
            }

            $uploadPath = FCPATH . '/uploads/spk/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $rules = [
                'doc_type' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Document type is required"
                    ]
                ],
                'data_lokasi' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Equipment/Machine location is required"
                    ]
                ],
                'data_dept' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Requested dept is required"
                    ]
                ],
                'data_pelapor' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Requested by is required'
                    ]
                ],
                'data_tanggal' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => "Requested date is required",
                        'valid_date' => "Request date must have a valid date format"
                    ]
                ],
                'data_material' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Material is required"
                    ]
                ],
                'data_leader' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Team leader/supervisor is required"
                    ]
                ],
                'data_defect' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => "Problem defect is required"
                    ]
                ],
                'data_sub_defect' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Problem sub defect is required'
                    ]
                ],
                'data_berulang' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Repeat problem is required"
                    ]
                ],
                'data_repair' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Repair reason is required"
                    ]
                ],
                'data_image' => [
                    // 'rules' => 'uploaded[data_image]|max_size[data_image,51200]|is_image[data_image]|mime_in[data_image,image/jpg,image/jpeg,image/png]|ext_in[data_image,jpg,jpeg,png]',
                    'rules' => 'max_size[data_image,51200]|is_image[data_image]|mime_in[data_image,image/jpg,image/jpeg,image/png]|ext_in[data_image,jpg,jpeg,png]',
                    'errors' => [
                        'uploaded' => 'Problem position photo is required',
                        'max_size' => 'Problem position photo maximum size is 50MB',
                        'is_image' => 'Problem position photo must image file type (JPG/JPEG/PNG)',
                        'mime_in' => 'Problem position photo file format must JPG, JPEG atau PNG',
                        'ext_in' => 'Problem position photo file extension mus .jpg, .jpeg atau .png'
                    ]
                ]
            ];

            if ($doc_type !== '1') {
                $rules = array_merge($rules, [
                    'tipe_equipment' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => "Equipment type is required"
                        ]
                    ]
                ]);
            }

            if (!$this->validasi->setRules($rules)->withRequest($this->request)->run()) {
                $error_fields = $this->validasi->getErrors();
                $error_message = implode("<br>", array_map(function ($field, $msg) {
                    return "$field: $msg";
                }, array_keys($error_fields), $error_fields));
                log_action($this->module, $aksi, "error", current_url(), "Validation failed", '', json_encode([
                    'data' => $error_fields
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed! <br>" . $error_message);
            }

            $data_header = [
                'id' => $id_header,
                'kategori' => $doc_type,
                'code' => $this->spkModel->generateDocNo($prefix, $date, $mold),
                'lokasi' => $lokasi,
                'dept' => $dept,
                'pelapor' => $pelapor,
                'tgl_lapor' => $tanggal,
                'material' => $material,
                'material_name' => '',
                'material_model' => $model,
                'nomor_mesin' => $mold_no,
                'leader' => $leader,
                'defect' => $defect,
                'sub_defect' => $sub_defect,
                'berulang' => $berulang,
                'tipe_equipment' => $tipe_equipment,
                'alasan_repair' => $repair,
                'lokasi_repair' => $lokasi_repair,
                'supplier' => $supplier,
                'jig_status' => $jig_status,
                'deskripsi' => $keterangan,
                'dokumen_status' => 0,
                'created_by' => $this->NIK,
            ];

            $insert_header = $this->spkModel->insert($data_header);
            if (!$insert_header) {
                log_action($this->module, $aksi, "error", current_url(), "Save failed, failed to save a new SPK transaction", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                throw new \Exception("Save failed, there was an error during processing your request <br>" . json_encode($this->spkModel->errors()));
                // throw new \Exception(json_encode($this->spkModel->errors()));
            }

            if ($images) {
                foreach ($images as $image) {
                    if ($image->isValid() && !$image->hasMoved()) {
                        $fileName = $data_header['code'] . "-$baris." . $image->getExtension();
                        $image->move($uploadPath, $fileName, true);
                        $data_details = [
                            'id' => generate_uuid(),
                            'urut' => $baris,
                            'id_spk' => $id_header,
                            'nama_file' => $fileName,
                            'ukuran_file' => $image->getSize(),
                            'created_by' => $this->NIK,
                        ];

                        $insert_details = $this->detailModel->insert($data_details);
                        if (!$insert_details) {
                            $error_message[] = "Failed to insert image $fileName on row $baris. Error: " . json_encode($this->detailModel->errors());

                            if (file_exists($uploadPath . $fileName)) {
                                unlink($uploadPath . $fileName);
                            }
                        } else {
                            $success_count++;
                            $baris++;
                        }
                    } else {
                        $error_message[] = "Invalid file: " . $image->getName();
                    }
                }
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Save failed", '', json_encode([
                    'data' => $this->db->error()
                ]));

                // throw new \Exception("Save failed, there was an error during processing your request");
                throw new \Exception(json_encode($error_message));
            }

            if (!empty($error_message)) {
                log_action($this->module, $aksi, "error", current_url(), "Successfully saved SPK data with some errors, here are the details :<br>" . implode(", ", $error_message), '', json_encode([
                    'data' => $error_message
                ]));

                return pesan(ResponseInterface::HTTP_OK, "Successfully saved SPK data with some error with details: <br>" . implode("<br>", $error_message), ['token' => enkripsi($id_header)]);
            }

            log_action($this->module, $aksi, "success", current_url(), "Successfully saved SPK data with document No. <strong>" . $data_header['code'] . "</strong>", '', json_encode([
                'data' => [
                    'header' => $data_header,
                    'details' => $data_details
                ]
            ]));

            return pesan(ResponseInterface::HTTP_OK, "Successfully saved SPK data with document No. : " . $data_header['code'], [
                'token' => enkripsi($id_header)
            ]);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured : <br>" . $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    function showData($token)
    {
        $id_spk = dekripsi($token);
        $data_header = $this->spkModel->where('id', $id_spk)->first();
        $data_details = $this->detailModel->where('id_spk', $id_spk)->orderBy('urut', 'asc')->findAll();

        switch ($data_header->kategori) {
            case 1:
                $lists_material = $this->materialModel->generatePartList();
                break;
            case 2:
                $lists_material = $this->machineModel->generateMachineList();
                break;
            case 3:
                break;
            case 4:
                break;
        }

        switch ($data_header->dokumen_status) {
            case 0:
                $nama_status = "Created";
                break;
            case 1:
                $nama_status = "Under Review";
                break;
            case 2:
                $nama_status = "Approved";
                break;
            case 3:
                $nama_status = " On progress in Mold";
                break;
            case 4:
                $nama_status = " On progress in Planner";
                break;
            case 5:
                $nama_status = "On progress in Quality ";
                break;
            case 6:
                $nama_status = 'Hold';
                break;
            case 7:
                $nama_status = 'Reject';
                break;
            case 8:
                $nama_status = 'Close';
                break;
        }

        switch ($data_header->flow_status) {
            case 0:
                $nama_flow_status = "Un-Confirmed";
                break;
            case 1:
                $nama_flow_status = "Confirmed by Mold Engineer";
                break;
            case 2:
                $nama_flow_status = "Confirmed by Planner";
                break;
            case 3:
                $nama_flow_status = "Confirmed by ME";
                break;
            case 4:
                $nama_flow_status = "Finished by Mold Engineer";
                break;
            case 5:
                $nama_flow_status = "Finished by ME";
                break;
            case 6:
                $nama_flow_status = "Confirmed by Quality";
                break;
            case 7:
                $nama_flow_status = "Close";
                break;
        }

        $defect = $this->defectModel->where('kategori', $data_header->kategori)->findAll();
        $sub_defect = $this->subDefectModel->getListByDefect($data_header->defect);

        $aksi = "Open";
        log_action($this->module, $aksi, "info", current_url(), "Opening SPK details page");

        $data = [
            'token' => enkripsi($data_header->id),
            'title' => 'SPK Details',
            'code' => $data_header->code,
            'header' => $data_header,
            'details' => $data_details,
            'defect_list' => $defect,
            'sub_defect' => $sub_defect,
            'status' => $data_header->dokumen_status,
            'nama_status' => $nama_status . "&ensp;(" . $nama_flow_status . ")",
            'nama_flow_status' => $nama_flow_status,
            'material_list' => $lists_material,
            'location_list' => $this->lokasiModel->generateList(),
            'dept_list' => $this->deptModel->generateList(),
            'emp_list' => $this->karyawanModel->generateList(),
            'leader_list' => $this->leaderModel->generateList(),
            'reason_list' => $this->repairModel->generateList(),
            'supplier_list' => $this->supplierModel->generateList(),
            'footer' => [
                '<script src="' . base_url() . 'js/Transaction/SPK/SPK/edit.js' . '"></script>'
            ]
        ];

        return view('Transaction/SPK/SPK/edit', $data);
    }

    function updateData()
    {
        $aksi = "update";

        log_action($this->module, $aksi, "info", current_url(), "preparing to update SPK data");

        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Invalid request method");
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Invalid request method");
        }

        $this->db->transStart();

        try {
            $token = trim($this->request->getPost('data_token'));
            $id_spk = dekripsi($token);
            $code = trim($this->request->getPost('data_code'));
            $doc_type = trim($this->request->getPost('doc_type'));
            $lokasi = trim($this->request->getPost('data_lokasi'));
            $dept = trim($this->request->getPost('data_dept'));
            $pelapor = trim($this->request->getPost('data_pelapor'));
            $tanggal = trim($this->request->getPost('data_tanggal'));
            $material = trim($this->request->getPost('data_material'));
            $model = trim($this->request->getPost('data_model'));
            $mold_no = trim($this->request->getPost('data_mold'));
            $tipe_equipment = trim($this->request->getPost('tipe_equipment'));
            $leader = trim($this->request->getPost('data_leader'));
            $defect = trim($this->request->getPost('data_defect'));
            $sub_defect = trim($this->request->getPost('data_sub_defect'));
            $berulang = trim($this->request->getPost('data_berulang'));
            $repair = trim($this->request->getPost('data_repair'));
            $images = $this->request->getFileMultiple('data_image');
            $keterangan = strip_tags(trim($this->request->getPost('data_keterangan')));
            $lokasi_repair = trim($this->request->getPost('lokasi_repair'));
            $supplier = trim($this->request->getPost('data_supplier'));
            $jig_status = trim($this->request->getPost('data_jig'));
            $date = date("Ymd", strtotime($tanggal));
            $error_message = [];
            $success_count = 0;
            $baris = $this->detailModel->getLastRow($id_spk);

            $uploadPath = FCPATH . '/uploads/spk/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 055, true);
            }

            $rules = [
                'doc_type' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Document type is required"
                    ]
                ],
                'data_lokasi' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Equipment/Machine location is required"
                    ]
                ],
                'data_dept' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Requested dept is required"
                    ]
                ],
                'data_pelapor' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Requested by is required'
                    ]
                ],
                'data_tanggal' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => "Requested date is required",
                        'valid_date' => "Request date must have a valid date format"
                    ]
                ],
                'data_material' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Material is required"
                    ]
                ],
                'data_leader' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Team leader/supervisor is required"
                    ]
                ],
                'data_defect' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => "Problem defect is required"
                    ]
                ],
                'data_sub_defect' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Problem sub defect is required'
                    ]
                ],
                'data_berulang' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Repeat problem is required"
                    ]
                ],
                'data_repair' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => "Repair reason is required"
                    ]
                ],
            ];

            $uploadedImages = $this->request->getFileMultiple('data_image');
            $hasValidImage = false;
            if (!empty($uploadedImages)) {
                foreach ($uploadedImages as $img) {
                    if ($img && $img->isValid() && $img->getSize() > 0) {
                        $hasValidImage = true;
                        break;
                    }
                }
            }

            if ($hasValidImage) {
                $rules = array_merge($rules, [
                    'data_image' => [
                        'rules' => 'uploaded[data_image]|max_size[data_image,51200]|is_image[data_image]|mime_in[data_image,image/jpg,image/jpeg,image/png]|ext_in[data_image,jpg,jpeg,png]',
                        'errors' => [
                            'uploaded' => 'Problem position photo is required',
                            'max_size' => 'Problem position photo maximum size is 50MB',
                            'is_image' => 'Problem position photo must image file type (JPG/JPEG/PNG)',
                            'mime_in' => 'Problem position photo file format must JPG, JPEG atau PNG',
                            'ext_in' => 'Problem position photo file extension mus .jpg, .jpeg atau .png'
                        ]
                    ]
                ]);
            }

            if ($doc_type !== '1') {
                $rules = array_merge($rules, [
                    'tipe_equipment' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => "Equipment type is required"
                        ]
                    ]
                ]);
            }

            if (!$this->validasi->setRules($rules)->withRequest($this->request)->run()) {
                $error_fields = $this->validasi->getErrors();
                $error_message = implode("<br>", array_map(function ($field, $msg) {
                    return "$field: $msg";
                }, array_keys($error_fields), $error_fields));
                log_action($this->module, $aksi, "error", current_url(), "Validation failed", '', json_encode([
                    'data' => $error_fields
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Validation failed! <br>" . $error_message);
            }

            $data_header = [
                'kategori' => $doc_type,
                'lokasi' => $lokasi,
                'dept' => $dept,
                'pelapor' => $pelapor,
                'tgl_lapor' => $tanggal,
                'material' => $material,
                'material_name' => '',
                'material_model' => $model,
                'nomor_mesin' => $mold_no,
                'leader' => $leader,
                'defect' => $defect,
                'sub_defect' => $sub_defect,
                'berulang' => $berulang,
                'tipe_equipment' => $tipe_equipment,
                'alasan_repair' => $repair,
                'lokasi_repair' => $lokasi_repair,
                'supplier' => $supplier,
                'jig_status' => $jig_status,
                'deskripsi' => $keterangan,
                'dokumen_status' => 0,
                'updated_by' => $this->NIK,
            ];

            $update = $this->spkModel->update($id_spk, $data_header);
            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Update failed", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                throw new \Exception("Update failed, there was an error during processing updating data");
            }
            $details = [];


            if ($hasValidImage) {
                foreach ($images as $image) {
                    if ($image->isValid() && !$image->hasMoved()) {
                        $fileName = "$code-" . "$baris." . $image->getExtension();
                        $image->move($uploadPath, $fileName, true);
                        $data_details = [
                            'id' => generate_uuid(),
                            'urut' => $baris,
                            'id_spk' => $id_spk,
                            'nama_file' => $fileName,
                            'ukuran_file' => $image->getSize(),
                            'created_by' => $this->NIK,
                        ];

                        $insert_details = $this->detailModel->insert($data_details);
                        if (!$insert_details) {
                            $error_message[] = "Failed to insert image $fileName on row $baris. Error: " . json_encode($this->detailModel->errors());

                            if (file_exists($uploadPath . $fileName)) {
                                unlink($uploadPath . $fileName);
                            }
                        } else {
                            $success_count++;
                            $details[] = $data_details;
                            $baris++;
                        }
                    } else {
                        $error_message[] = "Invalid file: " . $image->getName();
                    }
                }
            }

            log_action($this->module, $aksi, "success", current_url(), "Update success", '', json_encode([
                'header' => $data_header,
                'details' => $details
            ]));

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Update failed", '', json_encode([
                    'data' => $this->db->error()
                ]));

                // throw new \Exception("Save failed, there was an error during processing your request");
                throw new \Exception(json_encode($error_message));
            }

            if (!empty($error_message)) {
                log_action($this->module, $aksi, "error", current_url(), "Successfully updated SPK data with some errors, here are the details :<br>" . implode(", ", $error_message), '', json_encode([
                    'data' => $error_message
                ]));

                return pesan(ResponseInterface::HTTP_OK, "Successfully updated SPK data with some error with details: <br>" . implode("<br>", $error_message));
            }
            return pesan(ResponseInterface::HTTP_OK, "Successfully updated SPK data with document No. : <strong>$code</strong>");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    function deleteImage()
    {
        $aksi = "delete image";

        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "SPK token is missing in JSON input");
                throw new \Exception("SPK No. is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
            }

            $token = $json_data['token'];
            $id_detail = dekripsi($token);

            $get_data = $this->detailModel->where('id', $id_detail)->first();
            if (!$get_data) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Image data not found in the database");
            }

            $uploadPath = FCPATH . 'uploads/spk/' . $get_data->nama_file;

            if (!unlink($uploadPath)) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Image directory not found");
            }

            $delete = $this->detailModel->delete($id_detail, true);
            if (!$delete) {
                log_action($this->module, $aksi, "error", current_url(), "Failed to delete image data", '', json_encode([
                    'data' => $this->detailModel->errors()
                ]));

                throw new \Exception("Failed to delete the image data");
            }

            log_action($this->module, $aksi, "success", current_url(), "Image deleted successfully", '', json_encode([
                'data' => $id_detail
            ]));

            return pesan(ResponseInterface::HTTP_OK, "Delete success");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    function submitData()
    {
        $aksi = "submit spk";

        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "SPK token is missing in JSON input");
                throw new \Exception("SPK No. is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
            }

            $token = $json_data['token'];
            $id_spk = dekripsi($token);

            $data = [
                'dokumen_status' => '1',
                'updated_by' => $this->NIK
            ];

            $update = $this->spkModel->update($id_spk, $data);
            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Submit SPK is failed", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                throw new \Exception("Failed to submit SPK data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Submit successfully");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trance' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    function undoData()
    {
        $aksi = "undo spk";

        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "SPK token is missing in JSON input");
                throw new \Exception("SPK No. is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
            }

            $token = $json_data['token'];
            $id_spk = dekripsi($token);

            $data = [
                'dokumen_status' => '0',
                'updated_by' => $this->NIK
            ];

            $update = $this->spkModel->update($id_spk, $data);
            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Undo SPK is failed", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                throw new \Exception("Failed to undo SPK data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Undo successfully");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trance' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    function approveData()
    {
        $aksi = "approve spk";

        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "SPK token is missing in JSON input");
                throw new \Exception("SPK No. is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
            }

            $token = $json_data['token'];
            $id_spk = dekripsi($token);

            $data = [
                'dokumen_status' => '2',
                'updated_by' => $this->NIK
            ];

            $update = $this->spkModel->update($id_spk, $data);
            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Approve SPK is failed", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                throw new \Exception("Failed to approve SPK data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Approve successfully");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trance' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    function unApproveData()
    {
        $aksi = "approve spk";

        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "SPK token is missing in JSON input");
                throw new \Exception("SPK No. is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
            }

            $token = $json_data['token'];
            $id_spk = dekripsi($token);

            $data = [
                'dokumen_status' => '3',
                'updated_by' => $this->NIK
            ];

            $get_data = $this->spkModel->where('id', $id_spk)->first();
            if ($get_data->flow_status !== '0') {
                return pesan(ResponseInterface::HTTP_FOUND, "Failed to un-approve SPK data, the SPK data already processed");
            }

            $update = $this->spkModel->update($id_spk, $data);
            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Un-Approve SPK is failed", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                throw new \Exception("Failed to un-approve SPK data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Un-Approve successfully");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trance' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    function pevData()
    {
        $aksi = "prev";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        log_action($this->module, $aksi, "info", current_url(), "Preparing to getting previous data");
        try {
            $json_data = $this->request->getJSON('true');

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['code'])) {
                log_action($this->module, $aksi, "error", current_url(), "SPK no. is missing in JSON input");
                throw new \Exception("SPK No. is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK no. is missing in JSON input");
            }

            $code = $json_data['code'];

            $get_prev_data = $this->spkModel->getPrevData($code);
            if (empty($get_prev_data)) {
                log_action($this->module, $aksi, "error", current_url(), "You are in the first data", '', json_encode([
                    'data' => $code
                ]));

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the first data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Data found", [
                'token' => enkripsi($get_prev_data->id)
            ]);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured : " . $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(500, "Unexpected error occured : " . $e->getMessage());
        }
    }

    function nextData()
    {
        $aksi = "next";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        log_action($this->module, $aksi, "info", current_url(), "Preparing to getting next data");
        try {
            $json_data = $this->request->getJSON('true');

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['code'])) {
                log_action($this->module, $aksi, "error", current_url(), "SPK no. is missing in JSON input");
                throw new \Exception("SPK No. is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK no. is missing in JSON input");
            }

            $code = $json_data['code'];

            $get_prev_data = $this->spkModel->getNextData($code);
            if (empty($get_prev_data)) {
                log_action($this->module, $aksi, "error", current_url(), "You are in the last data", '', json_encode([
                    'data' => $code
                ]));

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "You are in the last data");
            }

            return pesan(ResponseInterface::HTTP_OK, "Data found", [
                'token' => enkripsi($get_prev_data->id)
            ]);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured : " . $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(500, "Unexpected error occured : " . $e->getMessage());
        }
    }

    function exportData()
    {
        $aksi = "export";
        log_action($this->module, $aksi, "info", current_url(), "Preparing to export SPK data");

        try {
            $fileName = "SPK_list_" . date("Y-m-d H:i:s") . ".xlsx";
            $headers = [
                'SPK Category',
                'SPK No',
                'Doc. Status',
                'Critical Level',
                'Status',
                'Reported Date',
                'Location',
                'Requested Dept.',
                'Reported By',
                'Part/Machine No.',
                'Part/Machine Name',
                'Part/Machine Model',
                'Mold/Jig No.',
                'Equipment Type',
                'Repair Reason',
                'Problem Description',
                'Defect',
                'Sub Defect',
                'Repeat Problem',
                'Jig Status',
                'Repair Location',
                'Supplier',
                'Team Leader/Supervisor',
            ];

            $dataCallBack = function ($offset, $limit) {
                $column = '
                        nama_kategori,
                        code,
                        nama_dokumen_status,
                        nama_prioritas,
                        nama_flow,
                        tgl_lapor,
                        nama_lokasi,
                        nama_dept,
                        nama_karyawan,
                        kode_material,
                        nama_material,
                        model_material,
                        nomor_mesin,
                        nama_tipe_equipment,
                        nama_alasan_repair,
                        deskripsi,
                        nama_defect,
                        nama_sub_defect,
                        nama_berulang,
                        nama_jig_status,
                        nama_lokasi_repair,
                        nama_supplier,
                        nama_leader
                    ';
                return $this->masterModel->getChunkedData('vw_t_spk', $offset, $limit, 'tgl_lapor', $column);
            };

            return export_to_excel($fileName, $headers, $dataCallBack);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured : " . $e->getMessage(), '', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured : " . $e->getMessage());
        }
    }

    function showImage($token)
    {
        $aksi = "show image";
        log_action($this->module, $aksi, "info", current_url(), "Preparing to show transaction image", '', json_encode([
            'token' => $token
        ]));

        try {
            $id_spk = dekripsi($token);
            $get_header_data = $this->spkModel->where('id', $id_spk)->first();
            $get_image_data = $this->detailModel->where('id_spk', $id_spk)->orderBy('urut', 'asc')->findAll();
            if (!$get_image_data || empty($get_image_data)) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "No image available");
            }

            $data = [];
            foreach ($get_image_data as $list) {
                $data[] = [
                    'file_name' => base_url() . 'uploads/spk/' . $list->nama_file
                ];
            }

            return pesan(ResponseInterface::HTTP_OK, "Image available", [
                'header' => $get_header_data->code,
                'details' => $data
            ]);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured : " . $e->getMessage());
        }
    }

    function getTargetDocument()
    {
        if ($this->request->getMethod() !== 'POST') {
            if ($this->request->isAJAX()) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Invalid request method");
            } else {
                return view('errors/html/error_405');
            }
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if (!isset($json_data['data'])) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Data is missing in JSON input");
            }

            $token = $json_data['data'];
            $id_spk = dekripsi($token);

            $get_identification = $this->identifikasiModel->getIdentificationBySpk($id_spk);
            if (!$get_identification) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "No target document available");
            }

            return pesan(ResponseInterface::HTTP_OK, "Target document available", [
                'token' => enkripsi($get_identification->id)
            ]);
        } catch (\Exception $e) {
            log_action($this->module, 'Target Document', "error", current_url(), "Unexpected error occured : " . $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured : " . $e->getMessage());
        }
    }

    function deleteData()
    {
        $json_data = $this->request->getJSON(true);

        if (!is_array($json_data)) {
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
        }

        if (!isset($json_data['token'])) {
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
        }

        $token = $json_data['token'];
        $error = [];
        $success = [];

        if (session()->get('level') > 2) {
            return pesan(ResponseInterface::HTTP_UNAUTHORIZED, "You don't have permission to delete SPK data");
        }

        if (count($token) > 0) {
            for ($i = 0; $i < count($token); $i++) {
                $id = dekripsi($token[$i]);

                $cek_identifikasi = $this->identifikasiModel->getIdentificationBySpk($id);
                if ($cek_identifikasi) {
                    $error[] = [
                        "Failed to delete SPK document with no. <strong>$cek_identifikasi->code</strong>, the SPK document already associated with identification document"
                    ];
                    log_action(
                        $this->module,
                        'delete',
                        'error',
                        current_url(),
                        "Failed to delete SPK data with SPK No. $cek_identifikasi->code",
                    );
                    continue;
                }

                $this->db->transStart();
                $this->spkModel->delete($id);
                $getData = $this->spkModel->getDataById($id);
                $this->db->transComplete();

                if ($this->db->transStatus() === false) {
                    $error[] = "Internal server error, Failed to delete SPK data with SPK No. <strong>>$getData->code</strong";
                    log_action(
                        $this->module,
                        'delete',
                        'error',
                        current_url(),
                        "Failed to delete SPK data with SPK No. $getData->code",
                    );
                } else {
                    $success[] = "Deleted ID: $id";
                }
            }

            if (count($error) > 0) {
                return pesan(ResponseInterface::HTTP_OK, "SPK data deleted with some error ", $error);
            }

            return pesan(ResponseInterface::HTTP_OK, "Successfully deleted SPK data");
        } else {
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, "No data submitted");
        }
    }
}
