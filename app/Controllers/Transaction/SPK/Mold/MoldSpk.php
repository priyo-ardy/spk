<?php

namespace App\Controllers\Transaction\SPK\Mold;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\Mold\MoldSpkModel;
use App\Models\Transaction\SPK\Mold\SpkMoldDetails;
use App\Models\Transaction\SPK\SPK\SPKModel;
use App\Models\Transaction\SPK\SPK\SPKDetailsModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use CodeIgniter\HTTP\Response;
use Config\Services;
use Config\Database;

class MoldSpk extends BaseController
{
    protected $module;
    protected $spkModel;
    protected $moldModel;
    protected $moldDetail;
    protected $detailsModel;
    protected $masterModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;
    protected $db;


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->module = "Mold Confirmation";
        $this->moldModel = new MoldSpkModel();
        $this->moldDetail = new SpkMoldDetails();
        $this->masterModel = new MasterModel();
        $this->spkModel = new SPKModel();
        $this->detailsModel = new SPKDetailsModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
        $this->db = Database::connect();

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

            $get_data = $this->spkModel->where('id', $id_spk)->where('kategori', '1')->where('dokumen_status', '2')->first();
            if (!$get_data) {
                log_action($this->module, $aksi, "error", current_url(), "SPK not found");
                throw new \Exception("SPK not found");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK not found or SPK not approved yet");
            }

            switch ($get_data->flow_status) {
                case '1':
                    return pesan(ResponseInterface::HTTP_FOUND, "SPK already confirmed by mold engineer");
                    break;
                case '2':
                    return pesan(ResponseInterface::HTTP_FOUND, "SPK already confirm by planner");
                    break;
                case '3':
                    return pesan(ResponseInterface::HTTP_FOUND, "SPK already confirm by ME");
                    break;
                case '4':
                    return pesan(ResponseInterface::HTTP_FOUND, "SPK already finish by mold engineer");
                    break;
                case '5':
                    return pesan(ResponseInterface::HTTP_FOUND, "SPK already finish by ME");
                    break;
                case '6':
                    return pesan(ResponseInterface::HTTP_FOUND, "SPK already confirm by quality");
                    break;
                case '7':
                    return pesan(ResponseInterface::HTTP_FOUND, "SPK already closed");
                    break;
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
                $update_spk = $this->spkModel->update($id_spk, ['mold_confirm' => date("Y-m-d H:i:s"), 'flow_status ' => '1']);
                if (!$update_spk) {
                    log_message('error', 'Confirmation error: Update SPK flow status failed ' . $this->spkModel->errors()['message']);
                    log_action($this->module, $aksi, "error", current_url(), "Update SPK flow status failed " . $this->spkModel->errors()['message'], '', json_encode([
                        'data' => $this->spkModel->errors()
                    ]));
                    return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Update SPK flow status failed " . $this->spkModel->errors()['message']);
                }

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

    function getPlannerConfirm()
    {
        $aksi = "SPK Confimed by Planner";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        try {
            $json_data = $this->request->getJSON('true');

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Invalid JSON data");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Invalid JSON data");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "SPK token is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK token is missing in JSON input");
            }

            $token = $json_data['token'];
            $id_spk = dekripsi($token);

            $get_data = $this->spkModel->where('id', $id_spk)->where('kategori', '1')->where('dokumen_status', '2')->first();
            if (empty($get_data)) {
                log_action($this->module, $aksi, "error", current_url(), "SPK not found");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK not found");
            }

            if ($get_data->flow_status == '1') {
                log_action($this->module, $aksi, "error", current_url(), "SPK not found");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK not yet confirmed by Planner");
            }

            if ($get_data->flow_status == '4') {
                log_action($this->module, $aksi, "error", current_url(), "SPK already finish by mold engineer");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK already finish by mold engineer");
            }

            switch ($get_data->prioritas) {
                case 0:
                    $nama_prioritas = "Low";
                    break;
                case 1:
                    $nama_prioritas = "Normal";
                    break;
                case 2:
                    $nama_prioritas = "Urgent";
                    break;
                case 3:
                    $nama_prioritas = "Critical";
                    break;
            }

