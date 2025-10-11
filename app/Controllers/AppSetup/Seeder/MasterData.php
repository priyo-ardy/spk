<?php

namespace App\Controllers\AppSetup\Seeder;

use App\Controllers\BaseController;
use App\Controllers\MasterData\CommonData\Workshop\Workshop;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\MaterialManagement\MaterialCategory\MaterialCategoryModel;
use App\Models\MasterData\MaterialManagement\Material\MaterialModel;
use App\Models\MasterData\MaterialManagement\EquipmentType\EquipmentTypeModel;
use App\Models\MasterData\CommonData\Workshop\WorkshopModel;
use App\Models\MasterData\CommonData\Tonnage\TonnageModel;
use App\Models\MasterData\CommonData\Machine\MachineModel;
use App\Models\MasterData\CommonData\Defect\DefectModel;
use App\Models\MasterData\CommonData\SubDefect\SubDefectModel;
use App\Models\MasterData\CommonData\ProblemPosition\ProblemPositionModel;
use App\Models\MasterData\CommonData\RepairReason\RepairReasonModel;
use App\Models\MasterData\CommonData\Satuan\SatuanModel;
use App\Models\MasterData\CommonData\Dept\DeptModel;
use App\Models\MasterData\CommonData\Leader\LeaderModel;
use App\Models\MasterData\CommonData\Karyawan\KaryawanModel;
use App\Models\MasterData\CommonData\Lokasi\LokasiModel;

class MasterData extends BaseController
{
    protected $materialCategory;
    protected $materialModel;
    protected $equipmentType;
    protected $workshopModel;
    protected $tonnageModel;
    protected $machineModel;
    protected $defectModel;
    protected $subDefectModel;
    protected $porblemPositionModel;
    protected $repairReasonModel;
    protected $satuanModel;
    protected $deptModel;
    protected $leaderModel;
    protected $karyawanModel;
    protected $lokasiModel;

    public function __construct()
    {
        $this->materialCategory = new MaterialCategoryModel();
        $this->materialModel = new MaterialModel();
        $this->equipmentType = new EquipmentTypeModel();
        $this->workshopModel = new WorkshopModel();
        $this->tonnageModel = new TonnageModel();
        $this->machineModel = new MachineModel();
        $this->defectModel = new DefectModel();
        $this->subDefectModel = new SubDefectModel();
        $this->porblemPositionModel = new ProblemPositionModel();
        $this->repairReasonModel = new RepairReasonModel();
        $this->satuanModel = new SatuanModel();
        $this->deptModel = new DeptModel();
        $this->leaderModel = new LeaderModel();
        $this->karyawanModel = new KaryawanModel();
        $this->lokasiModel = new LokasiModel();
    }

    public function index()
    {
        $data = [
            'title' => "Master Data Database Seeder",
            'footer' => [
                '<script src="' . base_url() . 'js/AppSetup/Seeder/master_data.js' . '"></script>'
            ]
        ];

        return view('AppSetup/Seeder/MasterData/index.php', $data);
    }

    function generateData()
    {
        if ($this->request->getMethod() !== 'POST') {
            pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);
            if (empty($json_data)) {
                pesan(ResponseInterface::HTTP_BAD_REQUEST, "Request body is empty");
            }

            if (!isset($json_data['table_name'])) {
                pesan(ResponseInterface::HTTP_BAD_REQUEST, "Table name is empty");
            }

            $table_name = $json_data['table_name'];
            switch ($table_name) {
                case 1:
                    $lists = $this->materialCategory->orderBy('code', 'asc')->findAll();
                    break;
                case 2:
                    $lists = $this->materialModel->orderBy('code', 'asc')->findAll();
                    break;
                case 3:
                    $lists = $this->equipmentType->orderBy('code', 'asc')->findAll();
                    break;
                case 4:
                    $lists = $this->workshopModel->orderBy('code', 'asc')->findAll();
                    break;
                case 5:
                    $lists = $this->tonnageModel->orderBy('code', 'asc')->findAll();
                    break;
                case 6:
                    $lists = $this->machineModel->orderBy('code', 'asc')->findAll();
                    break;
                case 7:
                    $lists = $this->defectModel->orderBy('code', 'asc')->findAll();
                    break;
                case 8:
                    $lists = $this->subDefectModel->orderBy('code', 'asc')->findAll();
                    break;
                case 9:
                    $lists = $this->porblemPositionModel->orderBy('code', 'asc')->findAll();
                    break;
                case 10:
                    $lists = $this->repairReasonModel->orderBy('code', 'asc')->findAll();
                    break;
                case 11:
                    $lists = $this->satuanModel->orderBy('code', 'asc')->findAll();
                    break;
                case 12:
                    $lists = $this->deptModel->orderBy('code', 'asc')->findAll();
                    break;
                case 13:
                    $lists = $this->leaderModel->orderBy('NIK', 'asc')->findAll();
                    break;
                case 14:
                    $lists = $this->karyawanModel->orderBy('NIK', 'asc')->findAll();
                    break;
                case 15:
                    $lists = $this->lokasiModel->orderBy('code', 'asc')->findAll();
                    break;
                default:
                    $lists = [];
            }

            if (empty($lists)) {
                pesan(ResponseInterface::HTTP_BAD_REQUEST, "Table name not found");
            } else {
                return $this->response
                    ->setStatusCode(ResponseInterface::HTTP_OK)
                    ->setJSON([
                        'data' => $lists
                    ]);
            }
        } catch (\Exception $e) {
            pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
