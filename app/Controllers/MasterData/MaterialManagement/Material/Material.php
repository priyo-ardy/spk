<?php

namespace App\Controllers\MasterData\MaterialManagement\Material;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\MaterialManagement\Material\MaterialModel;

class Material extends BaseController
{
    protected $module;
    protected $materialModel;

    public function __construct()
    {
        $this->module = "Material";
        $this->materialModel = new MaterialModel();
    }

    public function index()
    {
        //
    }

    public function getMaterialData(){
        $aksi = "get material";
        if($this->request->getMethod() !== 'POST'){
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

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

            $id_material = $json_data['token'];

            $get_material = $this->materialModel->getDataById($id_material);
            if(!$get_material){
                log_action($this->module, $aksi, "error", current_url(), "Material data not found");

                return pesan(ResponseInterface::HTTP_NOT_FOUND, "Material data not found");
            }

            $data = [
                'name' => $get_material->name,
                'model' => '',
                'code' => $get_material->code
            ];

            return pesan(ResponseInterface::HTTP_OK, "Material data found", $data);
        } catch (\Exception $e){
            log_action($this->module, $aksi, 'error', current_url(), "Unexpected error occured", '', json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]));

            return pesan(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, "Unexpected error occured " . $e->getMessage());
        }
    }
}
