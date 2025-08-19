<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => "Dashboars",
            'footer' => [
                '<script src="' . base_url() . 'js/Dashboard/dashboard.js' . '"></script>'
            ]
        ];

        return view('Dashboard/index', $data);
    }
}
