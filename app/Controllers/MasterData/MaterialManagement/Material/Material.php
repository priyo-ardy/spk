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

    public function generateMaterialList()
    {
        $aksi = "Generate Material";
        try {
            $json_data = $this->request->getJSON(true);
            if (!is_array($json_data)) {
                throw new \Exception("Input is not a valid JSON object");
            }

            if (!isset($json_data['kategori'])) {
                throw new \Exception("Document type is not available in the JSON input");
            }

            $data = [];

            $doc_type = $json_data['kategori'];
            switch ($doc_type) {
                case 1:
                    $lists = $this->materialModel->generatePartList();
                    break;
                case 2:
                    break;
                case 3:
                    break;
                case 4:
                    break;
            }

            foreach ($lists as $item) {
                $data[] = [
                    'id' => $item->id,
                    'code' => $item->code,
                    'name' => $item->name,
                    'model' => ''
                ];
            }

            return pesan(ResponseInterface::HTTP_OK, 'Material List Found', $data);
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

    public function getMaterialData()
    {
        $aksi = "get material";
        if ($this->request->getMethod() !== 'POST') {
            log_action($this->module, $aksi, "error", current_url(), "Request method not allowed");

            return pesan(ResponseInterface::HTTP_METHOD_NOT_ALLOWED, "Request not allowed");
        }

        try {
            $json_data = $this->request->getJSON(true);

            if (!is_array($json_data)) {
                log_action($this->module, $aksi, "error", current_url(), "Input is not a valid JSON object");
                throw new \Exception("Input is not a valid JSON object");
            }

            if (!isset($json_data['kategori'])) {;
                log_action($this->module, $aksi, "error", current_url(), "Document type is missing from JSON input");
                throw new \Exception("Document type is missing from JSON input");
            }

            if (!isset($json_data['token'])) {
                log_action($this->module, $aksi, "error", current_url(), "Material token is missing in JSON input");
                throw new \Exception("Material token is missing in JSON input");
            }

            $kategori = $json_data['kategori'];
            $id_material = $json_data['token'];

            switch ($kategori) {
                case 1:
                    $get_material = $this->materialModel->getPartData($id_material);
                    break;
                case 2:
                    break;
                case 3:
                    break;
                case 4:
                    break;
            }

            $data = [
                'name' => $get_material->name,
                'model' => '',
                'code' => $get_material->code
            ];

            return pesan(ResponseInterface::HTTP_OK, "Material data found", $data);
        } catch (\Exception $e) {
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
