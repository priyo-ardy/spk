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
use CodeIgniter\HTTP\Response;
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

    function getData()
    {
        $aksi = "get data planner";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        $json_data = $this->request->getJSON('true');

        if (!is_array($json_data)) {
            log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
        }

        if (!isset($json_data['token'])) {
            log_action($this->module, $aksi, "error", current_url(), "SPK token is missing in JSON input");
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
        }

        $token = $json_data['token'];
        $id_spk = dekripsi($token);

        $get_data = $this->spkModel->getPlannerSpk($id_spk);

        if (!$get_data) {
            log_action($this->module, $aksi, "error", current_url(), "SPK data not found");
            return pesan(ResponseInterface::HTTP_NOT_FOUND, "SPK data not found");
        }

        return pesan(ResponseInterface::HTTP_OK, "Success", $get_data);
    }
}
