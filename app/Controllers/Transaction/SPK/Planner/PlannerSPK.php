<?php

namespace App\Controllers\Transaction\SPK\Planner;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\SPK\SPKModel;
use App\Models\Transaction\SPK\SPK\SPKDetailsModel;
use App\Models\Transaction\SPK\Mold\MoldSpkModel;
use App\Models\Transaction\SPK\Mold\SpkMoldDetails;
use App\Models\Transaction\Spk\Planner\PlannerModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Services;
use Config\Database;

class PlannerSPK extends BaseController
{
    protected $module;
    protected $spkModel;
    protected $spkDetailsModel;
    protected $moldModel;
    protected $moldDetailModel;
    protected $plannerModel;
    protected $masterModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;

    public function __construct()
    {
        $this->module = "SPK Planner";
        $this->spkModel = new SPKModel();
        $this->spkDetailsModel = new SPKDetailsModel();
        $this->moldModel = new MoldSpkModel();
        $this->moldDetailModel = new SpkMoldDetails();
        $this->plannerModel = new PlannerModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'vw_t_spk_planer';
        $column_order = [];
        $column_search = [];
        $order = array('tgl_lapor' => 'ASC');

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $column_search, $order);
    }

    function loadTable()
    {
        $aksi = "Load table";
        log_action($this->module, $aksi, "info", current_url(), "Load table spk planner");

        $lists = $this->dataTable->get_datatables();
        $data = [];

        foreach ($lists as $item) {
            $row = [];

            $row[] = $item->code;
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
        log_action($this->module, $aksi, 'info', current_url(), "Opening spk planner page");

        $data = [
            'title' => 'List of SPK',
            'footer' => [
                '<script src="' . base_url() . 'js/Transaction/SPK/Planner/planner.js' . '"></script>'
            ]
        ];

        return view('Transaction/SPK/Planner/index', $data);
    }
}
