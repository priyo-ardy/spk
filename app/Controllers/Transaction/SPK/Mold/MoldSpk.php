<?php

namespace App\Controllers\Transaction\SPK\Mold;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\Mold\MoldSpkModel;
use App\Models\Transaction\SPK\SPK\SPKModel;
use App\Models\Transaction\SPK\SPK\SPKDetailsModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use CodeIgniter\HTTP\Response;
use Config\Services;

class MoldSpk extends BaseController
{
    protected $module;
    protected $spkModel;
    protected $moldModel;
    protected $detailsModel;
    protected $masterModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->module = "Mold Confirmation";
        $this->moldModel = new MoldSpkModel();
        $this->masterModel = new MasterModel();
        $this->spkModel = new SPKModel();
        $this->detailsModel = new SPKDetailsModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'vw_t_spk_mold';
        $column_order = [];
        $columnt_search = [];
        $order = ['tgl_lapor' => 'asc'];

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $columnt_search, $order);
    }

    /**
     * Generate table of Mold Confirmation list
     * 
     * @return ResponseInterface
     */
    function loadTable()
    {
        $aksi = "Load Table";
        $lists = $this->dataTable->get_datatables();
        $data = [];

        foreach ($lists as $item) {
            $row = [];

            $row[] = '
                <a href="#" onclick="confirmSelesai(`' . enkripsi($item->id) . '`)" class="link-underline-opacity-100-hover fw-bolder" onlick="loading()">' . $item->code . '</a>
            ';
            $row[] = ($item->status_dokumen == '2') ? '<button type="button" class="btn btn-sm btn-primary d-block rounded-0">' . $item->status . '</button>' : $item->status;
            $row[] = date("d/M/Y", strtotime($item->tgl_lapor));
            $row[] = ($item->plan_selesai == null) ? "-" : date("d/M/Y", strtotime($item->plan_selesai));
            $row[] = ($item->aktual_selesai == null) ? "-" : date("d/M/Y", strtotime($item->aktual_selesai));
            $row[] = $item->nama_lokasi;
            $row[] = $item->nama_dept;
            $row[] = "$item->NIK - $item->nama_pelapor";
            $row[] = $item->nomor_mesin;
            $row[] = $item->nama_material;
            $row[] = $item->model_material;
            $row[] = $item->nomor_mesin;
            $row[] = $item->nama_alasan_repair;
            $row[] = strip_tags($item->deskripsi);
            $row[] = '
                <a href="#" onclick="lihatGambar(`' . enkripsi($item->id) . '`)" class="link-underline-opacity-100-hover fw-bolder">Show Image</a>
            ';
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


    /**
     * Open list of SPK page
     * 
     * @return \Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $aksi = "Open";
        log_action($this->module, $aksi, "info", current_url(), "Opening list of SPK page");

        $data = [
            'title' => 'List of SPK',
            'footer' => [
                '<script src="' . base_url() . 'js/Transaction/SPK/Mold/mold.js' . '"></script>'
            ]
        ];

        return view('Transaction/SPK/Mold/index', $data);
    }



    /**
     * Get SPK details
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \Exception
     */
    function getSpkData()
    {
        $aksi = "Get spk details";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        try {
            $json_data = $this->request->getJson('true');

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if ($json_data['token'] == null) {
                log_action($this->module, $aksi, "error", current_url(), "SPK token is missing in JSON input");
                throw new \Exception("SPK No. is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
            }

            $token = $json_data['token'];
            $id_spk = dekripsi($token);

            $get_data = $this->spkModel->where('id', $id_spk)->first();
            if (!$get_data) {
                log_action($this->module, $aksi, "error", current_url(), "SPK not found");
                throw new \Exception("SPK not found");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK not found");
            }

            $data = [
                'code' => $get_data->code,
                'tgl_lapor' => $get_data->tgl_lapor,
            ];

            return pesan(ResponseInterface::HTTP_OK, "Get spk details successfully", $data);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    /**
     * Show SPK image
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \Exception
     */
    function showImage()
    {
        $aksi = "Show image";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        try {
            $json_encode = $this->request->getJson('true');

            if (!is_array($json_encode)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if ($json_encode['token'] == null) {
                log_action($this->module, $aksi, "error", current_url(), "SPK token is missing in JSON input");
                throw new \Exception("SPK No. is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
            }

            $token = $json_encode['token'];
            $id_spk = dekripsi($token);

            $header = $this->moldModel->where('id', $id_spk)->first();
            $get_image = $this->detailsModel->where('id_spk', $id_spk)->findAll();
            if (!$get_image) {
                log_action($this->module, $aksi, "error", current_url(), "SPK not found");
                throw new \Exception("SPK not found");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK not found");
            }


            $data = [];
            foreach ($get_image as $item) {
                $uploadPath = base_url() . 'uploads/spk/' . $item->nama_file;

                $data[] = [
                    'file_name' => $uploadPath,
                    'title' => $item->nama_file
                ];
            }

            $output = [
                'code' => $header->code,
                'image' => $data
            ];

            return pesan(ResponseInterface::HTTP_OK, "Image Found", $output);
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    /**
     * Confirm SPK selesai
     *
     * @param string $token SPK token
     * @param string $tgl_lapor Tanggal laporan
     * @param string $tgl_selesai Tanggal rencana selesai
     * @param string $keterangan Keterangan
     *
     * @return object ResponseInterface
     */
    function konfirmSelesai()
    {
        $aksi = "Confirm spk selesai";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        try {
            $token = trim($this->request->getPost('konfirmasi_token'));
            $id_spk = dekripsi($token);
            $tgl_lapor = trim($this->request->getPost('tgl_lapor'));
            $tgl_selesai = trim($this->request->getPost('plan_finish_date'));
            $keterangan = trim($this->request->getPost('keterangan'));

            $rules = [
                'token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'SPK token is requried'
                    ]
                ],
                'tgl_lapor' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => 'Reported date is required',
                        'valid_date' => 'Reported date must have a valid date format'
                    ]
                ],
                'plan_finish_date' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => 'Plan finish date is required',
                        'valid_date' => 'Plan finish date must have a valid date format'
                    ]
                ]
            ];

            $this->validasi->setRules($rules);

            if (!$this->validasi->withRequest($this->request)->run() == false) {
                $err_message = implode("<br>", $this->validasi->getErrors());
                log_action($this->module, $aksi, "error", current_url(), $err_message);
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, $err_message);
            }

            if ($tgl_selesai < $tgl_lapor) {
                log_action($this->module, $aksi, "error", current_url(), "Plan finish date must be greater than reported date");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Plan finish date must be greater than reported date");
            }

            $checkData = $this->moldModel->where('id_spk', $id_spk)->first();
            if ($checkData) {
                log_message('error', 'Confirmation error: SPK already confirmed');
                log_action($this->module, $aksi, "error", current_url(), "SPK already confirmed", '', json_encode([
                    'data' => $id_spk
                ]));
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK already confirmed");
            }

            $data = [
                'id' => generate_uuid(),
                'id_spk' => $id_spk,
                'tgl_lapor' => $tgl_lapor,
                'plan_selesai' => $tgl_selesai,
                'status_dokumen' => '1',
                'keterangan' => $keterangan,
                'created_by' => $this->NIK
            ];

            $insert = $this->moldModel->insert($data);
            if ($insert) {
                $this->spkModel->update($id_spk, ['flow_status ' => '1']);
                log_message('info', 'Confirmation success');
                log_action($this->module, $aksi, "success", current_url(), "Confirmation success    ");
                return pesan(ResponseInterface::HTTP_OK, "Confirmation success");
            }
        } catch (\Exception $e) {
            log_action($this->module, $aksi, "error", current_url(), $e->getMessage(), '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
