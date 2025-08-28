<?php

namespace App\Controllers\MasterData\CommonData\Defect;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterData\CommonData\Defect\DefectModel;
use Config\Services;

class Defect extends BaseController
{
    protected $defectModel;
    protected $module;
    protected $validasi;
    protected $enkripsi;

    public function __construct()
    {
        $this->module = "Defect";
        $this->defectModel = new DefectModel();
        $this->validasi = Services::validation();
        $this->enkripsi = Services::encrypter();
    }

    public function index()
    {
        //
    }
}
