<?php

namespace App\Controllers\Transaction\Identification;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\Identification\IdentificationModel;
use App\Models\Transaction\SPK\SPK\SPKModel;
use App\Models\MasterData\CommonData\Dept\DeptModel;
use App\Models\MasterData\CommonData\Karyawan\KaryawanModel;
use App\Models\MasterData\MaterialManagement\Material\MaterialModel;
use App\Models\MasterData\MaterialManagement\EquipmentType\EquipmentTypeModel;
use App\Models\MasterData\CommonData\RepairReason\RepairReasonModel;
use App\Models\MasterData\CommonData\Defect\DefectModel;
use App\Models\MasterData\CommonData\SubDefect\SubDefectModel;
use App\Models\MasterData\CommonData\ProblemCategory\ProblemCategoryModel;
use App\Models\MasterData\CommonData\Supplier\SupplierModel;
use App\Models\MasterData\CommonData\Leader\LeaderModel;
use App\Models\MasterData\CommonData\ProblemPosition\ProblemPositionModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Database;
use Config\Services;

class Identification extends BaseController
{
    protected $module;
    protected $identifikasiModel;
    protected $spkModel;
    protected $deptModel;
    protected $karyawanModel;
    protected $materialModel;
    protected $equipmentTypeModel;
    protected $repairModel;
    protected $defectModel;
    protected $subDefectModel;
    protected $problemModel;
    protected $supplierModel;
    protected $leaderModel;
    protected $posisiModel;
    protected $db;
    protected $validasi;

    public function __construct()
    {
        $this->module = "Identification";
        $this->identifikasiModel = new IdentificationModel();
        $this->spkModel = new SPKModel();
        $this->deptModel = new DeptModel();
        $this->karyawanModel = new KaryawanModel();
        $this->materialModel = new MaterialModel();
        $this->equipmentTypeModel = new EquipmentTypeModel();
        $this->repairModel = new RepairReasonModel();
        $this->defectModel = new DefectModel();
        $this->subDefectModel = new SubDefectModel();
        $this->problemModel = new ProblemCategoryModel();
        $this->supplierModel = new SupplierModel();
        $this->leaderModel = new LeaderModel();
        $this->posisiModel = new ProblemPositionModel();
        $this->db = Database::connect();
        $this->validasi = Services::validation();
    }

    public function index()
    {
        $data = [
            'title' => "SPK Identification List",
            'footer' => []
        ];

        return view('Transaction/Identification/index', $data);
    }

    function generateFromSpk()
    {
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, "generate from spk", "error", current_url(), "Request method not allowed");

            if ($this->request->isAJAX()) {
                return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
            } else {
                redirect()->back()->with(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
            }
        }

        try {
            $spk = $this->request->getPost('spk');
            if (empty($spk)) {
                throw new \Exception("No SPK data was submitted");
                return;
            }

            $id_spk = array_map(fn($value) => dekripsi($value), $spk);

            $data = [];
            $qualified_document = [];
            $unqualified_document = [];
            $data_token = [];

            // Cek apakah SPK sudah di approve, bukan draft, bukan hold, bukan reject dan bukan close
            $status_spk = $this->spkModel->cekDokumenStatus($id_spk);
            if (!$status_spk) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "SPK data not found");
            }

            foreach ($status_spk as $row) {
                if ($row->dokumen_status     == '2') {
                    if ($row->identifikasi == '1') {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> already generated identification data, failed to generate identification data",
                        ];
                    } else {
                        $qualified_document[] = $row->id;
                    }
                } else {
                    if ($row->dokumen_status == 0) {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">created</span>, failed to generate identification data",
                        ];
                    } elseif ($row->dokumen_status == '1') {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">submitted</span>, failed to generate identification data",
                        ];
                    } else if ($row->dokumen_status == '6') {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">hold</span>, failed to generate identification data",
                        ];
                    } else if ($row->dokumen_status == '7') {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">rejected</span>, failed to generate identification data",
                        ];
                    } else if ($row->dokumen_status == '8') {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">closed</span>, failed to generate identification data",
                        ];
                    }
                }
            }

            // Kembalikan error jika terdapat dokummen yang statusnya bukan approved
            if (count($unqualified_document) > 0) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Failed to generate identification data", $unqualified_document);
            }

            // Generate data
            for ($i = 0; $i < count($qualified_document); $i++) {
                $id_spk = $qualified_document[$i];
                $id = generate_uuid();

                $data[] = [
                    'id' => $id,
                    'id_spk' => $id_spk,
                    'tanggal' => date('Y-m-d'),
                    'created_by' => session('user_name')
                ];

                $data_token[] = [
                    'token' => enkripsi($id)
                ];
            }

            $this->db->transStart();

            $update_spk = $this->spkModel->updateIdentifikasi($qualified_document); //Update field identifikasi set to 1
            $insert = $this->identifikasiModel->insertBatch($data); //Insert identifikasi data

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();

                log_action($this->module, "generate from spk", "error", current_url(), "Transaction failed", '', json_encode($this->spkModel->errors()));

                throw new \Exception("Failed to generate identification document data");
            }

            $data_update = [];

            log_action($this->module, "generate from spk", "info", current_url(), "Success generate identification document data");

            return pesan(ResponseInterface::HTTP_OK, "Success generate identification document data", $data_token);
        } catch (\Exception $e) {
            log_action($this->module, "generate from spk", "error", current_url(), $e->getMessage());
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, $e->getMessage() . $e->getFile() . $e->getLine());
        }
    }

    function showData($token)
    {
        $id_identification = dekripsi($token);
        // echo $id_identification;
        $getData = $this->identifikasiModel->getIdentificationById($id_identification);
        if (!$getData) {
            throw PageNotFoundException::forPageNotFound('SPK Identification data not found');
        }

        $data = [
            'page_title' => 'SPK Identification View',
            'title' => 'SPK Identification View',
            'dept' => $this->deptModel->generateList(),
            'karyawan' => $this->karyawanModel->generateList(),
            'material' => $this->materialModel->getMaterialByCategory($getData->kategori),
            'repair' => $this->repairModel->generateList(),
            'defect' => $this->defectModel->generateList(),
            'sub_defect' => $this->subDefectModel->getListByDefect($getData->defect),
            'posisi' => $this->posisiModel->generateList(),
            'problem_category' => $this->problemModel->generateList(),
            'supplier' => $this->supplierModel->generateList(),
            'leader' => $this->leaderModel->generateList(),
            'data' => $getData,
            'footer' => [
                '<script src="' . base_url() . 'js/Transaction/Identification/edit.js' . '"></script>',
            ],
        ];

        return view('Transaction/Identification/show', $data);
    }
}
