<?php

namespace App\Controllers\Transaction\SPK\Mold;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\Mold\MoldSpkModel;
use App\Models\Transaction\SPK\SPK\SPKModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use CodeIgniter\HTTP\Response;
use Config\Services;

class MoldSpk extends BaseController
{
    protected $module;
    protected $moldModel;
    protected $masterModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;

    public function __construct()
    {
        $this->module = "Mold Confirmation";
        $this->moldModel = new SPKModel();
        $this->masterModel = new MasterModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();

        $table = 'vw_t_spk_mold';
        $column_order = [];
        $columnt_search = [];
        $order = ['tgl_lapor' => 'asc'];

        $this->dataTable = new DataTableModel(Services::request(), $table, $column_order, $columnt_search, $order);
    }

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
            $row[] = date("d/M/Y", strtotime($item->tgl_lapor));
            $row[] = $item->nama_lokasi;
            $row[] = $item->nama_dept;
            $row[] = "$item->NIK - $item->nama_karyawan";
            $row[] = ($item->kategori == '1') ? $item->kode_material : $item->nomor_mesin;
            $row[] = $item->nama_material;
            $row[] = $item->model_material;
            $row[] = $item->nomor_mesin;
            $row[] = $item->nama_alasan_repair;
            $row[] = strip_tags($item->deskripsi);
            $row[] = '
                <a href="' . base_url() . 'spk/image/' . enkripsi($item->id) . '" class="link-underline-opacity-100-hover fw-bolder" onlick="loading()">Show Image</a>
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

            $get_data = $this->moldModel->where('id', $id_spk)->first();
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

    function konfirmSelesai()
    {
        $aksi = "Confirm spk selesai";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
        }

        try {
            $token = trim($this->request->getPost('token'));
            $tgl_selesai = trim($this->request->getPost('tgl_selesai'));
            $keterangan = trim($this->request->getPost('keterangan'));

            $rules = [
                'token',
                'tgl_selesai',
                'keterangan'
            ];
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
