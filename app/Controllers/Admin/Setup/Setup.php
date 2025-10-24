<?php

namespace App\Controllers\Admin\Setup;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Setup extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Application Setup',
            'footer' => []
        ];

        return view('Admin/Setup/index', $data);
    }
}
