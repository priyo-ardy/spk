<?php

namespace App\Controllers\Transaction\Verification;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Verification extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'SPK Verification List',
            'footer' => []
        ];

        return view('Transaction/Verification/index', $data);
    }
}
