<?php

namespace App\Controllers\Transaction\Identification;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Identification extends BaseController
{
    public function index()
    {
        $data = [
            'title' => "SPK Identification List",
            'footer' => []
        ];

        return view('Transaction/Identification/index', $data);
    }
}