            $data = [
                'tgl_lapor' => $get_data->tgl_lapor,
                'plan_finish' => date("Y-m-d", strtotime($get_data->mold_confirm)),
                'required_finish' => date("Y-m-d", strtotime($get_data->planner_confirm)),
                'prioritas' => $nama_prioritas,
            ];

            return pesan(ResponseInterface::HTTP_OK, "Success", $data);
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

    function finishTransaction()
    {
        $aksi = "Finish Transaction";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        $this->db->transStart();

        try {
            $token = trim($this->request->getPost('token_selesai'));
            $actual_finish = trim($this->request->getPost('actual_finish'));
            $nama_aktifitas = $this->request->getPost('nama_aktifitas');
            $operator = $this->request->getPost('operator');
            $tanggal = $this->request->getPost('tanggal');
            $durasi = $this->request->getPost('durasi');
            $mold_remark = trim($this->request->getPost('mold_remark'));

            $rules = [
                'token_selesai' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'SPK token is required',
                    ]
                ],
                'actual_finish' => [
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => 'Actual Finish Date is required',
                        'valid_date' => 'Actual Finish Date is not valid',
                    ]
                ],
                'nama_aktifitas.*' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Activity Name is required',
                    ]
                ],
                'operator.*' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Operator is required',
                    ]
                ],
                'tanggal.*' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Date is required',
                    ]
                ],
                'durasi.*' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Duration is required',
                    ]
                ],
            ];

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                log_action($this->module, $aksi, "error", current_url(), $this->validasi->getErrors());
                $error_message = implode('<br>', $this->validasi->getErrors());
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, $error_message);
            }

            $id = dekripsi($token);
            $data_header = [
                'mold_finish' => $actual_finish,
                'flow_status' => '4', //SPK telah selesai dikerjakan oleh mold engineer
            ];

            $update_header = $this->spkModel->update($id, $data_header);
            if (!$update_header) {
                log_action($this->module, $aksi, "error", current_url(), "Failed to update header");
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to update header data");
            }

            // Getting mold spk
            $get_mold_spk = $this->moldModel->where('id_spk', $id)->first();
            if (!$get_mold_spk) {
                log_action($this->module, $aksi, "error", current_url(), "Confirmed Mold SPK not found");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Confirmed Mold SPK not found");
            }

            // Update t_spk_mold
            $data_mold = [
                'aktual_selesai' => $actual_finish,
                'keterangan_selesai' => $mold_remark
            ];

            $update_mold = $this->moldModel->updateMoldFinish($id, $data_mold);
            if (!$update_mold) {
                log_action($this->module, $aksi, "error", current_url(), "Failed to update mold finish date");
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to update mold finish date");
            }

            // Looping for mold activity
            $urut = 1;
            $data_mold_detail = [];
            for ($i = 0; $i < count($nama_aktifitas); $i++) {
                if ($nama_aktifitas[$i] === '' || $operator[$i] === '' || $tanggal[$i] === '' || $durasi[$i] === '') {
                    return pesan(ResponseInterface::HTTP_BAD_REQUEST, "All fields are required");
                }

                $data_mold_detail[] = [
                    'id' => generate_uuid(),
                    'urut' => $urut,
                    'id_header' => $get_mold_spk->id,
                    'aktifitas' => $nama_aktifitas[$i],
                    'operator' => $operator[$i],
                    'tanggal' => $tanggal[$i],
                    'durasi' => $durasi[$i],
                    'created_by' => $this->NIK
                ];
                $urut++;
            }

            $insert_mold_details = $this->moldDetail->insertBatch($data_mold_detail);
            $this->db->transComplete();

            if (!$insert_mold_details) {
                log_action($this->module, $aksi, "error", current_url(), "Failed to insert mold details");
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to insert mold details");
            }

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Failed to insert mold details");
                return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Failed to insert mold details");
            }

            return pesan(ResponseInterface::HTTP_OK, "SPK selesai berhasil dikonfirmasi");
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
