<?php

namespace App\Controllers\Transaction\Identification;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Identification extends BaseController
{
    protected $module;

    public function __construct()
    {
        $this->module = "Identification";
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
        }

        try {
            $spk = $this->request->getPost('spk');
            if (empty($spk)) {
                throw new \Exception("No SPK data was submitted");
                return;
            }

            for ($i = 0; $i < count($spk); $i++) {
                $id_spk = dekripsi($spk[$i]);

                echo $id_spk . "<br>";
            }
        } catch (\Exception $e) {
            log_action($this->module, "generate from spk", "error", current_url(), $e->getMessage());
            return pesan(ResponseInterface::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
