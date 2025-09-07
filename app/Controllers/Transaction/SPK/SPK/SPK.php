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
use App\Models\MasterData\CommonData\Employee\EmployeeModel;
use App\Models\MasterData\CommonData\Leader\LeaderModel;
use App\Models\MasterData\CommonData\Dept\DeptModel;
use App\Models\MasterData\MaterialManagement\Material\MaterialModel;
use App\Models\MasterData\CommonData\Defect\DefectModel;
use App\Models\MasterData\CommonData\SubDefect\SubDefectModel;
use App\Models\MasterData\CommonData\ProblemPosition\ProblemPositionModel;
use App\Models\MasterData\CommonData\RepairReason\RepairReasonModel;
use App\Models\MasterData\CommonData\Lokasi\LokasiModel;
use Config\Services;
use Config\Database;

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
    protected $validasi;
    protected $enkripsi;

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

        $this->db = Database::connect();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
    }

    public function index()
    {
        $aksi = "Open";
        log_action($this->module, $aksi, "info", current_url(), "Opening list of SPK page");

        $data = [
            'title' => 'List of SPK',
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
            'position_list' => $this->positionModel->generateList(),
            'reason_list' => $this->repairModel->generateList(),
            'footer' => [
                '<script src="' . base_url() . 'js/Transaction/SPK/SPK/add.js' . '"></script>'
            ]
        ];

        return view('Transaction/SPK/SPK/add', $data);
    }

    function saveData()
    {
        $aksi =  "save SPK";

        $this->db->transStart();
        try {
            $id = generate_uuid();
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
}
