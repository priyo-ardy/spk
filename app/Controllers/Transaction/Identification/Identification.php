<?php

namespace App\Controllers\Transaction\Identification;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Transaction\Identification\IdentificationModel;
use App\Models\Transaction\SPK\SPK\SPKModel;
use CodeIgniter\HTTP\Response;
use Config\Database;
use Config\Services;

class Identification extends BaseController
{
    protected $module;
    protected $identifikasiModel;
    protected $spkModel;
    protected $db;

    public function __construct()
    {
        $this->module = "Identification";
        $this->identifikasiModel = new IdentificationModel();
        $this->spkModel = new SPKModel();
        $this->db = Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => "SPK Identification List",
            'footer' => []
        ];

        return view('Transaction/Identification/index', $data);
    }

    function generateFromSpk()
    {
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, "generate from spk", "error", current_url(), "Request method not allowed");

            if ($this->request->isAJAX()) {
                return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
            } else {
                redirect()->back()->with(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request Not Allowed");
            }
        }

        try {
            $spk = $this->request->getPost('spk');
            if (empty($spk)) {
                throw new \Exception("No SPK data was submitted");
                return;
            }

            $id_spk = array_map(fn($value) => dekripsi($value), $spk);

            $data = [];
            $qualified_document = [];
            $unqualified_document = [];
            $data_token = [];

            // Cek apakah SPK sudah di approve, bukan draft, bukan hold, bukan reject dan bukan close
            $status_spk = $this->spkModel->cekDokumenStatus($id_spk);
            if (!$status_spk) {
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "SPK data not found");
            }

            foreach ($status_spk as $row) {
                if ($row->dokumen_status     == '2') {
                    if ($row->identifikasi == '1') {
                        $qualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> already generated identification data, failed to generate identification data",
                        ];
                    } else {
                        $qualified_document[] = $row->id;
                    }
                } else {
                    if ($row->dokumen_status == 0) {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">created</span>, failed to generate identification data",
                        ];
                    } elseif ($row->dokumen_status == '1') {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">submitted</span>, failed to generate identification data",
                        ];
                    } else if ($row->dokumen_status == '6') {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">hold</span>, failed to generate identification data",
                        ];
                    } else if ($row->dokumen_status == '7') {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">rejected</span>, failed to generate identification data",
                        ];
                    } else if ($row->dokumen_status == '8') {
                        $unqualified_document[] = [
                            'message' => "SPK document <strong>$row->code</strong> status is <span class=\"badge bg-secondary\">closed</span>, failed to generate identification data",
                        ];
                    }
                }
            }

            // Kembalikan error jika terdapat dokummen yang statusnya bukan approved
            if (count($unqualified_document) > 0) {
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Failed to generate identification data", $unqualified_document);
            }

            // Generate data
            for ($i = 0; $i < count($spk); $i++) {
                $id_spk = dekripsi($spk[$i]);
                $id = generate_uuid();

                $data[] = [
                    'id' => $id,
                    'id_spk' => $id_spk,
                    'tanggal' => date('Y-m-d'),
                    'created_by' => session('user_name')
                ];

                $data_token[] = [
                    'token' => enkripsi($id)
                ];
            }

            $this->db->transStart();

            $update_spk = $this->spkModel->updateIdentifikasi($qualified_document); //Update field identifikasi set to 1
            $insert = $this->identifikasiModel->insertBatch($data); //Insert identifikasi data

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();

                log_action($this->module, "generate from spk", "error", current_url(), "Transaction failed", '', json_encode($this->spkModel->errors()));

                throw new \Exception("Failed to generate identification document data");
            }

            $data_update = [];

            log_action($this->module, "generate from spk", "info", current_url(), "Success generate identification document data");

            return pesan(ResponseInterface::HTTP_OK, "Success generate identification document data", $data_token);
        } catch (\Exception $e) {
            log_action($this->module, "generate from spk", "error", current_url(), $e->getMessage());
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    function showData($token)
    {
        echo dekripsi($token);
    }
}
