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
    protected $db;

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
        $this->db = Database::connect();

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
            log_action($this->module, $aksi, "error", current_url(), "SPK data already confirmed by planner");
            return pesan(ResponseInterface::HTTP_NOT_FOUND, "SPK data already confirmed by planner");
        }

        return pesan(ResponseInterface::HTTP_OK, "Success", $get_data);
    }

    function konfirmSelesai()
    {
        $aksi = 'konfirmasi';
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            log_message('error', 'Konfirmasi SPK Error: Request method not allowed');
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();

        try {
            $token = trim(sanitize_filename($this->request->getPost('konfirmasi_token')));
            $tgl_lapor = trim(sanitize_filename($this->request->getPost('tgl_lapor')));
            $plan_finish_date = trim(sanitize_filename($this->request->getPost('plan_finish_date')));
            $required_finish_date = trim(sanitize_filename($this->request->getPost('required_finish_date')));
            $prioritas = trim(sanitize_filename($this->request->getPost('prioritas')));
            $reason = trim(sanitize_filename($this->request->getPost('reason')));
            $id_spk = dekripsi($token);

            $rules = [
                'konfirmasi_token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'SPK token is required'
                    ]
                ],
                'tgl_lapor' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => 'Reported date is required',
                        'valid_date' => 'Reported date must be a valid date'
                    ]
                ],
                'plan_finish_date' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => 'Plan finish date is required',
                        'valid_date' => 'Plan finish date must be a valid date'
                    ]
                ],
                'required_finish_date' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => 'Required finish date is required',
                        'valid_date' => 'Required finish date must be a valid date'
                    ]
                ],
                'prioritas' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Priority is required'
                    ]
                ],
            ];

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());
                log_action($this->module, $aksi, "error", current_url(), $error_message);
                log_message('error', 'Konfirmasi SPK Error: ' . $error_message);
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, $error_message);
            }

            if ($required_finish_date < $tgl_lapor) {
                log_action($this->module, $aksi, "error", current_url(), "Required finish date must be greater than plan finish date");
                log_message('error', 'Konfirmasi SPK Error: Required finish date must be greater than plan finish date');
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Required finish date must be greater than or equal to plan finish date");
            }

            // data t_spk
            $data = [
                'planner_confirm' => date("Y-m-d H:i:s"),
                'prioritas' => $prioritas,
                'flow_status' => '2'
            ];

            // Update t_spk
            $update = $this->spkModel->update($id_spk, $data);
            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Failed to update SPK data");
                log_message('error', 'Konfirmasi SPK Error: Failed to update SPK data');
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to update SPK data");
            }

            // Data t_spk_mold
            $data_mold = [
                'required_finish' => $required_finish_date,
                'planner_comment' => $reason,
            ];

            $update_mold = $this->moldModel->updatePlannerConfirm($id_spk, $data_mold);
            if (!$update_mold) {
                log_action($this->module, $aksi, "error", current_url(), "Failed to update SPK mold data");
                log_message('error', 'Konfirmasi SPK Error: Failed to update SPK mold data');
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to update SPK mold data");
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Failed to update SPK data");
                log_message('error', 'Konfirmasi SPK Error: Failed to update SPK data');
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to update SPK data");
            }

            return pesan(ResponseInterface::HTTP_OK, "SPK data has been confirmed by planner");
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), "Error: " . $e->getMessage());
            log_message('error', 'Konfirmasi SPK Error: ' . $e->getMessage());
            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Error: " . $e->getMessage());
        }
    }
}
