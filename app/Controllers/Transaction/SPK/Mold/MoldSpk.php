<?php

namespace App\Controllers\Transaction\SPK\Mold;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\Mold\MoldSpkModel;
use App\Models\MasterData\MaterialManagement\Material\MaterialModel;
use App\Models\MasterData\CommonData\Dept\DeptModel;
use App\Models\MasterData\CommonData\Employee\EmployeeModel;
use App\Models\MasterData\CommonData\Leader\LeaderModel;
use App\Models\MasterData\CommonData\RepairReason\RepairReasonModel;
use App\Models\DataTable\DataTableModel;
use App\Models\Master\MasterModel;
use Config\Services;

class MoldSpk extends BaseController
{
    protected $module;
    protected $spkModel;
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
        $this->materialModel = new MaterialModel();
        $this->deptModel = new DeptModel();
        $this->karyawanModel = new EmployeeModel();
        $this->leaderModel = new LeaderModel();
        $this->masterModel = new MasterModel();
        $this->repairModel = new RepairReasonModel();
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
}
