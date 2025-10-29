<?php

namespace App\Controllers\Transaction\SPK\Quality;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\SPK\SPK\SPKModel;
use App\Models\Transaction\Spk\Mold\MoldSpkModel;
use Config\Services;
use Config\Database;

class QualitySPK extends BaseController
{
    protected $module;
    protected $spkModel;
    protected $moldModel;
    protected $validasi;
    protected $db;

    public function __construct()
    {
        $this->module = "Quality Confirmation";
        $this->spkModel = new SPKModel();
        $this->moldModel = new MoldSpkModel();
        $this->validasi = Services::validation();
        $this->db = Database::connect();
    }

    public function index()
    {
        //
    }

    function getQualityData()
    {
        $aksi = 'Get quality data';

        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, 'error', current_url(), 'Request method not allowed');
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, 'Request Not Allowed');
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

            if ($get_data->flow_status !== '4') {
                log_action($this->module, $aksi, "error", current_url(), "SPK not finihed yet");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK not finihed yet");
            }

            $get_mold = $this->moldModel->where('id_spk', $id_spk)->first();
            if (empty($get_mold)) {
                log_action($this->module, $aksi, "error", current_url(), "SPK mold is not found");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "SPK mold is not found");
            }
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

    function submitData()
    {
        $aksi = 'Submit quality data';

        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, 'error', current_url(), 'Request method not allowed');
            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, 'Request Not Allowed');
        }

        $this->db->transStart();

        try {
            $token = trim($this->request->getPost('qa_token'));
            $appearance = trim($this->request->getPost('appearance'));
            $dimension = trim($this->request->getPost('dimension'));
            $performance = trim($this->request->getPost('performance'));
            $id_spk = dekripsi($token);

            $rules = [
                'qa_token' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'SPK token is required'
                    ]
                ],
                'appearance' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Appearance is required'
                    ]
                ],
                'dimension' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Dimension is required'
                    ]
                ],
                'performance' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Performance is required'
                    ]
                ],
            ];

            $this->validasi->setRules($rules);
            if (!$this->validasi->withRequest($this->request)->run()) {
                $error_message = implode('<br>', $this->validasi->getErrors());
                log_action($this->module, $aksi, 'error', current_url(), 'Validation failed', '', json_encode([
                    'message' => $error_message
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, $error_message);
            }

            $data = [
                'quality_confirm' => date('Y-m-d H:i:s'),
                'dokumen_status' => '8',
                'flow_status' => '7',
                'appearance' => $appearance,
                'dimension' => $dimension,
                'performance' => $performance,
            ];

            $update = $this->spkModel->update($id_spk, $data);

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_action($this->module, $aksi, "error", current_url(), "Update SPK is failed", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Update SPK is failed");
            }

            if (!$update) {
                log_action($this->module, $aksi, "error", current_url(), "Update SPK is failed", '', json_encode([
                    'data' => $this->spkModel->errors()
                ]));

                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Update SPK is failed");
            }

            // $this->db->transCommit();
            log_action($this->module, $aksi, "success", current_url(), "Update SPK is success");
        } catch (\Exception $e) {
            $this->db->transRollback();
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
