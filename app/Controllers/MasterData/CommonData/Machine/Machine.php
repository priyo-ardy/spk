<?php

namespace App\Controllers\MasterData\CommonData\Machine;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\Machine\MachineModel;
use App\Models\MasterData\CommonData\Workshop\WorkshopModel;
use App\Models\MasterData\CommonData\Tonnage\TonnageModel;
use Config\Services;

class Machine extends BaseController
{
    protected $mesinModel;
    protected $workshopModel;
    protected $tonageModel;
    protected $module;
    protected $validasi;
    protected $enkripsi;

    public function __construct()
    {
        $this->module = "Machine Management";
        $this->mesinModel = new MachineModel();
        $this->workshopModel = new WorkshopModel();
        $this->tonageModel = new TonnageModel();
    }


    public function index()
    {
        $data = [
            'title' => "Machine Management",
            'footer' => [
                '<script src="'.base_url().'js/MasterData/CommonData/Machine/machine.js'.'"></script>'
            ]
        ];

        return view('MasterData/CommonData/Machine/index', $data);
    }

    function getDataById(){
        $aksi = "get";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, "get", "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try{
            $json_data = $this->request->getJSON(true);
            if(!is_array($json_data)){
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if(!isset($json_data['token'])){
                log_action($this->module, $aksi, "error", current_url(), "Job data ID is missing in JSON input");
                throw new \Exception("Job data ID is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Job data ID is missing in JSON input");
            }

            $id_mesin = $json_data['token'];

            $get = $this->mesinModel->where('id', $id_mesin)->first();
            if(!$get){
                log_action($this->module, $aksi, "error", current_url(), 'Machine data not found');

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Machine data not found");
            }

            $data = [
                'id' => $get->id,
                'code' => $get->code,
                'workshop' => $get->workshop,
                'nomor_mesin' => $get->nomor_mesin,
                'name' => $get->name,
                'specification' => $get->spesifikasi,
                'tonnage' => $get->tonnage,
                'brand' => $get->brand,
                'serial_no' => $get->serial_no,
                'rate' => $get->rate,
                'mfg_date' => $get->mfg_date,
                'purchase_date' => $get->purchase_date,
                'remark' => $get->remark
            ];

            return pesan(ResponseInterface::HTTP_OK, "Machine data found", $data);
        } catch(\Exception $e){
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occurred", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }
}
