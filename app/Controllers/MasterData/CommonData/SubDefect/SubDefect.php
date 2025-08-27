<?php

namespace App\Controllers\MasterData\CommonData\SubDefect;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\SubDefect\SubDefectModel;
use App\Models\MasterData\CommonData\Defect\DefectModel;
use App\Models\Master\MasterModel;
use App\Models\DataTable\DataTableModel;
use Config\Services;
use Config\Database;

class SubDefect extends BaseController
{
    protected $module;
    protected $subDefectModel;
    protected $defectModel;
    protected $masterModel;
    protected $dataTable;
    protected $validasi;
    protected $enkripsi;
    protected $db;

    public function __construct()
    {
        $this->module = "Sub Defect";
        $this->defectModel = new DefectModel();
        $this->subDefectModel = new SubDefectModel();
        $this->masterModel = new MasterModel();
        $this->db = Database::connect();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
    }

    public function index()
    {
        //
    }

    function getSubDefectByDefect(){
        $aksi = "Get Sub Defect List";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        $this->db->transStart();

        try{
            $json_data = $this->request->getJSON(true);
            if(!is_array($json_data)){
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Input is not a valid JSON object");
            }

            if(!isset($json_data['token'])){
                log_action($this->module, $aksi, "error", current_url(), "Token data is missing in JSON input");
                throw new \Exception("Token data is missing in JSON input");
                return pesan(ResponseInterface::HTTP_BAD_REQUEST, "Token data is missing in JSON input");
            }

            $defect = $json_data['token'];
            $generate_list = $this->subDefectModel->getListByDefect($defect);
            if(!$generate_list){
                log_action($this->module, $aksi, "error", current_url(), "Sub defect data is not available");
                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Sub defect data is not found");
            }

            $data = [];
            foreach($generate_list as $item){
                $data[] = [
                    'token' => $item->id,
                    'name' => $item->name
                ];
            }

            return pesan(ResponseInterface::HTTP_OK, "Sub defect data found", $data);
        } catch(\Exception $e){
            log_action($this->module, $aksi, "error", current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }
}
